<?php

namespace SiteSearch;

use Elgg\IntegrationTestCase;

/**
 * Verifies plugin-exposed hooks are triggered and can be filtered.
 */
class HookTest extends IntegrationTestCase {

    public function up() {
    }

    public function down() {
    }

    public function getPluginID(): string {
        return 'site_search';
    }

    public function testSearchTypesHookIsTriggered(): void {
        $called = false;
        $handler = function (\Elgg\Hook $hook) use (&$called) {
            $called = true;
            $value = (array) $hook->getValue();
            $value[] = 'custom';
            return $value;
        };

        elgg_register_plugin_hook_handler('search_types', 'get_types', $handler);

        $output = elgg_view('filters/search', [
            'query' => 'x',
            'filter_context' => 'object',
        ]);

        elgg_unregister_plugin_hook_handler('search_types', 'get_types', $handler);

        $this->assertTrue($called, 'search_types,get_types hook should fire when filters/search renders');
        $this->assertIsString($output);
    }

    public function testSearchTypesHookCanAddType(): void {
        $handler = function (\Elgg\Hook $hook) {
            $value = (array) $hook->getValue();
            $value[] = 'custom_type';
            return $value;
        };

        elgg_register_plugin_hook_handler('search_types', 'get_types', $handler);

        $result = elgg_trigger_plugin_hook('search_types', 'get_types', [], ['object', 'user', 'group']);

        elgg_unregister_plugin_hook_handler('search_types', 'get_types', $handler);

        $this->assertIsArray($result);
        $this->assertContains('custom_type', $result);
    }

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
        $handler = function (\Elgg\Hook $hook) use (&$called) {
            $called = true;
            $value = (array) $hook->getValue();
            $value['custom'] = 'custom_subtitle';
            return $value;
        };

        elgg_register_plugin_hook_handler('subtitle', 'search:object:blog', $handler);

        $output = elgg_view('search/entity', [
            'entity' => $entity,
            'query' => 'hook',
        ]);

        elgg_unregister_plugin_hook_handler('subtitle', 'search:object:blog', $handler);

        $this->assertTrue($called, 'subtitle,search:object:blog hook should fire when rendering search/entity');
        $this->assertStringContainsString('custom_subtitle', $output);
    }
}
