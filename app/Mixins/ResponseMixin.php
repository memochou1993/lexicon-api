<?php

namespace App\Mixins;

use Closure;

class ResponseMixin
{
    /**
     * @return Closure
     */
    public function api()
    {
        return function (bool $success) {
            return $this->make([
                'success' => $success,
            ]);
        };
    }
}
