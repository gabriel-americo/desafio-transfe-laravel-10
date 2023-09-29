<?php

namespace Feature\app\Http\Controllers;

use App\Models\User;
use Database\Factories\UserFactory;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    public function createApplication()
    {
        return require './bootstrap/app.php';
    }

    public function testUserShouldNotAuthenticateWithWrongProvider()
    {
        $user = User::factory()->create();

        $payload = [
            'email' => $user->email,
            'password' => 'novo123',
        ];

        $request = $this->post(route('authenticate', ['provider' => 'deixa-o-sub']), $payload);

        $request->assertResponseStatus(422);
        $request->seeJson(['errors' => ['main' => 'Wrong provider provided']]);
    }

    public function testUserShouldBeDeniedIfNotRegistered()
    {
        $user = User::factory()->create();

        $payload = [
            'email' => $user->email,
            'password' => 'novo123',
        ];

        $request  = $this->post(route('authenticate', ['provider' => 'user']), $payload);
        $request->assertResponseStatus(401);
        $request->seeJson(['errors' => ['main' => 'Wrong credentials']]);
    }

    public function testUserShouldSendWrongPassword()
    {
        $user = User::factory()->create();

        dd($user);

        $payload = [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ];

        $request  = $this->post(route('authenticate', ['provider' => 'user']), $payload);
        $request->assertResponseStatus(401);
        $request->seeJson(['errors' => ['main' => 'Wrong credentials']]);
    }
}
