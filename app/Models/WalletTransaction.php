<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    protected $fillable = [
        'user_id', 'type', 'amount',
        'balance_before', 'balance_after', 'description', 'reference_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'balance_before' => 'decimal:2',
            'balance_after' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'deposit'    => 'Depósito',
            'withdrawal' => 'Retiro',
            'bet'        => 'Apuesta',
            'winning'    => 'Ganancia',
            'refund'     => 'Reembolso',
            default      => $this->type,
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'deposit'    => 'success',
            'withdrawal' => 'danger',
            'bet'        => 'warning',
            'winning'    => 'success',
            'refund'     => 'info',
            default      => 'secondary',
        };
    }
}
