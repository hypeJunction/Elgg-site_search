<?php

$search_type = elgg_extract('search_type', $vars, get_input('search_type', 'object'));

if (!elgg_view_exists("lists/search/$search_type")) {
	$search_type = 'object';
}

$query = get_input('query');
if (!isset($query)) {
	$query = get_input('q', get_input('tag', ''));
}

$params = [
	'query' => $query,
	'filter_context' => $search_type,
];

$title = elgg_echo('search');

elgg_push_breadcrumb($title, elgg_generate_url('default:search'));

$content = elgg_view("lists/search/$search_type", $params);
if (elgg_is_xhr()) {
	echo $content;
} else {
	$filter = elgg_view('filters/search', $params);
	$sidebar = elgg_view('sidebars/search', $params);

	$layout_params = [
		'content' => $content,
		'title' => $title,
		'filter' => $filter,
		'sidebar' => $sidebar,
	];

	$body = elgg_view_layout('default', $layout_params);

	echo elgg_view_page($title, $body);
}
