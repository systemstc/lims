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

        // 1. Menu 42: Dashboard - Grant RC role (Role 2) view/create/edit/delete in ACM
        $dashboard = Menu::find(42);
        if ($dashboard) {
            $dashboard->m05_role_view   = $addRole($dashboard->m05_role_view, 2);
            $dashboard->m05_role_create = $addRole($dashboard->m05_role_create, 2);
            $dashboard->m05_role_edit   = $addRole($dashboard->m05_role_edit, 2);
            $dashboard->m05_role_delete = $addRole($dashboard->m05_role_delete, 2);
            $dashboard->save();
        }

        // 2. Fix typos in m05_menus titles
        // ID 19: "Sample Regsitration" -> "Sample Registration"
        $menu19 = Menu::find(19);
        if ($menu19 && str_contains($menu19->m05_title, 'Regsitration')) {
            $menu19->m05_title = str_replace('Regsitration', 'Registration', $menu19->m05_title);
            $menu19->save();
        }

        // ID 25: "Allottment" -> "Allotment"
        $menu25 = Menu::find(25);
        if ($menu25 && $menu25->m05_title === 'Allottment') {
            $menu25->m05_title = 'Allotment';
            $menu25->save();
        }

        // ID 38: "Verifivation" -> "Verification"
        $menu38 = Menu::find(38);
        if ($menu38 && str_contains($menu38->m05_title, 'Verifivation')) {
            $menu38->m05_title = str_replace('Verifivation', 'Verification', $menu38->m05_title);
            $menu38->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reversal logic
    }
};

