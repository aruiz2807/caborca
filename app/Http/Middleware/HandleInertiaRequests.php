<?php

namespace App\Http\Middleware;

use App\Models\BiReport;
use App\Models\BiSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Inertia\Middleware;

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
        $file = lang_path( "php_" . App::currentLocale() . ".json" );

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user(),
                'roles' => $request->user() ? $request->user()->getRoleNames() : [],
                'permissions' => $request->user() ? $request->user()->getAllPermissions()->pluck('name') : [],
                'unreadMessagesCount' => $request->user() ? \App\Models\Message::where('user_id', $request->user()->id)->where('status', \App\Enums\MessageStatus::NEW)->count() : 0,
            ],
            'reportsMenuSections' => fn () => $this->buildReportsMenuSections($request),
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
                'error' => fn () => $request->session()->get('error')
            ],
            'locale' => App::currentLocale(),
            'locales' => config( 'app.available_locales' ),
            'translations' => File::exists( $file ) ? File::json( $file ) : []
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function buildReportsMenuSections(Request $request): array
    {
        $user = $request->user();

        if (! $user) {
            return [];
        }

        if (! Schema::hasTable('bi_sections') || ! Schema::hasTable('bi_reports')) {
            return [];
        }

        $sections = BiSection::query()
            ->with(['reports' => fn ($query) => $query->orderBy('name')])
            ->orderBy('name')
            ->get();

        return $sections
            ->map(function (BiSection $section) use ($user): ?array {
                if (! $user->can($section->permissionName())) {
                    return null;
                }

                $reports = $section->reports
                    ->filter(fn (BiReport $report) => $user->can($report->permissionName()))
                    ->map(function (BiReport $report): array {
                        return [
                            'title' => $report->name,
                            'routeName' => 'reports.show',
                            'routeParams' => [
                                'biReport' => $report->id,
                            ],
                        ];
                    })
                    ->values()
                    ->all();

                if (empty($reports)) {
                    return null;
                }

                return [
                    'title' => $section->name,
                    'options' => $reports,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }
}
