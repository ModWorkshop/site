<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Arr;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $query = Setting::query();

        $user = $this->user();
        if (!$user?->hasPermission('admin')) {
            $query->where('public', true);
        }

        return Arr::pluck($query->get(), 'value', 'name');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->authorize('update', Setting::class);

        $val = $request->validate([
            'max_file_size' => 'integer',
            'mod_storage_size' => 'integer',
            'image_max_file_size' => 'integer',
            'mod_max_image_count' => 'integer',
            'discord_webhook' => 'string|max:255'
        ]);

        foreach ($val as $key => $value) {
            Setting::where('name', $key)->where('value', '!=', $value)->update(['value' => $value]);
        }

        Setting::flushQueryCache();
    }
}
