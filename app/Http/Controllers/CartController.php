<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = $this->getCart();
        return view('cart.index', compact('cart'));
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        $product = Product::findOrFail($data['product_id']);

        if ($product->stock < $data['quantity']) {
            return back()->with('error', 'Stock insuficiente.');
        }

        $cart = $this->getOrCreateCart();

        $existingItem = $cart->items()->where('product_id', $product->id)->first();

        if ($existingItem) {
            $newQty = $existingItem->quantity + $data['quantity'];
            if ($newQty > $product->stock) {
                return back()->with('error', 'Stock insuficiente.');
            }
            $existingItem->update([
                'quantity' => $newQty,
                'price' => $product->currentPrice(),
            ]);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $data['quantity'],
                'price' => $product->currentPrice(),
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Producto agregado al carrito.');
    }

    public function update(Request $request, CartItem $cartItem)
    {
        $data = $request->validate([
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        if ($cartItem->product->stock < $data['quantity']) {
            return back()->with('error', 'Stock insuficiente.');
        }

        $cartItem->update(['quantity' => $data['quantity']]);

        return back()->with('success', 'Carrito actualizado.');
    }

    public function remove(CartItem $cartItem)
    {
        $cartItem->delete();
        return back()->with('success', 'Producto eliminado del carrito.');
    }

    private function getCart(): ?Cart
    {
        if (auth()->check()) {
            $cart = Cart::where('user_id', auth()->id())->with('items.product')->first();
            if ($cart) return $cart;
        }
        return Cart::where('session_id', session()->getId())->with('items.product')->first();
    }

    private function getOrCreateCart(): Cart
    {
        $cart = $this->getCart();
        if ($cart) return $cart;

        if (auth()->check()) {
            return Cart::create(['user_id' => auth()->id()]);
        }

        return Cart::create(['session_id' => session()->getId()]);
    }
}
