<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\Setting
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string $value
 * @property bool $public
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Setting newModelQuery()
 * @method static Builder|Setting newQuery()
 * @method static Builder|Setting query()
 * @method static Builder|Setting whereCreatedAt($value)
 * @method static Builder|Setting whereId($value)
 * @method static Builder|Setting whereName($value)
 * @method static Builder|Setting wherePublic($value)
 * @method static Builder|Setting whereType($value)
 * @method static Builder|Setting whereUpdatedAt($value)
 * @method static Builder|Setting whereValue($value)
 * @mixin Eloquent
 */
class Setting extends Model
{
    use HasFactory;

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
