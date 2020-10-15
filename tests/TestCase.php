<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * @var User
     */
    protected $user;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /**
     * @param  array  $models
     * @return void
     */
    public function flushEventListeners(...$models): void
    {
        foreach ($models as $model) {
            $model::flushEventListeners();
        }
    }
}
