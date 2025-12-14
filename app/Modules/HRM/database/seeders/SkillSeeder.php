<?php

namespace App\Modules\HRM\database\seeders;

use App\Modules\HRM\Models\Skill;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            // Technical Skills (Development)
            ['name' => 'PHP', 'category' => 'Development', 'description' => 'Server-side scripting language'],
            ['name' => 'Laravel', 'category' => 'Development', 'description' => 'PHP framework for web artisans'],
            ['name' => 'JavaScript', 'category' => 'Development', 'description' => 'Programming language of the web'],
            ['name' => 'Vue.js', 'category' => 'Development', 'description' => 'Progressive JavaScript framework'],
            ['name' => 'React', 'category' => 'Development', 'description' => 'Library for building user interfaces'],
            ['name' => 'Python', 'category' => 'Development', 'description' => 'High-level programming language'],
            ['name' => 'MySQL', 'category' => 'Database', 'description' => 'Relational database management system'],
            ['name' => 'Git', 'category' => 'Tools', 'description' => 'Version control system'],
            
            // Design Skills
            ['name' => 'Adobe Photoshop', 'category' => 'Design', 'description' => 'Graphics editing software'],
            ['name' => 'Figma', 'category' => 'Design', 'description' => 'Vector graphics editor and prototyping tool'],
            
            // Soft Skills
            ['name' => 'Leadership', 'category' => 'Soft Skill', 'description' => 'Ability to lead and inspire a team'],
            ['name' => 'Communication', 'category' => 'Soft Skill', 'description' => 'Effective verbal and written communication'],
            ['name' => 'Problem Solving', 'category' => 'Soft Skill', 'description' => 'Ability to identify and solve complex problems'],
            ['name' => 'Teamwork', 'category' => 'Soft Skill', 'description' => 'Ability to work effectively in a team'],
            
            // Management
            ['name' => 'Project Management', 'category' => 'Management', 'description' => 'Planning and executing projects'],
            ['name' => 'Agile/Scrum', 'category' => 'Management', 'description' => 'Project management methodology'],
        ];

        foreach ($skills as $skill) {
            Skill::firstOrCreate(
                ['name' => $skill['name']],
                [
                    'category' => $skill['category'],
                    'description' => $skill['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
