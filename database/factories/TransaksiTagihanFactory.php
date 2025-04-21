<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Tagihan;
use App\Models\TransaksiTagihan;

class TransaksiTagihanFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TransaksiTagihan::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'id_tagihan' => Tagihan::factory()->create()->id_tagihan,
            'kode' => $this->faker->regexify('[A-Za-z0-9]{25}'),
            'jumlah_bayar' => $this->faker->numberBetween(-10000, 10000),
            'jenis_pembayaran' => $this->faker->text(),
            'keterangan' => $this->faker->text(),
        ];
    }
}
