<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Menu;
use App\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class RolesController extends Controller
{
    public function index(Request $request)
    {
        $roles = Role::query()->with(['menus'])->latest()->paginate($request->page_size ?? 15);

        return RoleResource::collection($roles);
    }

    public function store(RoleRequest $request)
    {
        $role = Role::create(
            $request->only([
                'label',
                'name'
            ])
        );

        return RoleResource::make($role);
    }

    public function update(RoleRequest $request, Role $role)
    {
        $role->fill(
            $request->only([
                'label',
                'name'
            ])
        );

        $role->save();

        return RoleResource::make($role);
    }

    public function assignMenu(Request $request, Role $role)
    {
        $role->menus()->sync($request->menu_ids);

        return RoleResource::make($role);
    }

    public function show(Role $role)
    {
        $role->menu_ids = Menu::query()->whereHas('roles', function (Builder $q) use ($role) {
            $q->where('role_id', $role->id);
        })->pluck('id')->toArray();

        return RoleResource::make($role);
    }
}
