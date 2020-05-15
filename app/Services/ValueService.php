<?php

namespace App\Services;

use App\Models\Key;
use App\Models\Value;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

class ValueService
{
    /**
     * @var Key
     */
    private Key $key;

    /**
     * @var Value
     */
    private Value $value;

    /**
     * Instantiate a new service instance.
     *
     * @param  Key  $key
     * @param  Value  $value
     */
    public function __construct(
        Key $key,
        Value $value
    ) {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * @param  int  $key_id
     * @param  array  $relations
     * @param  int  $per_page
     * @return LengthAwarePaginator
     */
    public function getByKey(int $key_id, array $relations, int $per_page): LengthAwarePaginator
    {
        return $this->key->find($key_id)->values()->with($relations)->paginate($per_page);
    }

    /**
     * @param  int  $keyId
     * @param  int  $languageId
     * @param  int  $formId
     * @param  array  $data
     * @return Model
     */
    public function storeByKey(int $keyId, int $languageId, int $formId, array $data): Model
    {
        $value = $this->key->find($keyId)->values()->create($data);

        $value->languages()->attach($languageId);
        $value->forms()->attach($formId);

        return $value;
    }

    /**
     * @param  Value  $value
     * @param  array  $relations
     * @return Model
     */
    public function get(Value $value, array $relations): Model
    {
        return $this->value->with($relations)->find($value->id);
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
