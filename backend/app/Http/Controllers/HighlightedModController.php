<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\HighlightedMod;
use App\Models\Mod;
use Arr;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HighlightedModController extends Controller
{
    public function __construct() {
        $this->authorizeGameResource(HighlightedMod::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // TODO
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HighlightedMod $highlightedMod)
    {
        $mod = $highlightedMod->mod;

        $highlightedMod->delete();

        if (!HighlightedMod::where('mod_id', $mod->id)->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))->exists()) {
            $mod->update([
                'highlighted' => false
            ]);
        }
    }
}
