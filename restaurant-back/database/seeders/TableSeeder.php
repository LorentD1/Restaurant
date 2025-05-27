<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Table;

class TableSeeder extends Seeder
{
    public function run(): void
    {
        $tables = [
            ['name' => 'Tavolina A1', 'seats' => 4],
            ['name' => 'Tavolina A2', 'seats' => 4],
            ['name' => 'Tavolina A3', 'seats' => 4],
            ['name' => 'Tavolina B1', 'seats' => 6],
            ['name' => 'Tavolina B2', 'seats' => 6],
            ['name' => 'Tavolina B3', 'seats' => 8],
            ['name' => 'Tavolina VIP', 'seats' => 10],

        ];

        foreach ($tables as $table) {
            Table::create($table);
        }
    }
}
