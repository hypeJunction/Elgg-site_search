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
        $routes = _elgg_services()->routes;
        $route = $routes->get('default:search');
        $this->assertNotNull($route);
        $defaults = $route->getDefaults();
        $this->assertSame('object', $defaults['search_type'] ?? null);
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
