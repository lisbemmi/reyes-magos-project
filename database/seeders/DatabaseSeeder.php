<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
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
        $password = env('SEED_PASSWORD');

        if (! $password) {
            throw new \RuntimeException('Define SEED_PASSWORD en tu archivo .env');
        }

        $usuarios = [
            ['name' => 'Jefe de Caravana', 'email' => 'jefe@polonorte.test', 'rol' => 'JEFE_CARAVANA'],
            ['name' => 'Pastor de Belén', 'email' => 'pastor@belen.test', 'rol' => 'PASTOR'],
        ];

        foreach ($usuarios as $datos) {
            $usuario = User::where('email', $datos['email'])->first() ?? new User();

            $usuario->forceFill([
                'name' => $datos['name'],
                'email' => $datos['email'],
                'rol' => $datos['rol'],
                'password' => Hash::make($password),
            ])->save();
        }
    }
}
