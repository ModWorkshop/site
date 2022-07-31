<?php

namespace Database\Factories;

use App\Models\Mod;
use Illuminate\Database\Eloquent\Factories\Factory;

class ModFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Mod::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $faker = $this->faker;
        return [
            'name' => $faker->words(3, true),
            'user_id' => 1,
            'desc' => $faker->text(500),
            'version' => $faker->word(),
            'visibility' => 1,
            'game_id' => 2
        ];
    }
}
