<?php

$search_type = get_input('search_type');
if (!$search_type) {
	$segments = _elgg_services()->request->getUrlSegments();
	$page = array_shift($segments); // 'search'
	$search_type = array_shift($segments);
}

if (!elgg_view_exists("lists/search/$search_type")) {
	$search_type = 'object';
}

$query = get_input('query');
if (!isset($query)) {
	$query = get_input('q', get_input('tag', ''));
}
$query = stripslashes($query);

$params = array(
	'query' => $query,
	'filter_context' => $search_type,
);

$title = elgg_echo('search');

elgg_push_breadcrumb($title, 'search');

$content = elgg_view("lists/search/$search_type", $params);
if (elgg_is_xhr()) {
	echo $content;
} else {
	$filter = elgg_view('filters/search', $params);
	$sidebar = elgg_view('sidebars/search', $params);

	$params = array(
		'content' => $content,
		'title' => $title,
		'filter' => $filter,
		'sidebar' => $sidebar
	);

	$body = elgg_view_layout('content', $params);

	echo elgg_view_page($title, $body);
}