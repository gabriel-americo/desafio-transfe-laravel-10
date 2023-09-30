<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function postAuthenticate(Request $request, string $provider)
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ];

        $this->validate($request, $rules);

        $providers = ['users', 'retailer'];

        if (!in_array($provider, $providers)) {
            return response()->json(['errors' => ['main' => 'Wrong provider provided']], 422);
        }

        $selectedProvider = $this->getProvider($provider);
        $model = $selectedProvider->where('email', '=', $request->input('email'))->first();

        if (!$model) {
            return response()->json(['errors' => ['main' => 'Wrong credentials']], 401);
        }

        if(!Hash::check($request->input('password'), $model->password)) {
            return response()->json(['errors' => ['main' => 'Wrong credentials']], 401);
        }

        return 'o provider escolhido foi ' . $provider;
    }

    public function getProvider(string $provider)
    {
        $providers = [
            'user' => User::class,
            'retailer' => Retailer::class,
        ];

        if (array_key_exists($provider, $providers)) {
            return new $providers[$provider]();
        } else {
            throw new Exception('Provider not found');
        }
    }
}
