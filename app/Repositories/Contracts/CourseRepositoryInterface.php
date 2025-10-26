<?php

namespace App\Repositories\Contracts;

interface CourseRepositoryInterface
{
    public function getAllCourses();
    public function createCourse(array $data);
    public function getCourseWithRelations($id);
    public function updateCourse($id, array $data);
    public function deleteCourse($id);
}