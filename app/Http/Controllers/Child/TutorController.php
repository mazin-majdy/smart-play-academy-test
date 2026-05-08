<?php
// ══════════════════════════════════════════════════════════════════
// app/Http/Controllers/Child/TutorController.php
// المساعد الذكي داخل اللعبة — يشرح بلغة مناسبة لعمر الطفل
// ══════════════════════════════════════════════════════════════════
namespace App\Http\Controllers\Child;

use App\Http\Controllers\Controller;
use App\Models\AiTutorChat;
use App\Services\AiTutorService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TutorController extends Controller
{
    public function __construct(private AiTutorService $tutor) {}

    // POST /play/tutor/chat
    public function chat(Request $request): JsonResponse
    {
        $data = $request->validate([
            'message'        => 'required|string|max:500',
            'session_id'     => 'nullable|exists:game_sessions,id',
            'topic_id'       => 'nullable|exists:topics,id',
            'chat_id'        => 'nullable|exists:ai_tutor_chats,id',
        ]);

        $child = $request->_child;

        // نجلب أو ننشئ محادثة
        $chat = $data['chat_id']
            ? AiTutorChat::find($data['chat_id'])
            : AiTutorChat::create([
                'child_id'        => $child->id,
                'game_session_id' => $data['session_id'] ?? null,
                'topic_id'        => $data['topic_id']   ?? null,
                'messages'        => [],
            ]);

        // نضيف رسالة الطفل
        $messages   = $chat->messages;
        $messages[] = ['role' => 'user', 'content' => $data['message']];

        // نحصل على رد الـ AI
        $reply = $this->tutor->respond(
            child: $child,
            messages: $messages,
            topicId: $data['topic_id'] ?? $chat->topic_id,
        );

        // نضيف رد الـ AI
        $messages[] = ['role' => 'assistant', 'content' => $reply['content']];

        // نحفظ المحادثة
        $chat->update([
            'messages'      => $messages,
            'message_count' => count($messages),
            'tokens_used'   => $chat->tokens_used + ($reply['tokens'] ?? 0),
        ]);

        return response()->json([
            'chat_id' => $chat->id,
            'reply'   => $reply['content'],
        ]);
    }
}
