<?php

namespace App\Repositories\Transaction;

use App\Exceptions\NoMoneyException;
use App\Exceptions\TransactionDeniedException;
use App\Models\Retailer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\InvalidDataProviderException;

class TransactionRepository
{
    public function handle(array $data): array
    {
        if (!$this->guardCanTransfer()) {
            throw new TransactionDeniedException('Retailer is not authorized to make transactions', 401);
        }

        $model = $this->getProvider($data['provider']);

        $user = $model->findOrFail($data['payee_id']);

        if(!$this->checkUserBalance($user, $data['amount'])) {
            throw new NoMoneyException('No money in your wallet', 422);
        }

        return [];
    }

    public function guardCanTransfer(): bool
    {
        if (Auth::guard('users')->check()) {
            return true;
        }

        if (Auth::guard('retailer')->check()) {
            return false;
        }

        throw new InvalidDataProviderException('Provider not found');
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
            throw new \Exception('Provider not found');
        }
    }

    private function checkUserBalance($user, $money)
    {
        return $user->wallet->balance >= $money;
    }
}
