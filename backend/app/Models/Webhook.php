<?php

namespace App\Models;

use App\Services\Utils;
use Auth;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\Collection;
use Spatie\WebhookServer\WebhookCall;
use StringTemplate\NestedKeyIterator;
use StringTemplate\RecursiveArrayOnlyIterator;

/**
 * @property int $id
 * @property string $name
 * @property string $event
 * @property string $url
 * @property string $content
 * @property int|null $game_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Game|null $game
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Webhook newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Webhook newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Webhook query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Webhook whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Webhook whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Webhook whereEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Webhook whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Webhook whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Webhook whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Webhook whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Webhook whereUrl($value)
 * @mixin \Eloquent
 */
class Webhook extends Model
{
    protected $guarded = [];

    public function game() {
        return $this->belongsTo(Game::class);
    }

    public function getMorphClass(): string {
        return 'webhook';
    }

    static function sendEvent(string $event, array $args=[], ?int $gameId=null, ?string $content=null) {
        $webhooks = Webhook::where("event_{$event}", true)
            ->where(function($q) use ($gameId) {
                $q->whereNull('game_id')
                    ->when(isset($gameId), fn($q) => $q->orWhere('game_id', $gameId));
            })
            ->get();

        foreach ($args as $k => $v) {
            if (is_object($v)) {
                $args[$k] = $v->toArray();
            }
        }

        $content ??= __("api.webhook_event_{$event}");
        foreach (new NestedKeyIterator(new RecursiveArrayOnlyIterator($args)) as $key => $value) {
            if (!$value || $value instanceof MissingValue)
                $value = '';
            if (is_object($value) && !method_exists($value, '__toString'))
                continue;

            $value = Utils::discordEscape($value);
            $content = str_replace("{{$key}}", $value, $content);
        }

        $args['event'] = $event;
        $args['content'] = $content;

        self::send($webhooks, $args);
    }

    static function sendModEvent(Mod $mod, string $event, array $args=[], ?string $content=null){
        $siteUrl = env('FRONTEND_URL');
        $user = Auth::user();

        self::sendEvent($event, [
            'mod_url' => "{$siteUrl}/mod/{$mod->id}",
            'mod' => $mod,
            'user' => $user,
            ...$args
        ], $mod->game_id, $content);
    }

    /**
     * Send a message to a webhook URL
     * Handles some form of markdown escaping to avoid webhook mentioning users on sites like Discord (only args, careful with the message!)
     *
     * If you are handling the message yourself, the content is in "content".
     *
     * @param Webhook[]|Collection<int, Webhook> $webhooks
     */
    private static function send(iterable $webhooks, array $args=[]) {
        foreach ($webhooks as $webhook) {
            try {
                $req = WebhookCall::create()
                    ->doNotSign() // Maybe eventually do this, but not sure
                    ->useTimestamp()
                    ->maximumTries(5)
                    ->url($webhook->url);

                if (empty($webhook->custom_template)) {
                    $req->payload([
                        'webhook_id' => $webhook->id,
                        'webhook_name' => $webhook->name,
                        ...$args
                    ]);
                } else {
                    $customTemplate = $webhook->custom_template;

                    foreach (new NestedKeyIterator(new RecursiveArrayOnlyIterator($args)) as $key => $value) {
                        if (!$value || $value instanceof MissingValue)
                            $value = '';
                        if (is_object($value) && !method_exists($value, '__toString'))
                            continue;

                        $value = substr(json_encode($value), 1, -1);
                        $customTemplate = str_replace("{{$key}}", $value, $customTemplate);
                    }

                    if (json_validate($customTemplate)) {
                        $req->payload(json_decode($customTemplate, true));
                    }
                }

                $req->dispatch();
            } catch (\Throwable $th) {
                \Log::warning('Failed to send webhook', [
                    'webhook_id' => $webhook->id,
                    'event' => $args['event'],
                    'error' => $th->getMessage()
                ]);
            }
        }
	}
}
