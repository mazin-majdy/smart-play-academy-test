<?php

namespace App\Jobs;

use App\Models\{Child, ParentNotification};
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class NotifyParentJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(
        public Child  $child,
        public string $type,
        public array  $data = []
    ) {}

    public function handle(): void
    {
        $messages = [
            'frustration_detected' => [
                'title' => "⚠️ {$this->child->name} يحتاج مساعدة",
                'body'  => "لاحظنا أن {$this->child->name} يواجه صعوبة في اللعبة. قد يكون بحاجة لدعمك.",
            ],
            'limit_reached' => [
                'title' => "⏱️ {$this->child->name} وصل للحد اليومي",
                'body'  => "أتمّ {$this->child->name} وقت الشاشة المسموح به اليوم. وقت للراحة!",
            ],
            'streak_broken' => [
                'title' => "📅 انقطعت سلسلة {$this->child->name}",
                'body'  => "لم يلعب {$this->child->name} أمس. شجّعه على العودة اليوم!",
            ],
        ];

        $msg = $messages[$this->type] ?? [
            'title' => "إشعار جديد",
            'body'  => "تحديث عن {$this->child->name}",
        ];

        foreach ($this->child->parents as $parent) {
            ParentNotification::create([
                'user_id'  => $parent->id,
                'child_id' => $this->child->id,
                'type'     => $this->type,
                'title'    => $msg['title'],
                'body'     => $msg['body'],
                'data'     => $this->data,
            ]);
        }
    }
}
