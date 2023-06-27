<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

/**
 * App\Models\Setting
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string $value
 * @property bool $public
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting wherePublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereValue($value)
 * @mixin \Eloquent
 */
class Setting extends Model
{
    use HasFactory, QueryCacheable;

    //One of the longest cached values in the site. Since they change so infrequently.
    public $cacheFor = 86400;
    public static $flushCacheOnUpdate = true;

    protected $guarded = ['type', 'name'];

    public function getMorphClass(): string {
        return 'setting';
    }

    public static function get(string $name)
    {
        return parent::where('name', $name)->first();
    }

    public static function getValue(string $name)
    {
        return parent::get($name)->value;
    }

    public function value() : Attribute
    {
        return Attribute::make(
            get: function($value, $attrs) {
                return match($attrs['type']) {
                    'integer' => (integer)$value,
                    'float' => (float)$value,
                    'boolean' => (boolean)$value,
                    default => $value
                };
            },
            set: fn($value) => (string)$value
        );
    }

    /**
     * Handles casting to correct type
     */
    protected function getCastType($key) {
        if ($key == 'value' && !empty($this->type)) {
            return $this->type;
        } else {
            return parent::getCastType($key);
        }
    }
}
