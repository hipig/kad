<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MenuRequest;
use App\Http\Resources\MenuResource;
use App\Models\Menu;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenusController extends Controller
{
    public function tree(Request $request)
    {
        $menus = Menu::query()->whereNull('parent_id')->with(['children'])->orderBy('sort')->oldest()->get();

        return MenuResource::collection($menus);
    }

    public function current(Request $request)
    {
        $roleIds = [];
        if (Auth::check()) {
            $roleIds = Auth::user()->roles()->pluck('id')->toArray();
        }

        $menus = Menu::query()->with(['children' => function($qc) use ($roleIds) {
            $qc->whereDoesntHave('roles')->orWhere(function (Builder $q) use ($roleIds) {
                $q->whereHas('roles', function (Builder $qr) use ($roleIds) {
                    $qr->whereIn('role_id', $roleIds);
                });
            });
        }])->whereNull('parent_id')->whereDoesntHave('roles')->orWhere(function (Builder $q) use ($roleIds) {
            $q->whereHas('roles', function (Builder $qr) use ($roleIds) {
                $qr->whereIn('role_id', $roleIds);
            });
        })->whereNull('parent_id')->orderBy('sort')->oldest()->get();

        return MenuResource::collection($menus);
    }

    public function store(MenuRequest $request)
    {
        $menu = Menu::create($request->only([
            'name',
            'key',
            'path',
            'icon',
            'parent_id',
            'sort',
            'status',
        ]));

        return MenuResource::make($menu);
    }

    public function show(Menu $menu)
    {
        $menu->load('roles');
        return MenuResource::make($menu->append('role_ids'));
    }

    public function update(MenuRequest $request, Menu $menu)
    {
        $menu->fill($request->only([
            'name',
            'key',
            'path',
            'icon',
            'parent_id',
            'sort',
            'status',
        ]));
        $menu->syncRoles($request->get('role_ids'));

        $menu->save();

        return MenuResource::make($menu);
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();

        return response()->noContent();
    }

    public function sort(Request $request)
    {
        Menu::sort($request->tree_data);

        return response()->noContent();
    }
}
