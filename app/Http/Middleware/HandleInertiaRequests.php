<?php

namespace App\Http\Middleware;

use Inertia\Middleware;
use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserSharedResource;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            
            'auth.user' => fn () => $request->user()
                ? new UserSharedResource($request->user())
                : null,
            // 'documentCategories' => function () {
            //     if (Auth::check() && method_exists(Auth::user(), 'can') && Auth::user()) {
            //         return Categories::with('children')->get()->toTree();
            //     }
            //     return [];
            // },
            'flash' => [
                'status' => fn () => $request->session()->get('status'),
                'error' => fn () => $request->session()->get('error'),
                'success' => fn () => $request->session()->get('success'),
                'message'=> fn () => $request->session()->get('message'),
                'warning' => fn () => $request->session()->get('warning'),
            ],
        ];
    }
}
