<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarkMigrationAsRanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if the migration is already in the table
        $exists = DB::table('migrations')
            ->where('migration', '2023_05_10_000000_create_comment_likes_table')
            ->exists();
            
        if (!$exists) {
            // Insert the migration entry
            DB::table('migrations')->insert([
                'migration' => '2023_05_10_000000_create_comment_likes_table',
                'batch' => 1,
            ]);
            
            $this->command->info('Migration marked as ran: 2023_05_10_000000_create_comment_likes_table');
        } else {
            $this->command->info('Migration already exists in the migrations table.');
        }
    }
} 