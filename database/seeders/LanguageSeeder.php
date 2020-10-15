<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Traits\HasStaticAttributes;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    use HasStaticAttributes;

    public const AMOUNT = 3;

    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $languages = Language::factory()->count(self::AMOUNT)->create();

        $this->set('languages', $languages);
    }
}
