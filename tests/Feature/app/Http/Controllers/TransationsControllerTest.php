<?php

namespace Feature\app\Http\Controllers;

use App\Events\SendNotification;
use App\Models\Retailer;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class TransationsControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('passport:install');
    }

    public function testUserShouldBeExistingOnProviderToTransfer()
    {
        $user = User::factory()->create();

        $payload =
            [
                'provider' => 'users',
                'payee_id' => 'whatever',
                'amount' => 123
            ];

        // Faça a solicitação POST autenticada como o usuário
        $request = $this->actingAs($user, 'users')
            ->post(route('postTransaction'), $payload);

        // Verifique se a resposta tem um código de status 404 (Not Found)
        $request->assertStatus(404);
    }

    public function testUserShouldNotSendWrongProvider()
    {
        $user = User::factory()->create();

        $payload =
            [
                'provider' => 'errado',
                'payee_id' => 'whatever',
                'amount' => 123
            ];

        $request = $this->actingAs($user, 'users')
            ->post(route('postTransaction'), $payload);

        $request
            ->assertStatus(422)
            ->assertJson([
                'errors' => ['main' => 'Wrong provider provided']
            ]);
    }

    public function testUserShouldBeAValidUserToTransfer()
    {
        $user = User::factory()->create();

        $payload =
            [
                'provider' => 'users',
                'payee_id' => 'whatever',
                'amount' => 123
            ];

        $request = $this->actingAs($user, 'users')
            ->post(route('postTransaction'), $payload);

        $request->assertStatus(404);
    }

    public function testRetailerShouldNotTransfer()
    {
        $user = Retailer::factory()->create();

        $payload =
            [
                'provider' => 'users',
                'payee_id' => 'whatever',
                'amount' => 123
            ];

        $request = $this->actingAs($user, 'retailers')
            ->post(route('postTransaction'), $payload);

        $request->assertStatus(401);
    }

    public function testUserShouldHaveMoneyToPerformSomeTransaction()
    {
        $userPayer = User::factory()->create();
        $userPayed = User::factory()->create();

        $payload =
            [
                'provider' => 'users',
                'payee_id' => $userPayed->id,
                'amount' => 123
            ];

        $request = $this->actingAs($userPayer, 'users')
            ->post(route('postTransaction'), $payload);

        $request->assertStatus(422);
    }

    public function testUserCanTransferMoney()
    {
        Event::fakeFor(function () {
            $userPayer = User::factory()->create();
            $userPayer->wallet->deposit(1000);
            $userPayed = User::factory()->create();

            $payload = [
                'provider' => 'users',
                'payee_id' => $userPayed->id,
                'amount' => 100
            ];

            $request = $this->actingAs($userPayer, 'users')
                ->post(route('postTransaction'), $payload);

            $request->assertStatus(200);

            $this->assertDatabaseHas('wallets', [
                'id' => $userPayer->wallet->id,
                'balance' => 1000,
            ]);

            $this->assertDatabaseHas('wallets', [
                'id' => $userPayed->wallet->id,
                'balance' => 0,
            ]);

            Event::assertDispatched(SendNotification::class);
        });
    }
}
