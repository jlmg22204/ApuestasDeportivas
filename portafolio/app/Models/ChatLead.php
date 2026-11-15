<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatLead extends Model
{
    public const STATUSES = ['nuevo', 'contactado', 'cerrado', 'descartado'];

    protected $fillable = [
        'name', 'email', 'phone', 'project_type', 'budget', 'summary', 'status',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(ChatConversation::class, 'chat_conversation_id');
    }
}
