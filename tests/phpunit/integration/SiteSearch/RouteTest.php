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

    public function getPluginID(): string {
        return 'site_search';
    }

    public function testSearchRouteIsRegistered(): void {
        $routes = _elgg_services()->routes;
        $route = $routes->get('default:search');
        $this->assertNotNull($route, 'default:search route should be registered');
    }

    public function testSearchRouteGeneratesUrl(): void {
        $url = elgg_generate_url('default:search');
        $this->assertIsString($url);
        $this->assertStringContainsString('/search', $url);
    }

    public function testSearchRouteAcceptsSearchType(): void {
        $url = elgg_generate_url('default:search', ['search_type' => 'user']);
        $this->assertStringContainsString('/search/user', $url);
    }

    public function testSearchRouteDefaultSearchTypeIsObject(): void {
        $routes = _elgg_services()->routes;
        $route = $routes->get('default:search');
        $this->assertNotNull($route);
        $defaults = $route->getDefaults();
        $this->assertSame('object', $defaults['search_type'] ?? null);
    }

    public function testSearchRouteResource(): void {
        $routes = _elgg_services()->routes;
        $route = $routes->get('default:search');
        $this->assertNotNull($route);
        $defaults = $route->getDefaults();
        $this->assertSame('search/index', $defaults['_resource'] ?? ($defaults['resource'] ?? null));
    }
}
