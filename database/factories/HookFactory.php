<?php

namespace Database\Factories;

use App\Models\Hook;
use Illuminate\Database\Eloquent\Factories\Factory;

class HookFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Hook::class;

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
            'url' => config('app.url').'/api/'.config('lexicon.path'),
            'events' => [
                'sync',
            ],
        ];
    }
}
