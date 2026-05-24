<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $this->migrateCart();

            if (auth()->user()->isAdmin()) {
                return redirect()->intended('/admin');
            }

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden.',
        ])->onlyInput('email');
    }

    protected function migrateCart(): void
    {
        $sessionId = session()->getId();
        $sessionCart = Cart::where('session_id', $sessionId)->whereNull('user_id')->first();

        if (!$sessionCart || $sessionCart->items->isEmpty()) {
            return;
        }

        $userCart = Cart::where('user_id', auth()->id())->first();

        if ($userCart) {
            foreach ($sessionCart->items as $item) {
                $existingItem = $userCart->items()->where('product_id', $item->product_id)->first();
                if ($existingItem) {
                    $existingItem->increment('quantity', $item->quantity);
                } else {
                    $userCart->items()->create([
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                    ]);
                }
            }
            $sessionCart->items()->delete();
            $sessionCart->delete();
        } else {
            $sessionCart->update(['user_id' => auth()->id(), 'session_id' => null]);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
