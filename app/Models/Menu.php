<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\Permission\Traits\HasRoles;

class Menu extends Model
{
    use HasRoles;

    protected $fillable = [
        'name',
        'key',
        'path',
        'icon',
        'parent_id',
        'sort',
        'status',
    ];

    public function parent()
    {
        return $this->belongsTo(Menu::class);
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->with('children')->orderBy('sort')->oldest();
    }

    protected function roleIds() : Attribute
    {
        return Attribute::make(function () {
            return $this->roles()->pluck('id');
        });
    }

    public function guardName()
    {
        return 'api';
    }

    public static function sort($treeData)
    {
        $menus = Menu::query()->get();
        self::sortMenu($treeData, $menus);
    }

    public static function sortMenu($treeData, $menus, $parent = null) {
        $sort = 0;
        foreach ($treeData as $item) {
            $menu = $menus->where('id', $item['id'])->first();
            $menu->sort = $sort++;

            if (!is_null($parent) && $menu->parent_id != $parent->id) {
                $menu->parent_id = $parent->id;
            }
            $menu->save();

            if ($item['children'] ?? []) {
                self::sortMenu($item['children'], $menus, $menu);
            }
        }
    }
}
