<?php

use App\Models\Value;
use App\Traits\HasStaticAttributes;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class ValueSeeder extends Seeder
{
    use HasStaticAttributes;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $keys = app(KeySeeder::class)->keys;

        $values = $keys->reduce(function ($carry, $key) {
            $amount =  LanguageSeeder::DATA_AMOUNT * FormSeeder::DATA_AMOUNT;
            $values = factory(Value::class, $amount)->make();

            return $carry->merge($key->values()->saveMany($values));
        }, app(Collection::class));

        $this->set('values', $values);
    }
}
