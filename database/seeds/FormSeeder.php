<?php

use App\Models\Form;
use App\Traits\HasStaticAttributes;
use Illuminate\Database\Seeder;

class FormSeeder extends Seeder
{
    use HasStaticAttributes;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->forms = factory(Form::class, 2)->withoutEvents()->create();
    }
}
