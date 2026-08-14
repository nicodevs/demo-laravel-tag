<?php

namespace App\Jobs;

use App\Ai\Agents\SlackBot;
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

    $response = (new SlackBot)->prompt($text);

    Http::withToken(config('services.slack.bot_token'))
      ->post('https://slack.com/api/chat.postMessage', [
        'channel' => $this->event['channel'],
        'text' => $response->text,
        'thread_ts' => $this->event['thread_ts'] ?? $this->event['ts'],
      ]);
  }
}
