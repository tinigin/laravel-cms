<?php

namespace LaravelCms\Tests\Unit;

use Illuminate\Support\Facades\Auth;
use LaravelCms\Models\Cms\User;
use LaravelCms\Tests\TestUnitCase;

class UserTest extends TestUnitCase
{
    public function testHasCorrectInstance(): void
    {
        $user = $this->createUser();

        $this->assertNotNull($user);
        $this->assertInstanceOf(User::class, $user);
    }

    public function testCanGetNameTitle(): void
    {
        $user = $this->createUser();

        $this->assertNotNull($user->name);
    }

    public function testCanGetEMail(): void
    {
        $user = $this->createUser();

        $this->assertNotNull($user->email);
    }

    public function testCanIsActive(): void
    {
        $user = $this->createUser();

        $this->assertEquals($user->status_id, 1);
    }

    /**
     * @return User
     */
    private function createUser(): User
    {
        return User::factory()->create();
    }

    public function testLoginAs(): void
    {
        $user = $this->createUser();

        $this->actingAs($user);
        $this->assertEquals($user->id, Auth::id());
    }
}
