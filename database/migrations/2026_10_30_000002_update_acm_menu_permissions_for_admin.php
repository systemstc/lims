<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Menu;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Helper to add role to comma-separated permission string
        $addRole = function ($currentRoles, $roleId) {
            $roles = $currentRoles ? array_filter(explode(',', (string)$currentRoles)) : [];
            if (!in_array((string)$roleId, $roles)) {
                $roles[] = (string)$roleId;
            }
            sort($roles);
            return implode(',', $roles);
        };

        // 1. Menu 42: Dashboard
        $dashboard = Menu::find(42);
        if ($dashboard) {
            $dashboard->m05_role_view   = $addRole($dashboard->m05_role_view, 1);
            $dashboard->m05_role_create = $addRole($dashboard->m05_role_create, 1);
            $dashboard->m05_role_edit   = $addRole($dashboard->m05_role_edit, 1);
            $dashboard->m05_role_delete = $addRole($dashboard->m05_role_delete, 1);
            $dashboard->save();
        }

        // 2. Report menus (27: Report, 28: Manage Reports, 30: Test Templates, 31: Audit Trail)
        $reportMenus = [27, 28, 30, 31];
        foreach ($reportMenus as $menuId) {
            $menu = Menu::find($menuId);
            if ($menu) {
                $menu->m05_role_view   = $addRole($menu->m05_role_view, 1);
                $menu->m05_role_view   = $addRole($menu->m05_role_view, 3);
                $menu->m05_role_create = $addRole($menu->m05_role_create, 1);
                $menu->m05_role_create = $addRole($menu->m05_role_create, 3);
                $menu->m05_role_edit   = $addRole($menu->m05_role_edit, 1);
                $menu->m05_role_edit   = $addRole($menu->m05_role_edit, 3);
                $menu->m05_role_delete = $addRole($menu->m05_role_delete, 1);
                $menu->m05_role_delete = $addRole($menu->m05_role_delete, 3);
                $menu->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reversal logic if required
    }
};

