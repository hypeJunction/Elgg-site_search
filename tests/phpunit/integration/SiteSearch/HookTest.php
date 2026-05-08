<?php

namespace SiteSearch;

use Elgg\IntegrationTestCase;

/**
 * Verifies plugin-exposed events are triggered and can be filtered.
 */
class HookTest extends IntegrationTestCase {

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
    public function testSearchTypesHookIsTriggered(): void {
        $called = false;
        $handler = function (\Elgg\Event $event) use (&$called) {
            $called = true;
            $value = (array) $event->getValue();
            $value[] = 'custom';
            return $value;
        };

        elgg_register_event_handler('search_types', 'get_types', $handler);

        $output = elgg_view('filters/search', [
            'query' => 'x',
            'filter_context' => 'object',
        ]);

        elgg_unregister_event_handler('search_types', 'get_types', $handler);

        $this->assertTrue($called, 'search_types,get_types event should fire when filters/search renders');
        $this->assertIsString($output);
    }

    /**
     * @return void
     */
    public function testSearchTypesHookCanAddType(): void {
        $handler = function (\Elgg\Event $event) {
            $value = (array) $event->getValue();
            $value[] = 'custom_type';
            return $value;
        };

        elgg_register_event_handler('search_types', 'get_types', $handler);

        $result = elgg_trigger_event_results('search_types', 'get_types', [], ['object', 'user', 'group']);

        elgg_unregister_event_handler('search_types', 'get_types', $handler);

        $this->assertIsArray($result);
        $this->assertContains('custom_type', $result);
    }

    /**
     * @return void
     */
    public function testSubtitleHookCanBeRegistered(): void {
        $user = $this->createUser();
        $entity = $this->createObject([
            'subtype' => 'blog',
            'owner_guid' => $user->guid,
            'container_guid' => $user->guid,
            'title' => 'subtitle hook test',
            'description' => 'hook description',
        ]);

        $called = false;
        $handler = function (\Elgg\Event $event) use (&$called) {
            $called = true;
            $value = (array) $event->getValue();
            $value['custom'] = 'custom_subtitle';
            return $value;
        };

        elgg_register_event_handler('subtitle', 'search:object:blog', $handler);

        $output = elgg_view('search/entity', [
            'entity' => $entity,
            'query' => 'hook',
        ]);

        elgg_unregister_event_handler('subtitle', 'search:object:blog', $handler);

        $this->assertTrue($called, 'subtitle,search:object:blog event should fire when rendering search/entity');
        $this->assertStringContainsString('custom_subtitle', $output);
    }
}
