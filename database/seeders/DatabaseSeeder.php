<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\School;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Classroom;
use App\Models\SchoolYear;
use App\Models\SchoolEvent;
use App\Models\SubjectCategory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'super@admin.com',
            'password' => bcrypt('mantapjiwa00'),
        ]);

        $this->createSchools();
        $this->createClassrooms();
        $this->createSchoolYear();
        $this->createSubjectCategories();
        $this->createSubjects();
        $this->createTeachers();
        $this->createSchoolEvents();
    }

    public function createSchools(): void
    {
        $school = School::factory()
            ->state([
                'name' => 'SD BASIC 1',
            ])
            ->create();

        context()->add('school', $school);
    }

    public function createClassrooms(): void
    {
        Classroom::factory(10)->create();
    }

    public function createSchoolYear(): void
    {
        SchoolYear::factory()->active()->create();
    }

    public function createSubjectCategories(): void
    {
        SubjectCategory::factory()
            ->state([
                'name' => 'General Subject',
                'sort_order' => 1,
            ])
            ->create();
    }

    public function createSubjects(): void
    {
        Subject::factory()->state([
            'subject_category_id' => SubjectCategory::first()->getRouteKey(),
            'name' => 'Mathematics',
            'sort_order' => 1,
        ])->create();

        // Subject::factory(10)->create();
    }

    public function createTeachers(): void
    {
        Teacher::factory(5)->create();
    }

    public function createSchoolEvents(): void 
    {
        SchoolEvent::factory(5)->create();
    }
}
