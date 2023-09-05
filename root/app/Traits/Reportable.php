<?php

namespace App\Traits;

use App\Models\Report;
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

        $this->reports()->save($report);
        $report->save();
    }
}