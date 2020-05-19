<?php

namespace App\Services;

use App\Models\Value;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class ValueService
{
    /**
     * @var Value
     */
    private Value $value;

    /**
     * Instantiate a new service instance.
     *
     * @param  Value  $value
     */
    public function __construct(
        Value $value
    ) {
        $this->value = $value;
    }

    /**
     * @param  Value  $value
     * @param  array  $request
     * @return Model
     */
    public function get(Value $value, array $request): Model
    {
        return $this->value
            ->with(Arr::get($request, 'relations', []))
            ->find($value->id);
    }

    /**
     * @param  Value  $value
     * @param  array  $data
     * @return Model
     */
    public function update(Value $value, array $data): Model
    {
        $value = $this->value->find($value->id);

        $value->update($data);

        return $value;
    }

    /**
     * @param  Value  $value
     * @return bool
     */
    public function destroy(Value $value): bool
    {
        return $this->value->destroy($value->id);
    }
}
