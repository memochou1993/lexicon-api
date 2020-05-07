<?php

namespace App\Mixins;

use Closure;
use Illuminate\Database\Eloquent\Model;

class FactoryBuilderMixin
{
    /**
     * @var Model
     */
    private Model $class;

    /**
     * @return Closure
     */
    public function withoutEvents()
    {
        return function () {
            $this->class::flushEventListeners();

            return $this;
        };
    }
}
