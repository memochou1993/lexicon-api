<?php

namespace Database\Factories;

use App\Models\Form;
use Illuminate\Database\Eloquent\Factories\Factory;

class FormFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Form::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        static $index = 0;

        $index++;

        return [
            'name' => 'Form '.$index,
            'range_min' => pow(10, $index - 1) + ((int) $index > 1),
            'range_max' => pow(10, $index),
        ];
    }
}
