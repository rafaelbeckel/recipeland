<?php

use Recipeland\Data\User;
use Recipeland\Data\Role;
use Phinx\Seed\AbstractSeed;
use Recipeland\Data\Permission;

class AclSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        require __DIR__.'/../../bootstrap/Autoload.php';

        $container = require __DIR__.'/../../bootstrap/Config.php';

        $container->get('db');

        // Roles
        $chef = Role::firstOrCreate(
            ['name' => 'chef'],
            [
                'display_name' => 'Chef',
                'description' => 'Can create recipes, can edit his/her own recipes, cannot delete recipes',
            ]
        );

        $owner = Role::firstOrCreate(
            ['name' => 'restaurant_owner'],
            [
                'display_name' => 'Restaurant\'s Owner',
                'description' => 'Can create recipes, can edit all recipes, can delete all recipes',
            ]
        );

        // Permissions
        $create = Permission::firstOrCreate(
            ['name' => 'create_recipes'],
            [
                'display_name' => 'Create Recipes',
                'description' => 'User can create new recipes',
            ]
        );

        $edit = Permission::firstOrCreate(
            ['name' => 'edit_own_recipes'],
            [
                'display_name' => 'Edit User\'s Own Recipes',
                'description' => 'User can edit his own recipes',
            ]
        );

        $edit_all = Permission::firstOrCreate(
            ['name' => 'edit_all_recipes'],
            [
                'display_name' => 'Edit All Recipes',
                'description' => 'User can edit all recipes',
            ]
        );

        $delete = Permission::firstOrCreate(
            ['name' => 'delete_own_recipes'],
            [
                'display_name' => 'Delete User\'s Own Recipes',
                'description' => 'User can delete his own recipes',
            ]
        );

        $delete_all = Permission::firstOrCreate(
            ['name' => 'delete_all_recipes'],
            [
                'display_name' => 'Delete All Recipes',
                'description' => 'User can delete all recipes',
            ]
        );

        // Attach Permissions to Roles
        $chef->attachPermissions([$create, $edit]);
        $owner->attachPermissions([$create, $edit, $edit_all, $delete, $delete_all]);

        // Create Users
        $homer = User::firstOrCreate(
            ['username' => 'homer'],
            [
                'name' => 'Homer Simpson',
                'password' => password_hash('Marge1234!', PASSWORD_BCRYPT),
                'email' => 'homer@example.com',
            ]
        );

        $luigi = User::firstOrCreate(
            ['username' => 'luigi'],
            [
                'name' => 'Luigi Risotto',
                'password' => password_hash('Pasta1234!', PASSWORD_BCRYPT),
                'email' => 'luigi@example.com',
            ]
        );

        $burns = User::firstOrCreate(
            ['username' => 'burns'],
            [
                'name' => 'Montgomery Burns',
                'password' => password_hash('Money1234!', PASSWORD_BCRYPT),
                'email' => 'burns@example.com',
            ]
        );

        // Attach roles to users
        $luigi->attachRole($chef);
        $burns->attachRole($owner);
    }
}
