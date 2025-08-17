<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!DB::table('roles')->where('name', 'Member')->exists()) {
            DB::table('roles')->updateOrInsert(['id' => 1, 'name' => 'Member', 'order' => -1]);
            DB::select("SELECT SETVAL(pg_get_serial_sequence('roles', 'id'), (SELECT MAX(id) FROM roles));");
            DB::table('roles')->updateOrInsert(['name' => 'Admin', 'tag' => 'Admin', 'order' => 1]);
        }
        
        Permission::query()->delete();

        DB::table('permissions')->upsert([
            ['name' => 'admin', 'type' => 'global'],
            ['name' => 'moderate-users', 'type' => null],
            ['name' => 'manage-discussions', 'type' => null],
            ['name' => 'manage-categories', 'type' => null],
            ['name' => 'manage-forum-categories', 'type' => null],
            ['name' => 'manage-mods', 'type' => null],
            ['name' => 'manage-roles', 'type' => null],
            ['name' => 'manage-instructions', 'type' => null],
            ['name' => 'manage-tags', 'type' => null],
            ['name' => 'manage-documents', 'type' => null],
            ['name' => 'manage-game', 'type' => 'game'], //The only game-specific permission. It's like the 'admin' of games.
            ['name' => 'manage-games', 'type' => 'global'],
            ['name' => 'manage-users', 'type' => 'global'],
            ['name' => 'create-categories', 'type' => null],
            ['name' => 'create-mods', 'type' => null],
            ['name' => 'create-discussions', 'type' => null],
            ['name' => 'create-reports', 'type' => null],
            ['name' => 'delete-own-mod-comments', 'type' => null],
            ['name' => 'like-mods', 'type' => null],
        ], 'name', ['type']);

        Role::where('name', 'Admin')->first()->permissions()->attach(Permission::whereName('admin')->first()->id);
        Role::where('name', 'Member')->first()->permissions()->sync([
            Permission::whereName('create-mods')->first()->id,
            Permission::whereName('create-discussions')->first()->id,
            Permission::whereName('create-reports')->first()->id,
            Permission::whereName('delete-own-mod-comments')->first()->id,
            Permission::whereName('like-mods')->first()->id,
        ]);
    }
}
