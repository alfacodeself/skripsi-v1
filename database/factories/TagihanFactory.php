<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Langganan;
use App\Models\Tagihan;

class TagihanFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Tagihan::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'id_langganan' => Langganan::factory()->create()->id_langganan,
            'total_tagihan' => $this->faker->numberBetween(-10000, 10000),
            'sisa_tagihan' => $this->faker->numberBetween(-10000, 10000),
            'jatuh_tempo' => $this->faker->date(),
            'status_angsuran' => $this->faker->word(),
            'jumlah_angsuran' => $this->faker->numberBetween(-10000, 10000),
            'status' => $this->faker->randomElement(["lunas","belum lunas","dalam angsuran"]),
        ];
    }
}
