<?php

namespace Database\Seeders;

use App\Models\Form;
use App\Traits\HasStaticAttributes;
use Illuminate\Database\Seeder;

class FormSeeder extends Seeder
{
    use HasStaticAttributes;

    public const AMOUNT = 2;

    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $forms = Form::factory()->count(self::AMOUNT)->create();

        $this->set('forms', $forms);
    }
}
