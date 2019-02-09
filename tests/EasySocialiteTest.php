<?php

namespace Devmi\EasySocialite\Tests;



use Mockery;
use Illuminate\Support\Facades\Event;
use Devmi\EasySocialite\Tests\TestCase;
use Devmi\EasySocialite\Models\UserSocial;
use Devmi\EasySocialite\Tests\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Devmi\EasySocialite\Events\SocialAccountLinked;

class UserSocialTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authenticated_user_cannot_access_logins_routes()
    {
        $user = User::create([
            'name' => 'Adam',
            'email' => 'adam@example.com',
            'password' => bcrypt('secret'),
        ]);

        $this->actingAs($user)
            ->get('/login/github')
            ->assertRedirect('/home');
    }

    /** @test */
    public function diabled_provider_cannot_be_accessed()
    {
        $this->get('/login/linkedin')
        ->assertStatus(404);
    }

    /** @test */
    public function it_calls_the_redirect_method_on_the_provider()
    {
        $mockSocialite = Mockery::mock('Laravel\Socialite\Contracts\Factory');
        $this->app['Laravel\Socialite\Contracts\Factory'] = $mockSocialite;

        $provider = Mockery::mock('Laravel\Socialite\Contacts\Provider');

        $mockSocialite->shouldReceive('driver')
            ->once()
            ->andReturn($provider);

        $provider->shouldReceive('redirect')
            ->once()
            ->andReturn();

        $response = $this->get('/login/github');

    }

    /** @test */
    public function it_store_the_user_and_its_social_account()
    {
        $user = User::make([
            'id' => 1,
            'name' => 'adam',
            'email' => 'adam@example.com',
        ]);

        $provider = 'github';

        $this->mockSocialite(123, $user->name, $user->email);
        $response = $this->get("/login/{$provider}/callback");

        $this->assertDatabaseHas('users', [
            'name' => $user->name,
            'email' => $user->email,
        ]);

        $this->assertDatabaseHas('users_social', [
            'user_id' => 1,
            'provider_id' => 123,
            'provider' => $provider,
        ]);
    }

    /** @test */
    public function different_providers_can_be_linked_to_the_same_user()
    {
        $user = User::create([
            'id' => 1,
            'name' => 'adam',
            'email' => 'adam@example.com',
        ]);

        $user->social()->create([
            'user_id' => $user->id,
            'provider_id' => 123,
            'provider' => 'github',
        ]);

        $this->mockSocialite(456, $user->name, $user->email);
        $response = $this->get("/login/twitter/callback");

        $this->assertEquals(1, $user->fresh()->count());
        $this->assertEquals(2, $user->fresh()->social()->count());

    }

    /** @test */
    public function it_fires_an_event_when_a_new_provider_linked_to_a_user()
    {

        Event::fake();

        $this->mockSocialite();
        $provider = 'github';
        $this->get("/login/{$provider}/callback");

        Event::assertDispatched(SocialAccountLinked::class, function ($e) use($provider) {
            $this->assertInstanceOf('Laravel\Socialite\Two\User', $e->providerUser);
            $this->assertEquals($provider, $e->provider);
            $this->assertInstanceOf(config('easysocialite.model.path'), $e->user);
            return true;
        });
    }

    protected function mockSocialite($id = 123, $name = 'adam', $email = 'adam@example.com')
    {
        $mockSocialite = Mockery::mock('Laravel\Socialite\Contracts\Factory');
        $this->app['Laravel\Socialite\Contracts\Factory'] = $mockSocialite;
        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser
            ->shouldReceive('getId')
            ->andReturn($id)
            ->shouldReceive('getName')
            ->andReturn($name)
            ->shouldReceive('getEmail')
            ->andReturn($email);

        $provider = Mockery::mock('Laravel\Socialite\Contract\Provider');
        $provider->shouldReceive('user')->once()->andReturn($abstractUser);
        $mockSocialite->shouldReceive('driver')->once()->andReturn($provider);
    }

}
