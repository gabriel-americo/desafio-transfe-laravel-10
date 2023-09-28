<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function postAuthenticate(string $provider)
    {
        $providers = ['users', 'retailer'];

        if (!in_array($provider, $providers)) {
            return response()->json(['errors' => ['main' => ['Wrong provider provided']]], 422);
        }

        $provider = $this->getProvider($provider);

        return 'o provider escolhido foi ' . $provider;
    }

    public function getProvider(string $provider): Authorizable
    {
        $providers = [
            'user' => User::class,
            'retailer' => Retailer::class,
        ];

        if (array_key_exists($provider, $providers)) {
            return new $providers[$provider]();
        } else {
            throw new \Exception('Provider not found');
        }
    }
}
