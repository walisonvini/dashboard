<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

use App\Models\Menu;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        $user = $request->user();

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $user,
                'permissions' => $user?->getAllPermissions()->pluck('name'),
            ],
            'menus' => $user ? $this->getMenusForUser($user) : [],
            'ziggy' => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }

    public function getMenusForUser($user)
    {
        $roleViewPermissions = $user->roles()
            ->with(['permissions' => function ($query) {
                $query->where('name', 'like', '%.view');
            }])
            ->get()
            ->pluck('permissions')
            ->flatten()
            ->pluck('id')
            ->unique();

        return \App\Models\Menu::whereNull('parent_id')
            ->where(function ($query) use ($roleViewPermissions) {
                $query->whereHas('permissions', function ($q) use ($roleViewPermissions) {
                    $q->whereIn('permissions.id', $roleViewPermissions);
                })
                ->orWhereHas('children.permissions', function ($q) use ($roleViewPermissions) {
                    $q->whereIn('permissions.id', $roleViewPermissions);
                });
            })
            ->with(['children' => function ($query) use ($roleViewPermissions) {
                $query->whereHas('permissions', function ($q) use ($roleViewPermissions) {
                    $q->whereIn('permissions.id', $roleViewPermissions);
                });
            }])
            ->orderBy('name')
            ->get();
    }
}
