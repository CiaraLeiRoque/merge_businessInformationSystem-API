<?php

namespace App\Http\Controllers;

use App\Models\Subscribers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\VerifySubscriptionMail;


class SubscriberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:subscriber,email',
        ]);

        // Create the subscriber
        $subscriber = Subscribers::create([
            'email' => $request->email,
        ]);

        // Generate verification token
        $verificationUrl = url("/verify-subscription?token=" . Str::random(60) . "&email={$subscriber->email}");

        // Send email
        Mail::to($subscriber->email)->send(new VerifySubscriptionMail($verificationUrl));

        return response()->json(['message' => 'Subscription successful! Please check your email for verification.'], 201);
    }   

    /**
     * Display the specified resource.
     */
    public function show(Subscribers $subscribers)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subscribers $subscribers)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subscribers $subscribers)
    {
        //
    }
}
