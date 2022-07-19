<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;


class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::firstOrCreate(['name' => 'profile-read','display_name' => 'Profile']);
        Permission::firstOrCreate(['name' => 'profile-update','display_name' => 'Profile']);
        Permission::firstOrCreate(['name' => 'user-read','display_name' => 'User']);
        Permission::firstOrCreate(['name' => 'user-create','display_name' => 'User']);
        Permission::firstOrCreate(['name' => 'user-update','display_name' => 'User']);
        Permission::firstOrCreate(['name' => 'user-delete','display_name' => 'User']);
        Permission::firstOrCreate(['name' => 'user-export','display_name' => 'User']);
        Permission::firstOrCreate(['name' => 'item-read','display_name' => 'Item']);
        Permission::firstOrCreate(['name' => 'item-create','display_name' => 'Item']);
        Permission::firstOrCreate(['name' => 'item-update','display_name' => 'Item']);
        Permission::firstOrCreate(['name' => 'item-delete','display_name' => 'Item']);
        Permission::firstOrCreate(['name' => 'item-export','display_name' => 'Item']);
        Permission::firstOrCreate(['name' => 'item-import','display_name' => 'Item']);
        Permission::firstOrCreate(['name' => 'customer-read','display_name' => 'Customer']);
        Permission::firstOrCreate(['name' => 'customer-create','display_name' => 'Customer']);
        Permission::firstOrCreate(['name' => 'customer-update','display_name' => 'Customer']);
        Permission::firstOrCreate(['name' => 'customer-delete','display_name' => 'Customer']);
        Permission::firstOrCreate(['name' => 'customer-export','display_name' => 'Customer']);
        Permission::firstOrCreate(['name' => 'customer-import','display_name' => 'Customer']);
        Permission::firstOrCreate(['name' => 'invoice-read','display_name' => 'Invoice']);
        Permission::firstOrCreate(['name' => 'invoice-create','display_name' => 'Invoice']);
        Permission::firstOrCreate(['name' => 'invoice-update','display_name' => 'Invoice']);
        Permission::firstOrCreate(['name' => 'invoice-delete','display_name' => 'Invoice']);
        Permission::firstOrCreate(['name' => 'invoice-export','display_name' => 'Invoice']);
    }
}
