<?php

namespace App\Models;

use Eloquent;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

/**
 * App\Models\Permission
 *
 * @property int $id
 * @property string $slug
 * @property string $name
 * @property string $desc
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Permission newModelQuery()
 * @method static Builder|Permission newQuery()
 * @method static Builder|Permission query()
 * @method static Builder|Permission whereCreatedAt($value)
 * @method static Builder|Permission whereDesc($value)
 * @method static Builder|Permission whereId($value)
 * @method static Builder|Permission whereName($value)
 * @method static Builder|Permission whereSlug($value)
 * @method static Builder|Permission whereUpdatedAt($value)
 * @property string|null $type
 * @method static Builder|Permission whereType($value)
 * @mixin Eloquent
 */
class Permission extends Model
{
    use HasFactory, Cachable;

    public function getMorphClass(): string {
        return 'permission';
    }
}
