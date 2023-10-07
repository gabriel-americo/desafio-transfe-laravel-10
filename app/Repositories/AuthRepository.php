<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Hash;
use App\Models\Retailer;
use App\Models\User;
use Exception;
use Illuminate\Auth\AuthenticationException;
use PHPUnit\Framework\InvalidDataProviderException;

class AuthRepository
{
    public function authenticate($provider, $fields)
    {
        $providers = ['user', 'retailer'];

        if (!in_array($provider, $providers)) {
            throw new InvalidDataProviderException('Wrong provider provided');
        }

        $selectedProvider = $this->getProvider($provider);
        $model = $selectedProvider->where('email', '=', $fields['email'])->first();

        if (!$model || !Hash::check($fields['password'], $model->password)) {
            throw new AuthenticationException('Wrong credentials');
        }

        $token = $model->createToken($provider);

        return [
            'access_token' => $token->accessToken,
            'expires_at' => $token->token->expires_at,
            'provider' => $provider
        ];
    }

    public function getProvider(string $provider)
    {
        $providers = [
            'user' => new User(),
            'retailer' => new Retailer(),
        ];

        if (array_key_exists($provider, $providers)) {
            return new $providers[$provider]();
        } else {
            throw new Exception('Provider not found');
        }
    }
}
