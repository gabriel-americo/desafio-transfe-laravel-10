<?php

namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\Controller;
use App\Repositories\Transaction\TransactionRepository;
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
        $request->validate([
            'provider' => 'required|in:user,retailer',
            'payee_id' => 'required'
        ]);

        $result = $this->repository->handle();

        return response()->json($result);
    }
}
