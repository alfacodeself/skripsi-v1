<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Admin;
use Carbon\Carbon;

class AdminFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Admin::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'foto' => $this->faker->word(),
            'nama' => $this->faker->word(),
            'email' => $this->faker->safeEmail(),
            'password' => bcrypt('password'),
            'superadmin' => 0,
            'status' => $this->faker->randomElement(["aktif", "tidak aktif", "peringatan", "diblokir"]),
            'catatan' => $this->faker->text(),
            'tanggal_verifikasi_email' => Carbon::now(),
            'remember_token' => $this->faker->uuid(),
        ];
    }
}
