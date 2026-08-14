<?php

namespace App\Jobs;

use App\Ai\Agents\SlackBot;
use App\Models\SlackConversation;
use App\Models\SlackUser;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;

class HandleSlackMessage implements ShouldQueue
{
    use Queueable;

    public function __construct(public array $event) {}

    public function handle(): void
    {
        $text = trim(preg_replace('/<@[^>]+>/', '', $this->event['text']));

        $user = SlackUser::firstOrCreate(['slack_id' => $this->event['user']]);

        $thread = $this->event['thread_ts'] ?? $this->event['ts'];
        $mapping = SlackConversation::firstOrNew(['thread_ts' => $thread]);

        $agent = $mapping->conversation_id
        ? (new SlackBot)->continue($mapping->conversation_id, as: $user)
        : (new SlackBot)->forUser($user);

        $response = $agent->prompt($text);

        $mapping->conversation_id = $response->conversationId;
        $mapping->save();

        Http::withToken(config('services.slack.bot_token'))
            ->post('https://slack.com/api/chat.postMessage', [
                'channel' => $this->event['channel'],
                'text' => $response->text,
                'thread_ts' => $this->event['thread_ts'] ?? $this->event['ts'],
            ]);
    }
}
