<?php

namespace LaravelCms\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use LaravelCms\Models\Cms\Section;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class SectionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Section::class;

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
            'folder' => $name,
            'description' => $this->faker->text(),
            'cms_section_group_id' => 1,
            'is_published' => $this->faker->boolean(),
        ];
    }
}
