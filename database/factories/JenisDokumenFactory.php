<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\JenisDokumen;

class JenisDokumenFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = JenisDokumen::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'nama_dokumen' => $this->faker->regexify('[A-Za-z0-9]{150}'),
            'status' => $this->faker->randomElement(["aktif","tidak aktif"]),
        ];
    }
}
