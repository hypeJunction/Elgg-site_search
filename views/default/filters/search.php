<?php

$filter_context = elgg_extract('filter_context', $vars);

$search_types = [
	'object',
	'user',
	'group',
];

$search_types = elgg_trigger_plugin_hook('search_types', 'get_types', $vars, $search_types);

foreach ($search_types as $type) {
	$href = elgg_generate_url('default:search', ['search_type' => $type]);
$href = elgg_http_add_url_query_elements($href, [
		'query' => elgg_extract('query', $vars),
	]);

elgg_register_menu_item('filter', [
		'name' => "search_types:$type",
		'href' => $href,
		'text' => elgg_echo("search_types:$type"),
		'selected' => $type == $filter_context,
	]);
}

echo elgg_view_menu('filter', [
	'sort_by' => 'priority',
]);
