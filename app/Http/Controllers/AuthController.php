<?php

namespace App\Http\Controllers;


use App\Repositories\AuthRepository;
use Illuminate\Auth\AuthenticationException;
use PHPUnit\Framework\InvalidDataProviderException;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private $repository;

    public function __construct(AuthRepository $repository)
    {
        $this->repository = $repository;
    }

    public function postAuthenticate(Request $request, string $provider)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|min:6'
            ]);

            $fields = $request->only(['email', 'password']);
            $result = $this->repository->authenticate($provider, $fields);

            return response()->json($result);
        } catch (AuthenticationException $exception) {
            return response()->json(['errors' => ['main' => $exception->getMessage()]], 401);
        } catch (InvalidDataProviderException $exception) {
            return response()->json(['errors' => ['main' => $exception->getMessage()]], 422);
        }
    }
}
