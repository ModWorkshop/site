<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property bool $enabled
 * @property int $order
 * @property int|null $package_id
 * @property int $price
 * @property int $duration_number
 * @property string $duration_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupporterPackage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupporterPackage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupporterPackage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupporterPackage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupporterPackage whereDurationNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupporterPackage whereDurationType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupporterPackage whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupporterPackage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupporterPackage whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupporterPackage whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupporterPackage wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupporterPackage wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupporterPackage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SupporterPackage extends Model
{
    protected $guarded = [];

    public function getMorphClass(): string {
        return 'supporter_package';
    }
}
