<?php

namespace Tests\Feature;

use App\Http\Middleware\HandleInertiaRequests;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * Guards what every page gets from HandleInertiaRequests (framework stack
 * rule: pages get only what they show). A new shared prop, or a new field on
 * the signed-in user, has to be added here on purpose.
 */
class SharedPropsTest extends TestCase
{
    use RefreshDatabase;

    private const SHARED = [
        'auth', 'billing', 'isDemo', 'modules', 'name', 'portal_flash',
        'push', 'shareLocation', 'sidebarOpen', 'tenant', 'time',
    ];

    private const AUTH = ['can', 'home', 'roles', 'routes', 'user'];

    private const CAN = [
        'backOffice', 'drive', 'manageBusiness', 'manageTime', 'manageUsers',
        'pick', 'priceExempt', 'quickbooksLog', 'seeCosts', 'seePrices',
        'setPrices', 'viewProfit', 'workQueue',
    ];

    private const USER = ['email', 'email_verified_at', 'id', 'name'];

    public function test_every_page_gets_only_the_listed_shared_props(): void
    {
        $request = Request::create('/dashboard');
        $request->setUserResolver(fn () => User::factory()->create());

        $shared = app(HandleInertiaRequests::class)->share($request);
        $fromInertia = array_keys((new class extends Middleware {})->share($request));

        $ours = array_values(array_diff(array_keys($shared), $fromInertia));
        sort($ours);
        $this->assertSame(self::SHARED, $ours, 'Add a new shared prop to SharedPropsTest on purpose.');

        $auth = array_keys($shared['auth']);
        sort($auth);
        $this->assertSame(self::AUTH, $auth);

        $can = array_keys($shared['auth']['can']);
        sort($can);
        $this->assertSame(self::CAN, $can);
    }

    public function test_the_signed_in_user_is_a_hand_picked_array(): void
    {
        $user = User::factory()->create(['phone' => '905-555-0100']);

        $this->actingAs($user)->get('/settings/profile')->assertInertia(function (Assert $page) use ($user) {
            $page->where('auth.user.id', $user->id)
                ->where('auth.user.name', $user->name)
                ->where('auth.user.email', $user->email);

            $fields = array_keys($page->toArray()['props']['auth']['user']);
            sort($fields);
            $this->assertSame(self::USER, $fields, 'Pages get only these user fields, never the whole model.');
        });
    }

    public function test_no_shared_prop_carries_user_secrets(): void
    {
        $user = User::factory()->create(['phone' => '905-555-0199']);

        $this->actingAs($user)->get('/settings/profile')->assertInertia(function (Assert $page) {
            $shared = json_encode(array_intersect_key($page->toArray()['props'], array_flip(self::SHARED)));

            foreach (['"password"', '"two_factor_secret"', '"two_factor_recovery_codes"', '"remember_token"', '905-555-0199', '"permissions"', '"tenant_id"', '"phone"'] as $marker) {
                $this->assertStringNotContainsString($marker, (string) $shared, "Shared props leak {$marker}.");
            }
        });
    }
}
