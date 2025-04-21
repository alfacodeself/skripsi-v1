<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\BiayaPaket;
use App\Models\DetailTagihan;
use App\Models\Tagihan;

class DetailTagihanFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DetailTagihan::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'id_tagihan' => Tagihan::factory()->create()->id_tagihan,
            'id_biaya_paket' => BiayaPaket::factory()->create()->id_biaya_paket,
            'jumlah_biaya' => $this->faker->numberBetween(-10000, 10000),
            'keterangan' => $this->faker->text(),
        ];
    }
}
