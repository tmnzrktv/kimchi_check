<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;



class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password;,
        ]);

        // 2.06. Добавляем Юзеру сразу несколько ролей
        $user->assignRole('CEO', 'Head of Department');
        // $user->givePermissionTo('view check-list', 'create check-list', 'edit check-list');
    }
}
