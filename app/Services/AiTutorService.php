<?php
// ══════════════════════════════════════════════════════════════════
// app/Services/AiTutorService.php
// خدمة الـ AI Tutor — مع Guardrails وضبط لغة حسب العمر
// ══════════════════════════════════════════════════════════════════
namespace App\Services;

use App\Models\{Child, Topic};
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;

class AiTutorService
{
    // ── System Prompt حسب الفئة العمرية ─────────────────────────
    protected function buildSystemPrompt(Child $child, ?Topic $topic): string
    {
        $ageInstructions = match ($child->age_group) {
            '6-8' => <<<PROMPT
                أنت مساعد تعليمي ودود اسمك "ليو" 🦁.
                تتحدث مع طفل عمره 6-8 سنوات باللغة العربية البسيطة جداً.
                - استخدم جمل قصيرة ومفردات سهلة
                - أضف emoji كثيرة لتشجيع الطفل
                - لا تعطِ الإجابة مباشرة، بل وجّه بأسئلة بسيطة
                - احتفل بكل خطوة صحيحة يقوم بها
                - مثال: "وووو! إجابة رائعة! 🌟 دعنا نجرب سؤالاً آخر؟"
            PROMPT,

            '9-11' => <<<PROMPT
                أنت مساعد تعليمي ذكي اسمك "ليو" 🦁.
                تتحدث مع طفل عمره 9-11 سنوات.
                - اشرح المفاهيم خطوة بخطوة
                - استخدم أمثلة من الحياة اليومية
                - اكشف نمط الخطأ وأرشده لاكتشاف الحل بنفسه
                - شجّع التفكير النقدي بأسئلة مثل "لماذا تعتقد ذلك؟"
                - لا تحل الواجبات مباشرة
            PROMPT,

            '12-14' => <<<PROMPT
                أنت مساعد تعليمي متقدم اسمك "ليو".
                تتحدث مع طالب عمره 12-14 سنوات.
                - اشرح المنطق والأساس وراء كل مفهوم
                - تحدَّه بأسئلة مفتوحة تحفز التفكير الاستنتاجي
                - اربط المفاهيم ببعضها واشرح التطبيقات الواقعية
                - استخدم أسلوباً احترافياً نسبياً مع الحفاظ على الدفء
            PROMPT,

            default => 'أنت مساعد تعليمي ودود باسم ليو.',
        };

        $topicContext = $topic
            ? "\nالموضوع الحالي: {$topic->name} (مستوى الصعوبة {$topic->difficulty_level}/5)"
            : '';

        return trim($ageInstructions) . $topicContext . <<<RULES

        قواعد صارمة يجب اتباعها دائماً:
        - لا تتحدث أبداً عن مواضيع خارج التعليم والمساعدة الدراسية
        - لا تُعطِ معلومات شخصية أو تطلب منها
        - إذا سألك الطفل عن شيء غير تعليمي، أعد توجيهه بلطف
        - أجب باللغة العربية دائماً
        - الحد الأقصى للرد: 150 كلمة
        RULES;
    }

    // ── رد الـ AI Tutor ──────────────────────────────────────────
    public function respond(Child $child, array $messages, ?int $topicId = null): array
    {
        $topic  = $topicId ? Topic::find($topicId) : null;
        $system = $this->buildSystemPrompt($child, $topic);

        // فلترة المحادثة — نبقي آخر 10 رسائل فقط لتقليل الـ tokens
        $history = array_slice($messages, -10);

        try {
            $response = OpenAI::chat()->create([
                'model'      => 'gpt-4o-mini', // أرخص وأسرع للأطفال
                'max_tokens' => 200,
                'messages'   => [
                    ['role' => 'system', 'content' => $system],
                    ...$history,
                ],
            ]);

            return [
                'content' => $response->choices[0]->message->content,
                'tokens'  => $response->usage->totalTokens,
            ];
        } catch (\Exception $e) {
            Log::error('AI Tutor error: ' . $e->getMessage());

            // Fallback إذا فشل الـ AI
            return [
                'content' => 'عذراً، لم أتمكن من الرد الآن 😅 جرب مجدداً بعد قليل!',
                'tokens'  => 0,
            ];
        }
    }
}
