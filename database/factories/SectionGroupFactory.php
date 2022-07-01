<?php

namespace LaravelCms\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use LaravelCms\Models\Cms\SectionGroup;

class SectionGroupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SectionGroup::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $name = $this->faker->unique()->lexify('??????????');

        return [
            'name' => $name,
            'is_published' => $this->faker->boolean(),
        ];
    }
}
