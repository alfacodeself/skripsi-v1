<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\JenisBiaya;

class JenisBiayaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = JenisBiaya::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'nama' => $this->faker->word(),
            'jenis_biaya' => $this->faker->randomElement(["persentase","flat"]),
            'berulang' => $this->faker->word(),
            'status' => $this->faker->randomElement(["aktif","tidak aktif"]),
        ];
    }
}
