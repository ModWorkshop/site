<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Models\Forum;
use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

class ForumController extends Controller
{
    public function __construct() {
        $this->authorizeGameResource(Forum::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(FilteredRequest $request)
    {
        $val = $request->val();

        return JsonResource::collection(Forum::queryGet($val));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show(Forum $forum)
    {
        return $forum;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }
}
