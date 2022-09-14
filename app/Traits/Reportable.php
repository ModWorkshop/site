<?php

namespace App\Traits;

use App\Models\Report;
use Auth;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Reportable {
    public $saveToReport = [];

    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function report(string $reason)
    {
        $user = Auth::user();
        $data = [];

        foreach ($this->saveToReport as $key) {
            $data[$key] = $this->$key;
        }

        $report = new Report([
            'name' => $this->name,
            'user_id' => $user->id,
            'game_id' => $this->game_id,
            'reason' => $reason,
            'data' => $data
        ]);

        $this->reports()->save($report);
        $report->save();
    }
}