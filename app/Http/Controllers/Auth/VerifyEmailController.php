<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Ichtrojan\Otp\Otp;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(Request $request)
    {

        $request->validate([
            "otp" => ["required", "string"]
        ]); 
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(
                config('app.frontend_url').'/dashboard?verified=1'
            );
        }

        $otpValidation = (new Otp)->validate($request->user()->email, $request->string("otp")); 
        
        if($otpValidation->status == false){
            return response()->json($otpValidation, 422); 
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended(
            config('app.frontend_url').'/dashboard?verified=1'
        );
    }
}
