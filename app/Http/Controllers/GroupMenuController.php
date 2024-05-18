<?php

namespace App\Http\Controllers;

use App\Models\GroupMenu;
use Illuminate\Http\Request;

class GroupMenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ('Hola Mundo - hello world');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\GroupMenu  $groupMenu
     * @return \Illuminate\Http\Response
     */
    public function show(GroupMenu $groupMenu)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\GroupMenu  $groupMenu
     * @return \Illuminate\Http\Response
     */
    public function edit(GroupMenu $groupMenu)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\GroupMenu  $groupMenu
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, GroupMenu $groupMenu)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\GroupMenu  $groupMenu
     * @return \Illuminate\Http\Response
     */
    public function destroy(GroupMenu $groupMenu)
    {
        //
    }
}
