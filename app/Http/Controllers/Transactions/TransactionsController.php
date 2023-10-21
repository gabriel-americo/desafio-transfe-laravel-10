<?php

namespace App\Http\Controllers\Transactions;

use App\Exceptions\IdleServiceException;
use App\Exceptions\NoMoneyException;
use App\Exceptions\TransactionDeniedException;

use App\Http\Controllers\Controller;
use App\Repositories\Transaction\TransactionRepository;
use PHPUnit\Framework\InvalidDataProviderException;
use Illuminate\Http\Request;

class TransactionsController extends Controller
{
    private $repository;

    public function __construct(TransactionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function postTransaction(Request $request)
    {
        try {
            $request->validate([
                'provider' => 'required|in:users,retailers',
                'payee_id' => 'required',
                'amount' => 'required|numeric'
            ]);

            $fields = $request->only(['provider', 'payee_id', 'amount']);

            $result = $this->repository->handle($fields);

            return response()->json($result);
        } catch (InvalidDataProviderException | NoMoneyException $exception) {
            return response()->json(['errors' => ['main' => $exception->getMessage()]], 422);
        } catch (TransactionDeniedException | IdleServiceException $exception) {
            return response()->json(['errors' => ['main' => $exception->getMessage()]], 401);
        } catch (\Exception $exception) {
            //dd($exception->getMessage());
        }
    }
}
