<?php

namespace App\Models;

use Arr;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 *
 *
 * @property int $id
 * @property string $type
 * @property int $user_id
 * @property string $objectable_type
 * @property int $objectable_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereObjectableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereObjectableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereUserId($value)
 * @property int|null $game_id
 * @property string|null $auditable_type
 * @property int|null $auditable_id
 * @property string|null $context_type
 * @property int|null $context_id
 * @property array<array-key, mixed>|null $data
 * @property string|null $auditable_name
 * @property string|null $context_name
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent|null $auditable
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent|null $context
 * @property-read \App\Models\Game|null $game
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereAuditableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereAuditableName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereAuditableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereContextId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereContextName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereContextType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereGameId($value)
 * @mixin \Eloquent
 */
class AuditLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    protected $with = ['context', 'auditable', 'user'];

    protected $casts = [
        'data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function auditable(): BelongsTo
    {
        return $this->morphTo();
    }

    public function context(): BelongsTo
    {
        return $this->morphTo();
    }

    public function getMorphClass(): string
    {
        return 'audit_log';
    }

    public function data(): Attribute {
        return Attribute::make(function ($value) {
            $value = json_decode($value, true);

            if ($this->type == 'game_roles_update' && isset($value['add_role_ids']) && isset($value['remove_role_ids'])) {
                return [
                    ...$value,
                    'add_roles' => GameRole::whereIn('id', $value['add_role_ids'])->get(),
                    'remove_roles' => GameRole::whereIn('id', $value['remove_role_ids'])->get(),
                ];
            }

            if ($this->type == 'roles_update' && isset($value['add_role_ids']) && isset($value['remove_role_ids'])) {
                return [
                    ...$value,
                    'add_roles' => Role::whereIn('id', $value['add_role_ids'])->get(),
                    'remove_roles' => Role::whereIn('id', $value['remove_role_ids'])->get()
                ];
            }

            return $value;
        });
    }

    public static function log(
        string $type,
        ?Model $auditable = null,
        array $data = [],
        ?Game $game = null,
        ?Model $context=null
    ) {
        $log = new AuditLog([
            'type' => $type,
            'data' => $data,
        ]);
        $log->user()->associate(Auth::user());
        if (isset($auditable)) {
            $log->auditable()->associate($auditable);
            $log->auditable_name = $auditable->name ?? $auditable->title;
        }
        if (isset($context)) {
            $log->context()->associate($context);
            $log->context_name = $context->name ?? $context->title;
        }

        $game ??= $auditable?->game;
        if (isset($game)) {
            $log->game()->associate($game);
        }

        return $log->save();
    }

    public static function logCreate(Model $object, array $data = [], ?Game $game = null, bool $objectUserAsContext=false) {
        return self::log(
            type:'create',
            auditable: $object,
            context: $objectUserAsContext ? $object->user : null,
            game: $game
        );
    }

    public static function logUpdate(Model $object, array $data = [], ?Game $game = null, bool $objectUserAsContext=false) {
        $added = Arr::pull($data, '$added', []);
        $removed = Arr::pull($data, '$removed', []);

        $changes = [];
        foreach ($data as $key => $value) {
            if ($object->getAttribute($key) !== $value) {
                $changes[] = [
                    'type' => 'set',
                    'key' => $key,
                    'old_value' => $object->getAttribute($key),
                    'value' =>  $value
                ];
            }
        }

        foreach ($removed as $key => $arr) {
            foreach ($arr as $add) {
                $changes[] = [
                    'type' => 'remove',
                    'key' => $key,
                    'value_type' => $add->getMorphClass(),
                    'value_name' => $add->name ?? $add->title
                ];
            }
        }

        foreach ($added as $key => $arr) {
            foreach ($arr as $add) {
                $changes[] = [
                    'type' => 'add',
                    'key' => $key,
                    'value_type' => $add->getMorphClass(),
                    'value_name' => $add->name ?? $add->title
                ];
            }
        }

        if (empty($changes)) {
            return false; // No changes to log
        }

        $existingLog = AuditLog::whereAuditableType($object->getMorphClass())
            ->whereType('update')
            ->whereAuditableId($object->id)
            ->where('updated_at', '>', Carbon::now()->subMinutes(5))
            ->where('user_id', Auth::id())
            ->latest()->first();

        if (isset($existingLog)) {
            $existingLog->update([
                'data' => [
                    ...$existingLog->data,
                    'changes' => [
                        ...$existingLog->data['changes'] ?? [],
                        ...$changes
                    ]
                ]
            ]);
        } else {
            return self::log(
                type: 'update',
                auditable: $object,
                context: $objectUserAsContext ? $object->user : null,
                data: [
                    'changes' => $changes
                ],
                game: $game
            );
        }
    }

    public static function logDelete(Model $object, ?Game $game = null, bool $objectUserAsContext=false) {
        $log = new AuditLog([
            'type' => 'delete',
            'auditable_name' => $object->name ?? $object->title,
            'auditable_type' => $object->getMorphClass(),
            'data' => []
        ]);

        $log->user()->associate(Auth::user());

        $game ??= $object?->game;
        if (isset($game)) {
            $log->game()->associate($game);
        }

        if ($objectUserAsContext && $object->user) {
            $log->context()->associate($object->user);
            $log->context_name = $object->user->name;
        }

        return $log->save();
    }
}
