<?php
namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $subjects = [
            'Matematika',
            'Bahasa Indonesia',
            'Bahasa Inggris',
            'IPA',
            'IPS',
            'Pendidikan Pancasila',
            'Bahasa Jawa',
            'Lainnya'
        ];

        User::create([
            'name'     => 'Admin NilaiKu Team',
            'email'    => 'nilaiku@gmail.com',
            'password' => bcrypt('NilaiKuTeam@69'),
            'role'     => 'admin',
        ]);

    foreach ($subjects as $subject) {
        Subject::create([
            'name' => $subject,
        ]);
    }
    }
}
