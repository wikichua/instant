<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class SeedData extends Migration
{
    public function up()
    {
        $user_id = 1;
        // create default admin user
        $user = app(config('auth.providers.users.model'))->create([
            'name' => 'Admin',
            'email' => 'admin@email.com',
            'password' => Hash::make('admin123'),
            'type' => 'Admin',
        ]);
        $user->created_by = $user_id;
        $user->updated_by = $user_id;
        $user->save();
        // create default admin role
        app(config('instant.Models.Role'))->create([
            'name' => 'Admin',
            'admin' => true,
            'created_by' => $user_id,
            'updated_by' => $user_id,
        ]);
        // give default admin user default admin role
        $user->roles()->attach(app(config('instant.Models.Role'))->where('admin', true)->first()->id);

        app(config('auth.providers.users.model'))->create([
            'created_by' => $user_id,
            'updated_by' => $user_id,
            'name' => 'User',
            'email' => 'user@email.com',
            'password' => Hash::make('admin123'),
        ]);

        app(config('instant.Models.Permission'))->createGroup('Audits', ['Read Audits'], $user_id);
        app(config('instant.Models.Permission'))->createGroup('Admin Panel', ['access-admin-panel'], $user_id);
        app(config('instant.Models.Permission'))->createGroup('Permissions', ['create-permissions', 'read-permissions', 'update-permissions', 'delete-permissions', 'Replicate Permissions'], $user_id);
        app(config('instant.Models.Permission'))->createGroup('Roles', ['create-roles', 'read-roles', 'update-roles', 'delete-roles'], $user_id);
        app(config('instant.Models.Permission'))->createGroup('Users', ['create-users', 'read-users', 'Update Users', 'delete-users', 'update-users-password'], $user_id);
        if (Schema::hasTable('personal_access_tokens')) {
            app(config('instant.Models.Permission'))->createGroup('Personal Access Token', ['create-personal-access-token', 'read-personal-access-token', 'delete-personal-access-token'], $user_id);
        }

        app(config('instant.Models.Permission'))->createGroup('Settings', ['create-settings', 'read-settings', 'update-settings', 'delete-settings'], $user_id);

        app(config('instant.Models.Setting'))->create([
            'created_by' => $user_id, 'updated_by' => $user_id,
            'key' => 'permission_groups',
            'value' => ['Admin Panel' => 'Admin Panel', 'Permission' => 'Permission', 'Setting' => 'Setting', 'Role' => 'Role', 'User' => 'User', 'Audit' => 'Audit', 'System Log' => 'System Log'],
        ]);

        app(config('instant.Models.Setting'))->create(['created_by' => $user_id, 'updated_by' => $user_id, 'key' => 'user_types', 'value' => ['Admin' => 'Admin', 'User' => 'User']]);
        app(config('instant.Models.Setting'))->create(['created_by' => $user_id, 'updated_by' => $user_id, 'key' => 'user_status', 'value' => ['A' => 'Active', 'I' => 'Inactive']]);
        app(config('instant.Models.Setting'))->create(['created_by' => $user_id, 'updated_by' => $user_id, 'key' => 'locales', 'value' => ['en' => 'EN']]);
        app(config('instant.Models.Setting'))->create(['created_by' => $user_id, 'updated_by' => $user_id, 'key' => 'report_status', 'value' => ['A' => 'Active', 'I' => 'Inactive']]);
        app(config('instant.Models.Setting'))->create(['created_by' => $user_id, 'updated_by' => $user_id, 'key' => 'cronjob_status', 'value' => ['A' => 'Active', 'I' => 'Inactive']]);
        app(config('instant.Models.Permission'))->createGroup('Reports', ['create-reports', 'read-reports', 'update-reports', 'delete-reports', 'export-reports'], $user_id);
        app(config('instant.Models.Setting'))->create(['created_by' => $user_id, 'updated_by' => $user_id, 'key' => 'brand_status', 'value' => ['A' => 'Published', 'P' => 'Pending', 'E' => 'Expired']]);
        app(config('instant.Models.Permission'))->createGroup('Brands', ['read-brands', 'update-brands'], $user_id);
        app(config('instant.Models.Permission'))->createGroup('Components', ['read-components'], $user_id);
        app(config('instant.Models.Setting'))->create(['created_by' => $user_id, 'updated_by' => $user_id, 'key' => 'page_status', 'value' => ['A' => 'Published', 'P' => 'Pending', 'E' => 'Expired']]);
        app(config('instant.Models.Permission'))->createGroup('Pages', ['create-pages', 'read-pages', 'update-pages', 'delete-pages', 'replicate-pages'], $user_id);
        app(config('instant.Models.Setting'))->create(['created_by' => $user_id, 'updated_by' => $user_id, 'key' => 'nav_status', 'value' => ['A' => 'Active', 'I' => 'Inactive']]);
        app(config('instant.Models.Permission'))->createGroup('Navs', ['create-navs', 'read-navs', 'update-navs', 'delete-navs'], $user_id);
        app(config('instant.Models.Setting'))->create(['created_by' => $user_id, 'updated_by' => $user_id, 'key' => 'carousel_tags', 'value' => ['new' => 'New', 'hot' => 'Hot', 'recommended' => 'Recommended']]);
        app(config('instant.Models.Setting'))->create(['created_by' => $user_id, 'updated_by' => $user_id, 'key' => 'carousel_status', 'value' => ['A' => 'Active', 'I' => 'Inactive']]);
        app(config('instant.Models.Permission'))->createGroup('Carousels', ['create-carousels', 'read-carousels', 'update-carousels', 'delete-carousels'], $user_id);
        app(config('instant.Models.Permission'))->createGroup('Files', ['upload-files', 'rename-files', 'delete-files', 'copy-files'], $user_id);
        app(config('instant.Models.Permission'))->createGroup('Folders', ['create-folders', 'rename-folders', 'delete-folders', 'copy-folders'], $user_id);
    }

    public function down()
    {
        app(config('instant.Models.Permission'))->whereIn('group', [
            'Audits',
            'Admin Panel',
            'Roles',
            'Users',
            'Personal Access Token',
            'Permissions',
            'Reports',
            'Brands',
            'Components',
            'Pages',
            'Navs',
            'Carousels',
            'Settings',
        ])->delete();

        app(config('instant.Models.Setting'))->whereIn('key', [
            'carousel_tags',
            'carousel_status',
            'nav_status',
            'page_status',
            'brand_status',
            'cronjob_status',
            'report_status',
            'permission_groups',
            'user_types',
            'user_status',
            'locales',
        ])->delete();
    }
}
