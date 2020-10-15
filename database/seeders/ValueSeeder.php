<?php

namespace Database\Seeders;

use App\Models\Value;
use App\Traits\HasStaticAttributes;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class ValueSeeder extends Seeder
{
    use HasStaticAttributes;

    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $keys = app(KeySeeder::class)->keys;

        $values = $keys->reduce(function ($carry, $key) {
            $values = Value::factory()->count(LanguageSeeder::AMOUNT * FormSeeder::AMOUNT)->make();

            return $carry->merge($key->values()->saveMany($values));
        }, app(Collection::class));

        $this->set('values', $values);
    }
}
