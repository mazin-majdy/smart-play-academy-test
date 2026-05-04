<?php
// يقيس الإحباط والانخراط — يشتغل real-time من الـ frontend
// ══════════════════════════════════════════════════════════════════
namespace App\Services;

use App\Models\Child;
use App\Models\GameSession;
use App\Jobs\NotifyParentJob;

class EngagementTracker
{
    // الـ frontend بيرسل هاي البيانات كل 30 ثانية
    public function track(GameSession $session, array $data): array
    {
        /*
        $data = [
            'avg_think_time'      => 15.3,  // ثانية
            'frustration_signals' => 2,      // نقرات متكررة / أخطاء متتالية
            'pauses'              => 1,      // توقف > 10 ثواني
            'hints_requested'     => 1,
        ]
        */

        // نحدّث بيانات الـ engagement في الجلسة
        $current = $session->engagement_data ?? [];
        $merged  = array_merge($current, $data);
        $session->update(['engagement_data' => $merged]);

        $action = 'continue'; // الافتراضي: كمّل

        // ── كشف الإحباط ──────────────────────────────────────────
        if ($this->isFrustrated($data)) {
            $action = $this->handleFrustration($session);
        }

        // ── كشف الملل (سرعة عالية جداً = قد يغشّ أو يضغط بلا تفكير)
        if (($data['avg_think_time'] ?? 999) < 2) {
            $action = 'slow_down'; // نُظهر رسالة تشجع على التفكير
        }

        return [
            'action'  => $action,
            'message' => $this->getActionMessage($action, $session->child),
        ];
    }

    protected function isFrustrated(array $data): bool
    {
        // 3+ إشارات إحباط أو 3+ إيقافات متتالية
        return ($data['frustration_signals'] ?? 0) >= 3
            || ($data['pauses'] ?? 0) >= 3;
    }

    protected function handleFrustration(GameSession $session): string
    {
        $child = $session->child;

        // 1. أرسل إشعار للأهل (عبر Queue لا يوقف الـ request)
        NotifyParentJob::dispatch($child, 'frustration_detected', [
            'game_id'    => $session->game_id,
            'session_id' => $session->id,
        ]);

        // 2. اقترح تغيير نمط اللعبة (من حسابي → بصري مثلاً)
        return $this->suggestGameSwitch($session->game->game_type);
    }

    protected function suggestGameSwitch(string $currentType): string
    {
        // إذا كان يلعب لعبة رياضية جافة → اقترح قصة تفاعلية
        $alternatives = [
            'math_puzzle'    => 'switch_to_visual',
            'logic_chain'    => 'switch_to_story',
            'word_challenge' => 'switch_to_drag_drop',
            'quiz'           => 'take_break',
        ];

        return $alternatives[$currentType] ?? 'take_break';
    }

    protected function getActionMessage(string $action, Child $child): string
    {
        $name = $child->name;
        return match ($action) {
            'switch_to_visual'  => "خلينا نجرب بطريقة أخرى يا {$name}! 🎨",
            'switch_to_story'   => "وقت قصة ممتعة! 📖",
            'switch_to_drag_drop' => "جرّب السحب والإفلات! 🖱️",
            'take_break'        => "خذ استراحة قصيرة يا {$name} 😊 ثم ارجع أقوى!",
            'slow_down'         => "فكّر معي خطوة بخطوة 🤔",
            default             => 'continue',
        };
    }
}
