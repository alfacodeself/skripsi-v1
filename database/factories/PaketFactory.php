<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Kategori;
use App\Models\Paket;

class PaketFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Paket::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'id_kategori' => Kategori::factory()->create()->id_kategori,
            'nama' => $this->faker->word(),
            'slug' => $this->faker->slug(),
            'deskripsi' => $this->faker->text(),
            'harga' => $this->faker->numberBetween(-10000, 10000),
            'layanan' => '{}',
            'bisa_diangsur' => $this->faker->word(),
            'maksimal_angsuran' => $this->faker->numberBetween(-10000, 10000),
            'minimal_jumlah_angsuran' => $this->faker->numberBetween(-10000, 10000),
            'durasi_hari_angsuran' => $this->faker->numberBetween(-10000, 10000),
            'status' => $this->faker->randomElement(["aktif","tidak aktif"]),
        ];
    }
}
