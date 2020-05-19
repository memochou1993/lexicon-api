<?php

namespace App\Services;

use App\Models\Key;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

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
     * @param  array  $request
     * @return Model
     */
    public function get(Key $key, array $request): Model
    {
        return $this->key
            ->with(Arr::get($request, 'relations', []))
            ->find($key->id);
    }

    /**
     * @param  Key  $key
     * @param  array  $data
     * @return Model
     */
    public function update(Key $key, array $data): Model
    {
        $key = $this->key->find($key->id);

        $key->update($data);

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
     * @param  int  $languageId
     * @param  int  $formId
     * @param  array  $data
     * @return Model
     */
    public function storeValue(Key $key, int $languageId, int $formId, array $data): Model
    {
        $value = $key->values()->create($data);

        $value->languages()->attach($languageId);
        $value->forms()->attach($formId);

        return $value;
    }
}
