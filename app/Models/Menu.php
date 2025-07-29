<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'm05_menus';
    protected $primaryKey = 'm05_menu_id';
    protected $fillable = [
        'm05_title',
        'm05_route_view',
        'm05_route_create',
        'm05_route_edit',
        'm05_route_delete',
        'm05_role_view_',
        'm05_role_create',
        'm05_role_edit',
        'm05_role_delete',
        'm05_icon',
        'm05_parent_id',
        'm05_is_default',
        'm05_has_submenu',
        'm05_status'
    ];

    public function children()
    {
        return $this->hasMany(Menu::class, 'm05_parent_id', 'm05_menu_id');
    }
}
