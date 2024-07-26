<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        // Save Favorite FCE if it's set.
        if ($request->filled('favorite_fce')) {
            $request->user()->favorite_fce = $request->input('favorite_fce');
        }

        $request->user()->save();

        return Redirect::route('profilo')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Add payment method to the user's profile.
     */
    public function addPaymentMethod(Request $request): RedirectResponse
    {
        // Get the Stripe user or create a new one.
        $stripeCustomer = $request->user()->createOrGetStripeCustomer();

        // Check if user has already a default payment method.
        if ($request->user()->hasDefaultPaymentMethod()) {
            // Set the default payment method.
            $request->user()->updateDefaultPaymentMethod($request->input('payment_method'));
            return Redirect::route('profilo')->with('status', 'payment-method-updated');
        } else {
            // Update the default payment method.
            $request->user()->addPaymentMethod($request->input('payment_method'));
            return Redirect::route('profilo')->with('status', 'payment-method-added');
        }
    }
}
