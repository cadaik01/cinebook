<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Gọi các Seeder theo thứ tự logic
        $this->call([
            UserSeeder::class,
            GenreSeeder::class,
            // MovieSeeder::class, // Cần tạo file này hoặc import SQL trước
            // MovieGenreSeeder::class, // Chỉ chạy được khi đã có Movies
        ]);
    }
}