<?php

namespace App\Services;

use App\Models\Key;
use App\Models\Value;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

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
     * @param  Request  $request
     * @return Model|Value
     */
    public function get(Value $value, Request $request): Value
    {
        return $this->value
            ->with($request->input('relations', []))
            ->find($value->id);
    }

    /**
     * @param  Key  $key
     * @param  Request  $request
     * @return Model|Value
     */
    public function store(Key $key, Request $request): Value
    {
        /** @var Value $value */
        $value = $key->values()->create($request->all());

        $value->languages()->attach($request->input('language_id'));
        $value->forms()->attach($request->input('form_id'));

        return $value;
    }

    /**
     * @param  Value  $value
     * @param  Request  $request
     * @return Model|Value
     */
    public function update(Value $value, Request $request): Value
    {
        $value->update($request->all());

        return $value->withoutRelations();
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
