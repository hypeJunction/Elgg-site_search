<?php

namespace SiteSearch;

use Elgg\IntegrationTestCase;

/**
 * Verifies plugin-provided views exist and render without errors.
 */
class ViewsTest extends IntegrationTestCase {

    public function up() {
    }

    public function down() {
    }

    public function getPluginID(): string {
        return 'site_search';
    }

    public function testResourceViewExists(): void {
        $this->assertTrue(elgg_view_exists('resources/search/index'));
    }

    public function testSearchEntityViewExists(): void {
        $this->assertTrue(elgg_view_exists('search/entity'));
    }

    public function testFilterSearchViewExists(): void {
        $this->assertTrue(elgg_view_exists('filters/search'));
    }

    public function testObjectListViewExists(): void {
        $this->assertTrue(elgg_view_exists('lists/search/object'));
    }

    public function testUserListViewExists(): void {
        $this->assertTrue(elgg_view_exists('lists/search/user'));
    }

    public function testGroupListViewExists(): void {
        $this->assertTrue(elgg_view_exists('lists/search/group'));
    }

    public function testEntityCssViewExists(): void {
        $this->assertTrue(elgg_view_exists('search/entity.css'));
    }

    public function testFilterSearchRenders(): void {
        $output = elgg_view('filters/search', [
            'query' => 'hello',
            'filter_context' => 'object',
        ]);
        $this->assertIsString($output);
        // Filter menu should contain links to each search type.
        $this->assertStringContainsString('search_types', $output);
    }

    public function testSearchEntityRendersForObject(): void {
        $user = $this->createUser();
        $entity = $this->createObject([
            'subtype' => 'blog',
            'owner_guid' => $user->guid,
            'container_guid' => $user->guid,
            'title' => 'Hello world',
            'description' => 'A searchable body containing hello text.',
        ]);

        $output = elgg_view('search/entity', [
            'entity' => $entity,
            'query' => 'hello',
        ]);

        $this->assertIsString($output);
        $this->assertNotEmpty($output);
        // Query term should be highlighted with <strong> tags.
        $this->assertStringContainsString('<strong>', $output);
    }

    public function testSearchEntityRendersForUser(): void {
        $user = $this->createUser();
        $output = elgg_view('search/entity', [
            'entity' => $user,
            'query' => substr($user->username, 0, 3),
        ]);
        $this->assertIsString($output);
        $this->assertNotEmpty($output);
    }

    public function testCssExtensionRegistered(): void {
        $extensions = (array) _elgg_services()->views->getViewList('elgg.css');
        $this->assertContains('search/entity.css', $extensions);
    }
}
