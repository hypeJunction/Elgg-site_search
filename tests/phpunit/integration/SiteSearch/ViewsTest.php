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

    /**
     * @return string
     */
    public function getPluginID(): string {
        return 'site_search';
    }

    /**
     * @return void
     */
    public function testResourceViewExists(): void {
        $this->assertTrue(elgg_view_exists('resources/search/index'));
    }

    /**
     * @return void
     */
    public function testSearchEntityViewExists(): void {
        $this->assertTrue(elgg_view_exists('search/entity'));
    }

    /**
     * @return void
     */
    public function testFilterSearchViewExists(): void {
        $this->assertTrue(elgg_view_exists('filters/search'));
    }

    /**
     * @return void
     */
    public function testObjectListViewExists(): void {
        $this->assertTrue(elgg_view_exists('lists/search/object'));
    }

    /**
     * @return void
     */
    public function testUserListViewExists(): void {
        $this->assertTrue(elgg_view_exists('lists/search/user'));
    }

    /**
     * @return void
     */
    public function testGroupListViewExists(): void {
        $this->assertTrue(elgg_view_exists('lists/search/group'));
    }

    /**
     * @return void
     */
    public function testEntityCssViewExists(): void {
        $this->assertTrue(elgg_view_exists('search/entity.css'));
    }

    /**
     * @return void
     */
    public function testFilterSearchRenders(): void {
        $output = elgg_view('filters/search', [
            'query' => 'hello',
            'filter_context' => 'object',
        ]);
        $this->assertIsString($output);
        // Filter menu should contain links to each search type.
        $this->assertStringContainsString('search_types', $output);
    }

    /**
     * @return void
     */
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

    /**
     * @return void
     */
    public function testSearchEntityRendersForUser(): void {
        $user = $this->createUser();
        $output = elgg_view('search/entity', [
            'entity' => $user,
            'query' => substr($user->username, 0, 3),
        ]);
        $this->assertIsString($output);
        $this->assertNotEmpty($output);
    }

    /**
     * @return void
     */
    public function testCssExtensionRegistered(): void {
        $extensions = (array) _elgg_services()->views->getViewList('elgg.css');
        $this->assertContains('search/entity.css', $extensions);
    }
}
