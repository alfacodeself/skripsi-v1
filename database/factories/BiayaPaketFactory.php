<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\BiayaPaket;
use App\Models\JenisBiaya;
use App\Models\Paket;

class BiayaPaketFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BiayaPaket::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'id_paket' => Paket::factory()->create()->id_paket,
            'id_jenis_biaya' => JenisBiaya::factory()->create()->id_jenis_biaya,
            'besar_biaya' => $this->faker->numberBetween(-10000, 10000),
            'status' => $this->faker->randomElement(["aktif","tidak aktif"]),
            'keterangan' => $this->faker->text(),
        ];
    }
}
