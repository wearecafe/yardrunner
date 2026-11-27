<?php

namespace Tests\Feature;

use App\Http\Middleware\ServeFromEdgeCache;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * The home page and the help center set no cookies and are cached at the
 * edge (routes/marketing.php, ServeFromEdgeCache).
 */
class MarketingPagesTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, array{string}> */
    public static function pages(): array
    {
        return [
            'home' => ['/'],
            'help center' => ['/help'],
            'help category' => ['/help/selling'],
            'help article' => ['/help/getting-started/set-up-your-yard'],
            'help search' => ['/help/search?q=pallet'],
            'help suggestions' => ['/help/suggest?q=pall'],
        ];
    }

    #[DataProvider('pages')]
    public function test_marketing_pages_set_no_cookies_and_can_be_cached(string $url): void
    {
        $response = $this->get($url)->assertOk();

        $this->assertNoCookies($response);
        $this->assertSame(
            sprintf('max-age=%d, public, s-maxage=%d', ServeFromEdgeCache::BROWSER_SECONDS, ServeFromEdgeCache::EDGE_SECONDS),
            $response->headers->get('Cache-Control'),
        );
    }

    #[DataProvider('pages')]
    public function test_marketing_pages_are_the_same_for_signed_in_people(string $url): void
    {
        $user = User::factory()->create(['name' => 'Signed In Person']);

        $response = $this->actingAs($user)->get($url)->assertOk();

        $this->assertNoCookies($response);
        $this->assertStringNotContainsString('Signed In Person', (string) $response->getContent());
        $this->assertStringNotContainsString($user->email, (string) $response->getContent());
    }

    public function test_the_home_page_offers_log_in_to_everyone(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/')
            ->assertInertia(fn (Assert $page) => $page->component('welcome')->where('auth.user', null));
    }

    public function test_inertia_visits_become_full_page_loads_that_are_never_cached(): void
    {
        $response = $this->get('/help', ['X-Inertia' => 'true', 'X-Requested-With' => 'XMLHttpRequest'])
            ->assertStatus(409)
            ->assertHeader('X-Inertia-Location', url('/help'));

        $this->assertNoCookies($response);
        $this->assertStringContainsString('no-store', (string) $response->headers->get('Cache-Control'));
    }

    public function test_missing_help_pages_are_never_cached(): void
    {
        $response = $this->get('/help/nope')->assertNotFound();

        $this->assertNoCookies($response);
        $this->assertStringContainsString('no-store', (string) $response->headers->get('Cache-Control'));
    }

    public function test_app_pages_still_use_sessions(): void
    {
        $this->get('/login')->assertOk()->assertCookie('XSRF-TOKEN');
    }

    private function assertNoCookies(TestResponse $response): void
    {
        $this->assertSame([], $response->headers->getCookies(), 'Marketing pages must not set cookies.');
    }
}
