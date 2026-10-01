<?php

namespace Database\Factories;

use App\Models\Channel;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChannelFactory extends Factory
{
    protected $model = Channel::class;

    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'name' => $this->faker->word(),
        ];
    }
}
