<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Ai\Concerns\HasConversations;

class SlackUser extends Model
{
    use HasConversations;

    protected $guarded = [];
}
