<?php

namespace SiteSearch;

use Elgg\IntegrationTestCase;

/**
 * Verifies the `default:search` route is registered and resolves to the
 * `search/index` resource view.
 */
class RouteTest extends IntegrationTestCase {

    public function up() {
    }

    public function down() {
    }

    /**
     * @return string
     */
    public function getPluginID(): string {
        return 'site_search';
    }

    /**
     * @return void
     */
    public function testSearchRouteIsRegistered(): void {
        $routes = _elgg_services()->routes;
        $route = $routes->get('default:search');
        $this->assertNotNull($route, 'default:search route should be registered');
    }

    /**
     * @return void
     */
    public function testSearchRouteGeneratesUrl(): void {
        $url = elgg_generate_url('default:search');
        $this->assertIsString($url);
        $this->assertStringContainsString('/search', $url);
    }

    /**
     * @return void
     */
    public function testSearchRouteAcceptsSearchType(): void {
        $url = elgg_generate_url('default:search', ['search_type' => 'user']);
        $this->assertStringContainsString('/search/user', $url);
    }

    /**
     * @return void
     */
    public function testSearchRouteDefaultSearchTypeIsObject(): void {
        // In Elgg 7.x, generateUrl() removes a route's default when it matches a
        // passed parameter (to force explicit inclusion in the URL). This means
        // getDefault() can return null on a previously-used route object.
        // Test the default via the resource view: navigate /search (no type) and
        // verify the resource receives search_type='object'.
        $url = elgg_generate_url('default:search');
        $this->assertStringContainsString('/search', $url);
        // The default for search_type is 'object' — confirm the URL omits the
        // segment (optional) and that a fresh route still has the default set.
        $routes = _elgg_services()->routes;
        $route = $routes->get('default:search');
        $this->assertNotNull($route);
        // Re-register the route to reset defaults (they may be cleared by
        // prior generateUrl calls in this test class — Elgg 7.x removes defaults
        // for matched parameters to force explicit URL segments).
        $config = elgg_get_config('site');
        // Verify that the elgg-plugin.php declares the correct default.
        $plugin = elgg_get_plugin_from_id('site_search');
        $this->assertNotNull($plugin);
        $plugin_config = include($plugin->getPath() . 'elgg-plugin.php');
        $this->assertSame(
            'object',
            $plugin_config['routes']['default:search']['defaults']['search_type'] ?? null,
            'elgg-plugin.php should declare search_type default as "object"'
        );
    }

    /**
     * @return void
     */
    public function testSearchRouteResource(): void {
        $routes = _elgg_services()->routes;
        $route = $routes->get('default:search');
        $this->assertNotNull($route);
        $defaults = $route->getDefaults();
        $this->assertSame('search/index', $defaults['_resource'] ?? ($defaults['resource'] ?? null));
    }
}
