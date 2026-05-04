<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\Topic;
// بيانات أولية للمواد والموضوعات

class SubjectsAndTopicsSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            [
                'name'       => 'الرياضيات',
                'name_en'    => 'Mathematics',
                'icon'       => '🔢',
                'color'      => '#4F46E5',
                'age_groups' => 'all',
                'topics'     => [
                    ['6-8',   'الجمع البسيط',      1],
                    ['6-8',   'الطرح البسيط',      1],
                    ['6-8',   'الأشكال الهندسية',  1],
                    ['9-11',  'الضرب',             2],
                    ['9-11',  'القسمة',             2],
                    ['9-11',  'الكسور',             3],
                    ['12-14', 'المعادلات',          3],
                    ['12-14', 'الاحتمالات',         4],
                ],
            ],
            [
                'name'       => 'اللغة العربية',
                'name_en'    => 'Arabic Language',
                'icon'       => '📚',
                'color'      => '#059669',
                'age_groups' => 'all',
                'topics'     => [
                    ['6-8',   'الحروف والكلمات',   1],
                    ['6-8',   'قراءة الجمل',       1],
                    ['9-11',  'قواعد النحو',       2],
                    ['9-11',  'التعبير الكتابي',   2],
                    ['12-14', 'البلاغة',           3],
                    ['12-14', 'تحليل النصوص',     4],
                ],
            ],
            [
                'name'       => 'العلوم',
                'name_en'    => 'Science',
                'icon'       => '🔬',
                'color'      => '#DC2626',
                'age_groups' => 'all',
                'topics'     => [
                    ['6-8',   'الحيوانات والنباتات', 1],
                    ['6-8',   'الطقس والفصول',      1],
                    ['9-11',  'جسم الإنسان',        2],
                    ['9-11',  'المادة وخصائصها',   2],
                    ['12-14', 'الفيزياء الأساسية', 3],
                    ['12-14', 'الكيمياء',          3],
                ],
            ],
            [
                'name'       => 'التفكير المنطقي',
                'name_en'    => 'Logical Thinking',
                'icon'       => '🧩',
                'color'      => '#7C3AED',
                'age_groups' => 'all',
                'topics'     => [
                    ['6-8',   'التصنيف والأنماط',  1],
                    ['9-11',  'حل المشكلات',       2],
                    ['9-11',  'التسلسل المنطقي',  2],
                    ['12-14', 'التفكير الاستنتاجي', 3],
                    ['12-14', 'البرمجة المنطقية',  4],
                ],
            ],
        ];

        foreach ($subjects as $subjectData) {
            $topics = $subjectData['topics'];
            unset($subjectData['topics']);

            $subject = Subject::create($subjectData);

            foreach ($topics as [$ageGroup, $name, $difficulty]) {
                Topic::create([
                    'subject_id'       => $subject->id,
                    'name'             => $name,
                    'age_group'        => $ageGroup,
                    'difficulty_level' => $difficulty,
                ]);
            }
        }
    }
}
