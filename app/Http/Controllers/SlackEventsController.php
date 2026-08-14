<?php

namespace App\Http\Controllers;

use App\Jobs\HandleSlackMessage;
use App\Models\SlackConversation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SlackEventsController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        if ($request->input('type') === 'url_verification') {
            return response()->json(['challenge' => $request->input('challenge')]);
        }

        $event = $request->input('event', []);

        // Only process plain messages from humans.
        // Skip edits, joins, pins, and other message subtypes.
        if (($event['type'] ?? null) !== 'message'
          || isset($event['subtype'])
          || isset($event['bot_id'])) {
            return response()->json();
        }

        $botId = config('services.slack.bot_user_id');
        $mentioned = str_contains($event['text'] ?? '', "<@{$botId}>");

        $thread = $event['thread_ts'] ?? null;
        $inKnownThread = $thread && SlackConversation::where('thread_ts', $thread)->exists();

        // Answer when tagged, or when the reply lands in a thread we're already in.
        if (! $mentioned && ! $inKnownThread) {
            return response()->json();
        }

        HandleSlackMessage::dispatch($event);

        return response()->json();
    }
}
