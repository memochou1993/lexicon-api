<?php

namespace App\Services;

use App\Models\Key;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class KeyService
{
    /**
     * @var Key
     */
    private Key $key;

    /**
     * Instantiate a new service instance.
     *
     * @param  Key  $key
     */
    public function __construct(
        Key $key
    ) {
        $this->key = $key;
    }

    /**
     * @param  Key  $key
     * @param  Request  $request
     * @return Model
     */
    public function get(Key $key, Request $request): Model
    {
        return $this->key
            ->with($request->relations ?? [])
            ->find($key->id);
    }

    /**
     * @param  Key  $key
     * @param  Request  $request
     * @return Model
     */
    public function update(Key $key, Request $request): Model
    {
        $key->update($request->all());

        return $key;
    }

    /**
     * @param  Key  $key
     * @return bool
     */
    public function destroy(Key $key): bool
    {
        return $this->key->destroy($key->id);
    }

    /**
     * @param  Key  $key
     * @param  Request  $request
     * @return Model
     */
    public function storeValue(Key $key, Request $request): Model
    {
        $value = $key->values()->create($request->all());

        $value->languages()->attach($request->languageId);
        $value->forms()->attach($request->formId);

        return $value;
    }
}
