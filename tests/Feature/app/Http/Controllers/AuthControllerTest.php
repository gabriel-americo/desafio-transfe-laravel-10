<?php

namespace Feature\app\Http\Controllers;

use App\Models\User;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    public function testUserShouldNotAuthenticateWithWrongProvider()
    {
        $payload = [
            'email' => 'gabrielamerico90@gmail.com',
            'password' => 'secret123',
        ];

        $request = $this->post(route('authenticate', ['provider' => 'errado']), $payload);

        $request
            ->assertStatus(422)
            ->assertJson([
                'errors' => ['main' => 'Wrong provider provided']
            ]);
    }

    public function testUserShouldBeDeniedIfNotRegistered()
    {
        $payload = [
            'email' => 'teste@exemple.com',
            'password' => 'secret123',
        ];

        $request = $this->post(route('authenticate', ['provider' => 'user']), $payload);
        $request
            ->assertStatus(401)
            ->assertJson(['errors' => ['main' => 'Wrong credentials']]);
    }

    public function testUserShouldSendWrongPassword()
    {
        $user = User::factory()->create();
        $payload = [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ];

        $request = $this->post(route('authenticate', ['provider' => 'user']), $payload);
        $request
            ->assertStatus(401)
            ->assertJson(['errors' => ['main' => 'Wrong credentials']]);
    }

    public function testUserCanAuthenticate()
    {
        $this->artisan('passport:install');
        $user = User::factory()->create();
        $payload = [
            'email' => $user->email,
            'password' => 'secret123',
        ];

        $request = $this->post(route('authenticate', ['provider' => 'user']), $payload);
        $request
            ->assertStatus(200)
            ->assertJsonStructure(['access_token', 'expires_at', 'provider']);
    }
}
