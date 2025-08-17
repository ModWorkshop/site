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
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Permission all($columns = [])
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Permission avg($column)
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Permission cache(array $tags = [])
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Permission cachedValue(array $arguments, string $cacheKey)
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Permission count($columns = '*')
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Permission disableCache()
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Permission disableModelCaching()
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Permission exists()
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Permission flushCache(array $tags = [])
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Permission getModelCacheCooldown(\Illuminate\Database\Eloquent\Model $instance)
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Permission inRandomOrder($seed = '')
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Permission insert(array $values)
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Permission isCachable()
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Permission max($column)
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Permission min($column)
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Permission sum($column)
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Permission truncate()
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Permission withCacheCooldownSeconds(?int $seconds = null)
 * @mixin Eloquent
 */
class Permission extends Model
{
    use HasFactory, Cachable;

    public function getMorphClass(): string {
        return 'permission';
    }
}
