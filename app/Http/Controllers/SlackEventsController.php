<?php

namespace App\Http\Controllers;

use App\Jobs\HandleSlackMessage;
use Illuminate\Http\Client\Factory as Http;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SlackEventsController extends Controller
{
  public function __invoke(Request $request, Http $http): JsonResponse
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

    // Only respond when someone explicitly mentions our bot.
    if (! str_contains($event['text'] ?? '', "<@{$botId}>")) {
      return response()->json();
    }

    // Reply
    HandleSlackMessage::dispatch($event);

    return response()->json();
  }
}
