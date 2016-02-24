<?php

$base_url = elgg_normalize_url('search/user') . '?' . parse_url(current_page_url(), PHP_URL_QUERY);

$list_class = (array) elgg_extract('list_class', $vars, array());
$list_class[] = 'search-list';

$item_class = (array) elgg_extract('item_class', $vars, array());

$options = (array) elgg_extract('options', $vars, array());

$list_options = array(
	'full_view' => false,
	'limit' => elgg_extract('limit', $vars, elgg_get_config('default_limit')) ? : 10,
	'list_class' => implode(' ', $list_class),
	'item_class' => implode(' ', $item_class),
	'no_results' => elgg_echo('search:no_results'),
	'pagination' => true,
	'pagination_type' => 'default',
	'base_url' => $base_url,
	'list_id' => 'search-user',
	'item_view' => 'search/entity',
);

$getter_options = array(
	'type' => 'user',
	'search_type' => 'entities',
	'query' => elgg_extract('query', $vars),
	'preload_owner' => true,
	'preload_containers' => true,
	'advanced_search' => true,
);

$options = array_merge($list_options, $options, $getter_options);

$params = $vars;
$params['options'] = $options;
$params['callback'] = 'elgg_list_entities';
$params['show_search'] = true;
$params['show_sort'] = true;
$params['show_rel'] = false;
echo elgg_view('lists/users', $params);
