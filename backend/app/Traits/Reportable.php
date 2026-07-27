<?php

namespace App\Traits;

use App\Models\Report;
use App\Models\User;
use App\Models\Webhook;
use App\Services\Utils;
use Auth;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Reportable {
    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function report(string $reason)
    {
        $user = Auth::user();
        $data = [];

        if (isset($this->saveToReport)) {
            foreach ($this->saveToReport as $key) {
                $data[$key] = $this->$key;
            }
        }

        $report = new Report([
            'name' => $this->name,
            'user_id' => $user->id,
            'game_id' => $this->game_id,
            'reason' => $reason,
            'reported_user_id' => $this::class == 'App\Models\User' ? $this->id : $this->user_id,
            'data' => $data
        ]);

        $siteUrl = env('FRONTEND_URL');

        $webhooks = Webhook::where('event', 'report_new')
            ->where(fn($q) => $q->whereNull('game_id')->orWhere('game_id', $this->game_id))
            ->get();

        $url = match ($this::class) {
            User::class => "user/{$this->id}",
            Mod::class => "mod/{$this->id}",
            Comment::class => "{$this->commentable_type}/{$this->commentable_id}/post/{$this->id}",
            default => null
        };

        Utils::sendWebhook($webhooks, [
            'reason' => $reason,
            'resource_url' => "{$siteUrl}/{$url}",
            'reporter_name' => $user->name,
            'reporter_id' => $user->id,
            'reported_user_id' => $report->reported_user_id,
            'reporter_link' => "{$siteUrl}/user/{$user->id}",
            'reported_user_link' => "{$siteUrl}/user/{$report->reported_user_id}",
        ]);

        $this->withSecureConstraints(fn() => $this->reports()->save($report));
        $report->save();
    }
}
