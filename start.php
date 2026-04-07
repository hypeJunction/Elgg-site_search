<?php

require_once __DIR__ . '/autoloader.php';

elgg_register_event_handler('init', 'system', 'site_search_init');

function site_search_init() {

	elgg_extend_view('elgg.css', 'search/entity.css');

}
