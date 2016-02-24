<?php

$filter_context = elgg_extract('filter_context', $vars);

$types = get_registered_entity_types();
$types = elgg_trigger_plugin_hook('search_types', 'get_queries', $params, $types);
$types = array_keys($types);

$search_types = array(
	'object',
	'user',
	'group',
);

foreach ($search_types as $key => $search_type) {
	if (!in_array($search_type, $types)) {
		unset($search_types[$key]);
	}
}

$custom_types = elgg_trigger_plugin_hook('search_types', 'get_types', $params, array());
foreach ($custom_types as $ct) {
	$search_types[] = $ct;
}

foreach ($search_types as $type) {
	elgg_register_menu_item('filter', array(
		'name' => "search_types:$type",
		'href' => elgg_http_add_url_query_elements("search/$type", array(
			'query' => elgg_extract('query', $vars),
		)),
		'text' => elgg_echo("search_types:$type"),
		'selected' => $type == $filter_context,
	));
}

echo elgg_view_menu('filter', array(
	'sort_by' => 'priority',
));
