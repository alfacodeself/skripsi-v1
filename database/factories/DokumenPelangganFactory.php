<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\DokumenPelanggan;
use App\Models\JenisDokuman;
use App\Models\Pelanggan;

class DokumenPelangganFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DokumenPelanggan::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'id_dokumen' => JenisDokuman::factory()->create()->id_dokumen,
            'id_pelanggan' => Pelanggan::factory()->create()->id_pelanggan,
            'path' => $this->faker->word(),
            'status' => $this->faker->randomElement(["diperiksa","disetujui","ditolak"]),
            'akses_pelanggan' => $this->faker->word(),
            'catatan' => $this->faker->text(),
        ];
    }
}
