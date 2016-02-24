<?php

/**
 * Site Search
 *
 * @author Ismayil Khayredinov <info@hypejunction.com>
 * @copyright Copyright (c) 2015, Ismayil Khayredinov
 */
require_once __DIR__ . '/autoloader.php';

elgg_register_event_handler('init', 'system', 'site_search_init');

/**
 * Initialize
 * @return void
 */
function site_search_init() {

	elgg_unregister_plugin_hook_handler('search_types', 'get_types', 'search_custom_types_tags_hook');
	elgg_unregister_plugin_hook_handler('search', 'tags', 'search_tags_hook');

	elgg_extend_view('elgg.css', 'search/entity.css');

}
