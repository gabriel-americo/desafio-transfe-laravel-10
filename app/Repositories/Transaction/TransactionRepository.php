<?php

namespace App\Repositories\Transaction;

use App\Events\SendNotification;
use App\Exceptions\IdleServiceException;
use App\Exceptions\NoMoneyException;
use App\Exceptions\TransactionDeniedException;

use App\Models\Retailer;
use App\Models\Transactions\Transaction;
use App\Models\Transactions\Wallet;
use App\Models\User;
use App\Services\MockyService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\InvalidDataProviderException;
use Ramsey\Uuid\Uuid;

class TransactionRepository
{
    public function handle(array $data): Transaction
    {
        if (!$this->guardCanTransfer()) {
            throw new TransactionDeniedException('Retailer is not authorized to make transactions', 401);
        }

        if (!$payee =  $this->retrievePayer($data)) {
            throw new InvalidDataProviderException('Invalida data provider', 404);
        }

        $myWallet = Auth::guard($data['provider'])->user()->wallet;

        if (!$this->checkUserBalance($myWallet, $data['amount'])) {
            throw new NoMoneyException('No money in your wallet', 422);
        }

        if (!$this->isServiceAbleToMakeTransaction()) {
            throw new IdleServiceException('Service is not responding. Try again later.', 401);
        }

        return $this->makeTransaction($payee, $data);
    }

    public function guardCanTransfer(): bool
    {
        if (Auth::guard('users')->check()) {
            return true;
        }

        if (Auth::guard('retailers')->check()) {
            return false;
        }

        throw new InvalidDataProviderException('Provider not found');
    }

    public function getProvider(string $provider)
    {
        $providers = [
            'users' => new User(),
            'retailers' => new Retailer(),
        ];

        if (array_key_exists($provider, $providers)) {
            return new $providers[$provider]();
        } else {
            throw new InvalidDataProviderException('Provider not found', 422);
        }
    }

    private function checkUserBalance(Wallet $wallet, $money)
    {
        return $wallet->balance >= $money;
    }

    private function retrievePayer(array $data)
    {
        try {
            $model = $this->getProvider($data['provider']);
            return $model->find($data['payee_id']);
        } catch (\Exception $e) {
            return false;
        }
    }

    private function makeTransaction($payee, array $data)
    {
        $payload = [
            'id' => Uuid::uuid4()->toString(),
            'payer_wallet_id' => Auth::guard($data['provider'])->user()->wallet->id,
            'payee_wallet_id' => $payee->wallet->id,
            'amount' => $data['amount']
        ];

        return DB::transaction(function () use ($payload) {

            $transaction = Transaction::create($payload);

            $transaction->walletPayer->withdraw($payload['amount']);
            $transaction->walletPayer->deposit($payload['amount']);

            event(new SendNotification($transaction));

            return $transaction;
        });
    }

    private function isServiceAbleToMakeTransaction(): bool
    {
        $service = app(MockyService::class)->authorizeTransaction();
        return $service['message'] == 'Autorizado';
    }
}
