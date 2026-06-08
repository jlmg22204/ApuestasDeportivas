<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Odd extends Model
{
    protected $fillable = ['event_id', 'type', 'label', 'value', 'is_active'];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function bets()
    {
        return $this->hasMany(Bet::class);
    }
}
