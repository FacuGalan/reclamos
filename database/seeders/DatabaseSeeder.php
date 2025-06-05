<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Area;
use App\Models\Secretaria;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear secretarías de ejemplo
        $secretaria1 = Secretaria::create(['nombre' => 'Secretaría de Gobierno']);
        $secretaria2 = Secretaria::create(['nombre' => 'Secretaría de Obras Públicas']);
        
        // Crear áreas de ejemplo
        $area1 = Area::create(['nombre' => 'Área de Sistemas', 'secretaria_id' => $secretaria1->id]);
        $area2 = Area::create(['nombre' => 'Área de RRHH', 'secretaria_id' => $secretaria1->id]);
        $area3 = Area::create(['nombre' => 'Área de Mantenimiento', 'secretaria_id' => $secretaria2->id]);

        // Crear usuario de prueba
        $testUser = User::factory()->create([
            'dni' => '1234567890',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'telefono' => '1122334455',
        ]);

        // Asignar áreas al usuario de prueba
        $testUser->areas()->attach([$area1->id, $area2->id]);

        // Crear usuarios adicionales
        $users = User::factory(10)->create();
        
        // Asignar áreas aleatorias a usuarios
        foreach ($users as $user) {
            $randomAreas = Area::inRandomOrder()->take(rand(1, 3))->pluck('id');
            $user->areas()->attach($randomAreas);
        }
    }
}