<?php


// ══════════════════════════════════════════════════════════════════
// app/Services/ContentGeneratorService.php
// يولّد أسئلة ومحتوى تعليمي بالـ AI — يشتغل في background Job
// ══════════════════════════════════════════════════════════════════
namespace App\Services;

use App\Models\{Game, Topic, Question};
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;

class ContentGeneratorService
{
    // ── توليد أسئلة جديدة لعبة ───────────────────────────────────
    public function generateQuestionsForGame(Game $game, int $count = 5): array
    {
        $topic      = $game->topic;
        $subject    = $topic->subject;
        $difficulty = $game->difficulty;

        $prompt = $this->buildQuestionPrompt(
            topicName: $topic->name,
            subjectName: $subject->name,
            ageGroup: $game->age_group,
            gameType: $game->game_type,
            difficulty: $difficulty,
            count: $count,
        );

        try {
            $response = OpenAI::chat()->create([
                'model'           => 'gpt-4o-mini',
                'max_tokens'      => 1500,
                'response_format' => ['type' => 'json_object'],
                'messages'        => [
                    ['role' => 'system', 'content' => 'أنت مولّد محتوى تعليمي. أجب بـ JSON فقط.'],
                    ['role' => 'user',   'content' => $prompt],
                ],
            ]);

            $raw       = $response->choices[0]->message->content;
            $data      = json_decode($raw, true);
            $questions = $data['questions'] ?? [];

            // حفظ الأسئلة في قاعدة البيانات
            $saved = [];
            foreach ($questions as $q) {
                $saved[] = Question::create([
                    'game_id'      => $game->id,
                    'topic_id'     => $topic->id,
                    'content'      => $q['content'],
                    'content_type' => 'text',
                    'answers'      => $q['answers'],
                    'answer_type'  => 'single_choice',
                    'difficulty'   => $difficulty,
                    'explanation'  => $q['explanation'] ?? null,
                    'hint'         => $q['hint'] ?? null,
                    'ai_generated' => true,
                    'ai_model'     => 'gpt-4o-mini',
                ]);
            }

            Log::info("ContentGenerator: Generated {$count} questions for game #{$game->id}");
            return $saved;
        } catch (\Exception $e) {
            Log::error('ContentGenerator error: ' . $e->getMessage());
            return [];
        }
    }

    protected function buildQuestionPrompt(
        string $topicName,
        string $subjectName,
        string $ageGroup,
        string $gameType,
        int    $difficulty,
        int    $count,
    ): string {
        return <<<PROMPT
        أنشئ {$count} أسئلة تعليمية متنوعة بالمواصفات التالية:

        المادة: {$subjectName}
        الموضوع: {$topicName}
        الفئة العمرية: {$ageGroup} سنوات
        نوع اللعبة: {$gameType}
        مستوى الصعوبة: {$difficulty} من 5

        الصيغة المطلوبة (JSON):
        {
          "questions": [
            {
              "content": "نص السؤال هنا",
              "answers": [
                {"id": 1, "text": "الخيار الأول", "is_correct": true},
                {"id": 2, "text": "الخيار الثاني", "is_correct": false},
                {"id": 3, "text": "الخيار الثالث", "is_correct": false},
                {"id": 4, "text": "الخيار الرابع", "is_correct": false}
              ],
              "explanation": "شرح لماذا هذه الإجابة صحيحة",
              "hint": "تلميح يساعد دون إعطاء الإجابة"
            }
          ]
        }

        تعليمات مهمة:
        - اجعل الأسئلة مناسبة تماماً للفئة العمرية {$ageGroup}
        - تأكد من تنوع الأسئلة
        - الإجابات يجب أن تكون منطقية وغير مضحكة
        - الشرح يجب أن يكون تعليمياً وبسيطاً
        - أجب باللغة العربية
        PROMPT;
    }
}
