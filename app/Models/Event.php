<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'sport_id', 'home_team', 'away_team', 'league',
        'event_date', 'status', 'home_score', 'away_score', 'result',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'datetime',
            'home_score' => 'integer',
            'away_score' => 'integer',
        ];
    }

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    public function odds()
    {
        return $this->hasMany(Odd::class);
    }

    public function bets()
    {
        return $this->hasMany(Bet::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'upcoming'  => 'Próximo',
            'live'      => 'En Vivo',
            'finished'  => 'Finalizado',
            'cancelled' => 'Cancelado',
            default     => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'upcoming'  => 'primary',
            'live'      => 'danger',
            'finished'  => 'secondary',
            'cancelled' => 'dark',
            default     => 'secondary',
        };
    }
}
