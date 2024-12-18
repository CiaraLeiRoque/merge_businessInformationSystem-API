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

        // Generate verification token
        $verificationToken = Str::random(60);

        // Create the subscriber
        $subscriber = Subscribers::create([
            'email' => $request->email,
            'verification_token' => $verificationToken,
        ]);

        // Generate verification URL
        $verificationUrl = url("/verify-subscription?token={$verificationToken}&email={$subscriber->email}");

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

    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
        ]);

        $subscriber = Subscribers::where('email', $request->email)
            ->where('verification_token', $request->token)
            ->first();

        if (!$subscriber) {
            return response('Invalid verification link or token.', 400);
        }

        $subscriber->update([
            'email_verified_at' => now(),
            'verification_token' => null,
        ]);

        return redirect('/email-verified');
    }

    public function unsubscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $subscriber = Subscribers::where('email', $request->email)
            ->whereNotNull('email_verified_at') // Ensure the user is subscribed
            ->first();

        if (!$subscriber) {
            return response()->json(['message' => 'Subscriber not found or already unsubscribed.'], 404);
        }

        // Set email_verified_at to null to mark as unsubscribed
        $subscriber->update(['email_verified_at' => null]);

        return response()->json(['message' => 'You have successfully unsubscribed.'], 200);
    }


}
