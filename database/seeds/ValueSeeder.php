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

        $this->values = $keys->reduce(function ($carry, $key) {
            return $carry->merge(
                $key->values()->saveMany(
                    factory(Value::class, 6)->withoutEvents()->make()
                )
            );
        }, app(Collection::class));
    }
}
