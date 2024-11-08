<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TokenController extends Controller
{
    public function __construct() {
        //Handled
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->user()->tokens()->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $val = $request->validate([
            'name' => 'string'
        ]);
    
        $token = $this->user()->createToken($val['name'], ['mods:read']);
        return $token->plainTextToken;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->user()->tokens()->where('id', $tokenId)->firstOrFail();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->user()->tokens()->where('id', $tokenId)->delete();
    }
}
