<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\APIService;
use Arr;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function __construct() {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return APIService::getSettings();
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
        $val = $request->validate([
            'max_file_size' => 'integer',
            'mod_storage_size' => 'integer',
            'image_max_file_size' => 'integer',
            'mod_max_image_count' => 'integer',
            'discord_webhook' => 'string|nullable|max:255'
        ]);

        $val['discord_webhook'] ??= '';

        foreach ($val as $key => $value) {
            Setting::where('name', $key)->where('value', '!=', $value)->update(['value' => $value]);
        }

        Setting::flushQueryCache();
    }
}
