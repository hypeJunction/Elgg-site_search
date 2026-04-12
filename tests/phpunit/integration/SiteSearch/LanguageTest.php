<?php

namespace SiteSearch;

use Elgg\IntegrationTestCase;

/**
 * Verifies plugin language keys are registered.
 */
class LanguageTest extends IntegrationTestCase {

    public function up() {
    }

    public function down() {
    }

    public function getPluginID(): string {
        return 'site_search';
    }

    public function testSearchTypeLanguageKeysExist(): void {
        $this->assertTrue(elgg_language_key_exists('search_types:object'));
        $this->assertTrue(elgg_language_key_exists('search_types:user'));
        $this->assertTrue(elgg_language_key_exists('search_types:group'));
    }

    public function testSearchSubtitleKeysExist(): void {
        $this->assertTrue(elgg_language_key_exists('search:owner'));
        $this->assertTrue(elgg_language_key_exists('search:container'));
        $this->assertTrue(elgg_language_key_exists('search:last_action'));
    }

    public function testOwnerKeyFormatting(): void {
        $this->assertSame('By Alice', elgg_echo('search:owner', ['Alice']));
    }
}
