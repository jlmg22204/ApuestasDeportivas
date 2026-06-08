<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Odd;
use App\Models\Sport;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::create([
            'name'     => 'Administrador',
            'email'    => 'admin@apuestas.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'balance'  => 0,
        ]);

        // Test user
        User::create([
            'name'     => 'Usuario Demo',
            'email'    => 'demo@apuestas.com',
            'password' => Hash::make('password'),
            'role'     => 'user',
            'balance'  => 500.00,
        ]);

        // Sports
        $sports = [
            ['name' => 'Fútbol',      'icon' => '⚽'],
            ['name' => 'Baloncesto',  'icon' => '🏀'],
            ['name' => 'Tenis',       'icon' => '🎾'],
            ['name' => 'Béisbol',     'icon' => '⚾'],
            ['name' => 'Fútbol Americano', 'icon' => '🏈'],
        ];

        foreach ($sports as $s) {
            Sport::create($s);
        }

        $futbol = Sport::where('name', 'Fútbol')->first();
        $basket = Sport::where('name', 'Baloncesto')->first();
        $tenis  = Sport::where('name', 'Tenis')->first();

        // Sample events
        $events = [
            [
                'sport_id'   => $futbol->id,
                'home_team'  => 'Real Madrid',
                'away_team'  => 'Barcelona',
                'league'     => 'La Liga',
                'event_date' => now()->addDays(2),
                'status'     => 'upcoming',
                'odds'       => [['home', 'Victoria Local', 2.10], ['draw', 'Empate', 3.40], ['away', 'Victoria Visitante', 3.20]],
            ],
            [
                'sport_id'   => $futbol->id,
                'home_team'  => 'Manchester City',
                'away_team'  => 'Liverpool',
                'league'     => 'Premier League',
                'event_date' => now()->addDays(3),
                'status'     => 'upcoming',
                'odds'       => [['home', 'Victoria Local', 1.90], ['draw', 'Empate', 3.60], ['away', 'Victoria Visitante', 3.80]],
            ],
            [
                'sport_id'   => $basket->id,
                'home_team'  => 'Lakers',
                'away_team'  => 'Celtics',
                'league'     => 'NBA',
                'event_date' => now()->addDays(1),
                'status'     => 'upcoming',
                'odds'       => [['home', 'Victoria Local', 1.75], ['away', 'Victoria Visitante', 2.05]],
            ],
            [
                'sport_id'   => $tenis->id,
                'home_team'  => 'Djokovic',
                'away_team'  => 'Alcaraz',
                'league'     => 'ATP Finals',
                'event_date' => now()->addDays(4),
                'status'     => 'upcoming',
                'odds'       => [['home', 'Djokovic', 2.20], ['away', 'Alcaraz', 1.65]],
            ],
        ];

        foreach ($events as $eventData) {
            $odds = $eventData['odds'];
            unset($eventData['odds']);

            $event = Event::create($eventData);

            foreach ($odds as [$type, $label, $value]) {
                Odd::create([
                    'event_id' => $event->id,
                    'type'     => $type,
                    'label'    => $label,
                    'value'    => $value,
                ]);
            }
        }
    }
}
