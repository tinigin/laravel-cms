<?php

namespace  LaravelCms\Database\Seeders;

use Illuminate\Database\Seeder;

class LaravelCmsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class="LaravelCms\Database\Seeders\LaravelCmsSeeder"
     *
     * run another class
     * php artisan db:seed --class="LaravelCms\Database\Seeders\UsersTableSeeder"
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            //AttachmentsTableSeeder::class,
            //UsersTableSeeder::class,
        ]);
    }
}
