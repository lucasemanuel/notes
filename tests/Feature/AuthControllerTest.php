<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthControllerTest extends TestCase
{
    /** @test */
    public function should_return_token_at_login()
    {
        $user = factory(User::class)->create([
            'password' => 'password'
        ]);

        $response = $this->post('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $response->assertOk()
            ->assertJsonStructure(['access_token']);
    }

    /** @test */
    public function should_return_unauthorized_when_password_or_email_are_wrong()
    {
        $user = factory(User::class)->create([
            'password' => 'password'
        ]);

        $response = $this->post('/api/auth/login', [
            'email' => $user->email,
            'password' => 'PASSWORD'
        ]);

        $response->assertUnauthorized()
            ->assertJsonStructure(['message']);
    }

    /** @test */
    public function should_return_true_if_token_is_valid()
    {
        $user = factory(User::class)->create([
            'password' => 'password'
        ]);
        $token = JWTAuth::fromUser($user);

        $response = $this->actingAs($user)
            ->postJson(
                '/api/auth/check',
                ['password' => 'password'],
                ['Authorization' => 'Bearer ' . $token]
            );

        $response->assertOk()
            ->assertJson([
                'auth' => true
            ]);
    }
}