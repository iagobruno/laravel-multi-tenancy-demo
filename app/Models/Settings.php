<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $fillable = [
        'key',
        'value'
    ];

    // TODO: Cachear as configurações para evitar ficar solicitando TUDO novamente
    public static function getAll(array $keys = null)
    {
        return self::all()
            ->mapWithKeys(fn ($item) => [$item['key'] => $item['value']])
            ->all();
    }

    public static function get(string $key = null)
    {
        if (is_null($key)) {
            return self::getAll();
        }
        return self::firstWhere('key', $key)?->getAttribute('value');
        // return collect(self::getAll())->get($key);
    }

    public static function set(string|array $keys, string $value = null)
    {
        if (is_string($keys) && !is_null($value)) {
            $keys = [$keys => $value];
        }

        return self::upsert(
            collect($keys)->map(fn ($value, $key) => compact(['key', 'value']))->values()->toArray(),
            ['key'],
            ['value']
        );
    }
}
