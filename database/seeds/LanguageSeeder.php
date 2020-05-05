<?php

use App\Models\Language;
use App\Traits\HasStaticAttributes;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    use HasStaticAttributes;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->languages = factory(Language::class, 3)->create();
    }
}
