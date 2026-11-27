<?php

namespace Tests\Feature;

use App\Help\HelpCenter;
use App\Http\Middleware\ServeFromEdgeCache;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
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

    /** The page sources behind the marketing routes (keep in step with routes/marketing.php). */
    private const SOURCES = [
        'resources/js/pages/welcome.tsx',
        'resources/js/pages/help',
        'resources/js/components/help',
    ];

    public function test_every_help_page_sets_no_cookies_and_can_be_cached(): void
    {
        $help = app(HelpCenter::class);
        $urls = array_map(fn (string $slug) => "/help/{$slug}", array_keys($help->categories()));

        foreach ($help->all() as $article) {
            $urls[] = "/help/{$article->path()}";
        }

        $this->assertNotEmpty($help->all());

        foreach ($urls as $url) {
            $response = $this->get($url);
            $this->assertSame(200, $response->getStatusCode(), "{$url} should load.");
            $this->assertNoCookies($response);
            $this->assertStringContainsString('s-maxage', (string) $response->headers->get('Cache-Control'), "{$url} should be cacheable.");
        }
    }

    public function test_links_on_marketing_pages_go_somewhere(): void
    {
        $links = [];

        foreach (app(HelpCenter::class)->all() as $article) {
            preg_match_all('/href="(\/[^"#?]*)/', $article->html, $found);
            foreach ($found[1] as $link) {
                $links[$link][] = "/help/{$article->path()}";
            }
        }

        foreach ($this->sourceFiles() as $file) {
            preg_match_all('/href="(\/[^"#?]*)"/', File::get($file), $found);
            foreach ($found[1] as $link) {
                $links[$link][] = $file;
            }
        }

        $this->assertNotEmpty($links);

        // The demo yard only exists where YARDRUNNER_DEMO is on, and the home
        // page links it only then (DemoYardTest covers it).
        unset($links['/demo']);

        foreach ($links as $link => $from) {
            $status = $this->get($link)->getStatusCode();
            // App pages send guests to sign in; anything else must load.
            $this->assertTrue($status === 200 || $status === 302, "{$link} (linked from ".implode(', ', array_unique($from)).") answers {$status}.");
        }
    }

    public function test_marketing_pages_have_no_forms_that_post(): void
    {
        foreach ($this->sourceFiles() as $file) {
            $source = File::get($file);

            foreach (['useForm(', '<Form', 'router.post', 'router.put', 'router.patch', 'router.delete', 'method="post"'] as $marker) {
                $this->assertStringNotContainsString($marker, $source, "{$file} posts ({$marker}); marketing pages have no session or CSRF token.");
            }
        }
    }

    /** @return list<string> */
    private function sourceFiles(): array
    {
        $files = [];

        foreach (self::SOURCES as $path) {
            $full = base_path($path);
            $files = [...$files, ...(is_dir($full) ? array_map(fn ($f) => $f->getPathname(), File::allFiles($full)) : [$full])];
        }

        return $files;
    }

    private function assertNoCookies(TestResponse $response): void
    {
        $this->assertSame([], $response->headers->getCookies(), 'Marketing pages must not set cookies.');
    }
}
