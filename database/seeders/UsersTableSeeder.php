<?php

namespace  LaravelCms\Database\Seeders;

use Illuminate\Database\Seeder;
use LaravelCms\Models\Cms\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(User::class, 10)->create();
    }
}
