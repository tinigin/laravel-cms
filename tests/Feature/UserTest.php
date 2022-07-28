<?php

namespace LaravelCms\Tests\Feature;

use LaravelCms\Tests\TestFeatureCase;

class UserTest extends TestFeatureCase
{
    public function testRouteSystemsUsers(): void
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->get(route('platform.systems.users'));

        $response->assertOk()
            ->assertSee($this->createAdminUser()->name)
            ->assertSee($this->createAdminUser()->email);
    }

    public function testRouteDashboard(): void
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->get(route('cms.dashboard'));

        $response->assertOk();
    }
}
