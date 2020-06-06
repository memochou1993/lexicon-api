<?php

use App\Models\Language;
use App\Traits\HasStaticAttributes;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    use HasStaticAttributes;

    public const AMOUNT = 3;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $languages = factory(Language::class, self::AMOUNT)->create();

        $this->set('languages', $languages);
    }
}
