<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Langganan;
use App\Models\Progress;
use App\Models\ProgressLangganan;

class ProgressLanggananFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProgressLangganan::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'id_progress' => Progress::factory()->create()->id_progress,
            'id_langganan' => Langganan::factory()->create()->id_langganan,
            'status' => $this->faker->randomElement(["diproses","selesai","gagal","menunggu"]),
            'bukti' => $this->faker->word(),
            'keterangan' => $this->faker->text(),
            'tanggal_perencanaan' => $this->faker->date(),
        ];
    }
}
