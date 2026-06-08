<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bet extends Model
{
    protected $fillable = [
        'user_id', 'event_id', 'odd_id',
        'amount', 'odd_value', 'potential_winnings', 'status',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'odd_value' => 'decimal:2',
            'potential_winnings' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function odd()
    {
        return $this->belongsTo(Odd::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'Pendiente',
            'won'       => 'Ganada',
            'lost'      => 'Perdida',
            'cancelled' => 'Cancelada',
            default     => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'warning',
            'won'       => 'success',
            'lost'      => 'danger',
            'cancelled' => 'secondary',
            default     => 'secondary',
        };
    }
}
