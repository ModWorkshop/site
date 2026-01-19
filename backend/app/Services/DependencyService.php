<?php
namespace App\Services;

use App\Models\Dependency;
use Illuminate\Http\Request;

class DependencyService {
    /**
     * Adds a dependency to a mod or instructions template
     */
    public static function store(Request $request, $dependable)
    {
        $val = $request->validate([
            'name' => 'string|exclude_with:mod_id|required_without:mod_id|min:3|max:150',
            'url' => 'url|exclude_with:mod_id|required_without:mod_id|min:3|max:1000',
            'mod_id' => 'integer|min:0|required_without:name,url|exists:mods,id',
            'optional' => 'boolean',
            'order' => 'integer'
        ]);

        $dependency = null;
        if (isset($val['mod_id'])) {
            if (!$dependable->dependencies()->where('mod_id', $val['mod_id'])->exists()) {
                $val['offsite'] = false;
                $dependency = $dependable->dependencies()->create($val);
            }
        } else {
            $val['offsite'] = true;
            $dependency = $dependable->dependencies()->create($val);
        }

        if (!isset($dependency)) {
            abort(409, 'already exists');
        }

        $dependency->loadMissing('mod');

        return $dependency;
    }

    /**
     * Updates dependency data
     */
    public static function update(Request $request, $dependable, Dependency $dependency)
    {
        $val = null;
        if ($dependency->offsite) {
            $val = $request->validate([
                'name' => 'string|min_strict:3|max:150',
                'url' => 'url|min:3|max:1000',
                'optional' => 'boolean',
                'order' => 'integer'
            ]);
        } else {
            $val = $request->validate([
                'optional' => 'boolean',
                'mod_id' => 'integer|min:0|exists:mods,id',
                'order' => 'integer'
            ]);
        }

        $dependency->update($val);
        $dependency->load('mod');

        return $dependency;
    }

    /**
     * Deletes the dependency
     */
    public static function destroy(Request $request, $dependable, Dependency $dependency)
    {
        $dependency->delete();
    }
}
