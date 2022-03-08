<?php

namespace Tests\Feature\Analytics;

use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class AnalyticsTest extends TestCase
{
    private $admin;
    private $user;
    private string $baseUrl;

    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->baseUrl = '/api';
        $this->admin = User::whereRole(User::ADMIN)->first();
        $this->user = User::whereRole(User::USER)->first();
    }

    public function testAdminCanGetAnalytics(): void
    {
        $url = $this->baseUrl . "/analytics";
        $this->actingAs($this->admin)->get($url)
            ->assertJson(fn(AssertableJson $json) =>
            $json
                ->where('success', true)
                ->where('message', trans('general.fetch'))
                ->has('data', fn($json) =>
                $json
                    ->has('removedProducts')
                    ->has('checkedOutProduct')
                    ->etc())->etc());
    }

    public function testOrdinaryUserCantGetAnalytics(): void
    {
        $url = $this->baseUrl . "/analytics";
        $this->actingAs($this->user)->get($url)
            ->assertJson(fn(AssertableJson $json) =>
            $json
                ->where('success', false)
                ->where('message', 'This action is unauthorized.')->etc());
    }
}