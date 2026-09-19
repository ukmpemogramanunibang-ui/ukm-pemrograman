<?php

namespace App\Http\Controllers;

use App\Models\LoginHistory;
use App\Http\Requests\StoreLoginHistoryRequest;
use App\Http\Requests\UpdateLoginHistoryRequest;

class LoginHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLoginHistoryRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(LoginHistory $loginHistory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LoginHistory $loginHistory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLoginHistoryRequest $request, LoginHistory $loginHistory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LoginHistory $loginHistory)
    {
        //
    }
}
