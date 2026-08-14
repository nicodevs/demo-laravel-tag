<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SlackEventsController extends Controller
{
  public function __invoke(Request $request): JsonResponse
  {
    if ($request->input('type') === 'url_verification') {
      return response()->json(['challenge' => $request->input('challenge')]);
    }

    logger()->info('From Slack: ', $request->all());

    return response()->json();
  }
}
