<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Achievement;

class AchievementsSeeder extends Seeder
{
    public function run(): void
    {
        $achievements = [
            // ── Streak ──────────────────────────────────────────
            [
                'name'         => 'المثابر',
                'icon'         => '🔥',
                'description'  => 'العب 3 أيام متتالية',
                'type'         => 'streak',
                'condition'    => ['streak_days' => 3],
                'stars_reward' => 20,
            ],
            [
                'name'         => 'البطل الأسبوعي',
                'icon'         => '🦸',
                'description'  => 'العب 7 أيام متتالية',
                'type'         => 'streak',
                'condition'    => ['streak_days' => 7],
                'stars_reward' => 50,
            ],
            [
                'name'         => 'لا يُهزم',
                'icon'         => '🏆',
                'description'  => 'العب 30 يوماً متتالياً',
                'type'         => 'streak',
                'condition'    => ['streak_days' => 30],
                'stars_reward' => 200,
            ],

            // ── Mastery ─────────────────────────────────────────
            [
                'name'         => 'أول خطوة',
                'icon'         => '🌱',
                'description'  => 'أتقن موضوعاً واحداً',
                'type'         => 'mastery',
                'condition'    => ['topics_count' => 1],
                'stars_reward' => 30,
            ],
            [
                'name'         => 'خبير',
                'icon'         => '🎓',
                'description'  => 'أتقن 5 مواضيع',
                'type'         => 'mastery',
                'condition'    => ['topics_count' => 5],
                'stars_reward' => 100,
            ],
            [
                'name'         => 'عبقري',
                'icon'         => '🧠',
                'description'  => 'أتقن 10 مواضيع',
                'type'         => 'mastery',
                'condition'    => ['topics_count' => 10],
                'stars_reward' => 250,
            ],

            // ── Accuracy ────────────────────────────────────────
            [
                'name'         => 'الدقيق',
                'icon'         => '🎯',
                'description'  => 'احصل على 100% في لعبة',
                'type'         => 'accuracy',
                'condition'    => ['accuracy' => 100, 'sessions' => 1],
                'stars_reward' => 25,
            ],
            [
                'name'         => 'الكامل',
                'icon'         => '💎',
                'description'  => 'احصل على 100% في 5 ألعاب',
                'type'         => 'accuracy',
                'condition'    => ['accuracy' => 100, 'sessions' => 5],
                'stars_reward' => 80,
            ],

            // ── Explorer ────────────────────────────────────────
            [
                'name'         => 'المستكشف',
                'icon'         => '🗺️',
                'description'  => 'العب في مادتين مختلفتين',
                'type'         => 'explorer',
                'condition'    => ['subjects' => 2],
                'stars_reward' => 15,
            ],
            [
                'name'         => 'المتعدد المواهب',
                'icon'         => '🌈',
                'description'  => 'العب في كل المواد الأربع',
                'type'         => 'explorer',
                'condition'    => ['subjects' => 4],
                'stars_reward' => 60,
            ],
        ];

        foreach ($achievements as $a) {
            Achievement::firstOrCreate(['name' => $a['name']], $a);
        }
    }
}
