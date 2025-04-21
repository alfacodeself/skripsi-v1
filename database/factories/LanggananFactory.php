<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Langganan;
use App\Models\Paket;
use App\Models\Pelanggan;

class LanggananFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Langganan::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'id_paket' => Paket::factory()->create()->id_paket,
            'id_pelanggan' => Pelanggan::factory()->create()->id_pelanggan,
            'kode_layanan' => $this->faker->regexify('[A-Za-z0-9]{25}'),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'alamat_lengkap' => $this->faker->text(),
            'status' => $this->faker->randomElement(["diperiksa","disetujui","ditolak","aktif","tidak aktif",""]),
            'catatan' => $this->faker->text(),
            'tanggal_pemasangan' => $this->faker->date(),
            'tanggal_aktif' => $this->faker->date(),
            'tanggal_kadaluarsa' => $this->faker->date(),
        ];
    }
}
