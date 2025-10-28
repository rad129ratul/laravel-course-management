(function($) {
    'use strict';

    let moduleCounter = 1;
    let contentCounters = { 0: 1 };

    function initializeEventHandlers() {
        $('#addModuleBtn').on('click', addModule);
        $(document).on('click', '.add-content', addContent);
        $(document).on('click', '.delete-module', deleteModule);
        $(document).on('click', '.delete-content', deleteContent);
        $(document).on('change', '.video-source-type', handleVideoSourceChange);
        $('#courseForm').on('submit', validateForm);
        
        updateModuleDeleteButtons();
    }

    function addModule() {
        const template = $('.module-container').first().clone();
        const newIndex = moduleCounter;
        
        template.attr('data-module-index', newIndex);
        template.find('.module-title').text(`Module ${newIndex + 1}`);
        template.find('.delete-module').show();
        
        template.find('input, select, textarea').each(function() {
            const name = $(this).attr('name');
            if (name) {
                $(this).attr('name', name.replace(/modules\[\d+\]/, `modules[${newIndex}]`));
                $(this).val('');
            }
        });
        
        template.find('.contents-container').attr('data-module-index', newIndex).empty();
        
        const firstContent = createContentElement(newIndex, 0);
        template.find('.contents-container').append(firstContent);
        
        $('#modules-container').append(template);
        
        contentCounters[newIndex] = 1;
        moduleCounter++;
        
        updateModuleDeleteButtons();
    }

    function addContent(e) {
        e.preventDefault();
        const moduleContainer = $(this).closest('.module-container');
        const moduleIndex = parseInt(moduleContainer.attr('data-module-index'));
        const contentIndex = contentCounters[moduleIndex] || 0;
        
        const newContent = createContentElement(moduleIndex, contentIndex);
        moduleContainer.find('.contents-container').append(newContent);
        
        contentCounters[moduleIndex] = contentIndex + 1;
    }

    function createContentElement(moduleIndex, contentIndex) {
        const template = `
            <div class="content-container" data-content-index="${contentIndex}">
                <button type="button" class="btn btn-danger btn-sm delete-btn delete-content">
                    <i class="fas fa-times"></i>
                </button>
                <div class="mb-3">
                    <label class="form-label">Content Title</label>
                    <input type="text" class="form-control" name="modules[${moduleIndex}][contents][${contentIndex}][title]">
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Video Source Type</label>
                        <select class="form-select video-source-type" name="modules[${moduleIndex}][contents][${contentIndex}][video_source_type]">
                            <option value="">Select Source</option>
                            <option value="youtube">YouTube</option>
                            <option value="vimeo">Vimeo</option>
                            <option value="upload">Upload Video</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Video Length</label>
                        <input type="text" class="form-control" name="modules[${moduleIndex}][contents][${contentIndex}][video_length]" placeholder="e.g., 10:30">
                    </div>
                </div>
                <div class="mb-3 video-url-container">
                    <label class="form-label">Video URL</label>
                    <input type="url" class="form-control" name="modules[${moduleIndex}][contents][${contentIndex}][video_url]" placeholder="https://youtube.com/watch?v=...">
                </div>
                <div class="mb-3 video-upload-container" style="display:none;">
                    <label class="form-label">Upload Video</label>
                    <input type="file" class="form-control" name="modules[${moduleIndex}][contents][${contentIndex}][video_file]" accept="video/*">
                </div>
                <div class="mb-3">
                    <label class="form-label">Content Text</label>
                    <textarea class="form-control" name="modules[${moduleIndex}][contents][${contentIndex}][content_text]" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Image (Optional)</label>
                    <input type="file" class="form-control" name="modules[${moduleIndex}][contents][${contentIndex}][image_file]" accept="image/*">
                </div>
                <div class="mb-3">
                    <label class="form-label">Column Position</label>
                    <select class="form-select" name="modules[${moduleIndex}][contents][${contentIndex}][column_position]">
                        <option value="">Select Column</option>
                        <option value="left">Left</option>
                        <option value="right">Right</option>
                        <option value="full">Full Width</option>
                    </select>
                </div>
            </div>
        `;
        
        return $(template);
    }

    function deleteModule(e) {
        e.preventDefault();
        
        if ($('.module-container').length <= 1) {
            alert('At least one module is required.');
            return;
        }
        
        if (confirm('Delete this module and all its contents?')) {
            $(this).closest('.module-container').remove();
            reindexModules();
            updateModuleDeleteButtons();
        }
    }

    function deleteContent(e) {
        e.preventDefault();
        
        const moduleContainer = $(this).closest('.module-container');
        const contentsContainer = moduleContainer.find('.contents-container');
        
        if (contentsContainer.find('.content-container').length <= 1) {
            alert('At least one content item is required per module.');
            return;
        }
        
        $(this).closest('.content-container').remove();
        
        const moduleIndex = parseInt(moduleContainer.attr('data-module-index'));
        reindexContents(moduleIndex);
    }

    function reindexModules() {
        let newModuleIndex = 0;
        
        $('.module-container').each(function() {
            $(this).attr('data-module-index', newModuleIndex);
            $(this).find('.module-title').text(`Module ${newModuleIndex + 1}`);
            
            $(this).find('input, select, textarea').each(function() {
                const name = $(this).attr('name');
                if (name && name.includes('modules[')) {
                    $(this).attr('name', name.replace(/modules\[\d+\]/, `modules[${newModuleIndex}]`));
                }
            });
            
            $(this).find('.contents-container').attr('data-module-index', newModuleIndex);
            reindexContents(newModuleIndex);
            
            newModuleIndex++;
        });
        
        moduleCounter = newModuleIndex;
    }

    function reindexContents(moduleIndex) {
        const moduleContainer = $(`.module-container[data-module-index="${moduleIndex}"]`);
        let newContentIndex = 0;
        
        moduleContainer.find('.content-container').each(function() {
            $(this).attr('data-content-index', newContentIndex);
            
            $(this).find('input, select, textarea').each(function() {
                const name = $(this).attr('name');
                if (name && name.includes(`modules[${moduleIndex}][contents][`)) {
                    $(this).attr('name', name.replace(
                        /modules\[\d+\]\[contents\]\[\d+\]/,
                        `modules[${moduleIndex}][contents][${newContentIndex}]`
                    ));
                }
            });
            
            newContentIndex++;
        });
        
        contentCounters[moduleIndex] = newContentIndex;
    }

    function handleVideoSourceChange(e) {
        const sourceType = $(this).val();
        const contentContainer = $(this).closest('.content-container');
        const urlContainer = contentContainer.find('.video-url-container');
        const uploadContainer = contentContainer.find('.video-upload-container');
        
        if (sourceType === 'upload') {
            urlContainer.hide();
            urlContainer.find('input').prop('required', false);
            uploadContainer.show();
        } else if (sourceType === 'youtube' || sourceType === 'vimeo') {
            uploadContainer.hide();
            uploadContainer.find('input').prop('required', false);
            urlContainer.show();
        } else {
            urlContainer.hide();
            uploadContainer.hide();
            urlContainer.find('input').prop('required', false);
            uploadContainer.find('input').prop('required', false);
        }
    }

    function updateModuleDeleteButtons() {
        const moduleCount = $('.module-container').length;
        
        if (moduleCount === 1) {
            $('.delete-module').hide();
        } else {
            $('.delete-module').show();
        }
    }

    function validateForm(e) {
        let isValid = true;
        const errors = [];
        
        if (!$('#title').val().trim()) {
            errors.push('Course title is required');
            isValid = false;
        }
        
        if (!$('#description').val().trim()) {
            errors.push('Course description is required');
            isValid = false;
        }
        
        if (!$('#category').val()) {
            errors.push('Course category is required');
            isValid = false;
        }
        
        $('.module-container').each(function(index) {
            const moduleTitle = $(this).find('input[name*="[title]"]').first().val();
            if (!moduleTitle || !moduleTitle.trim()) {
                errors.push(`Module ${index + 1} title is required`);
                isValid = false;
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fix the following errors:\n\n' + errors.join('\n'));
            return false;
        }
        
        return true;
    }

    $(document).ready(function() {
        initializeEventHandlers();
        
        $('.video-source-type').each(function() {
            if ($(this).val()) {
                $(this).trigger('change');
            }
        });
    });

})(jQuery);