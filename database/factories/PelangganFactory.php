<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Pelanggan;

class PelangganFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Pelanggan::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'foto' => $this->faker->word(),
            'nama' => $this->faker->word(),
            'kode' => $this->faker->regexify('[A-Za-z0-9]{20}'),
            'email' => $this->faker->safeEmail(),
            'telepon' => $this->faker->regexify('[A-Za-z0-9]{25}'),
            'password' => $this->faker->password(),
            'status' => $this->faker->randomElement(["aktif","tidak aktif","peringatan","diblokir","diperiksa"]),
            'catatan' => $this->faker->text(),
            'tanggal_verifikasi_email' => $this->faker->date(),
            'tanggal_verifikasi_telepon' => $this->faker->date(),
            'remember_token' => $this->faker->uuid(),
        ];
    }
}
