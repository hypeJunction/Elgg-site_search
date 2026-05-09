<?php

$base_url = elgg_generate_url('default:search', ['search_type' => 'object']) . '?' . parse_url(current_page_url(), PHP_URL_QUERY);

$list_class = (array) elgg_extract('list_class', $vars, []);
$list_class[] = 'search-list';

$item_class = (array) elgg_extract('item_class', $vars, []);

$options = (array) elgg_extract('options', $vars, []);

$list_options = [
	'full_view' => false,
	'limit' => elgg_extract('limit', $vars, elgg_get_config('default_limit')) ? : 10,
	'list_class' => implode(' ', $list_class),
	'item_class' => implode(' ', $item_class),
	'no_results' => elgg_echo('search:no_results'),
	'pagination' => true,
	'pagination_type' => 'default',
	'base_url' => $base_url,
	'list_id' => 'search-object',
	'item_view' => 'search/entity',
];

$subtype = get_input('entity_subtype');

$owner_guid = get_input('owner_guid');
$container_guid = get_input('container_guid');

$getter_options = [
	'type' => 'object',
	'subtype' => $subtype ?: null,
	'owner_guid' => $owner_guid ?: null,
	'container_guid' => $container_guid ?: null,
	'search_type' => 'entities',
	'query' => elgg_extract('query', $vars),
	'preload_owners' => true,
	'preload_containers' => true,
];

$options = array_merge($list_options, $options, $getter_options);

$params = $vars;
$params['options'] = $options;
$params['callback'] = 'elgg_list_entities';
$params['show_search'] = true;
$params['show_sort'] = true;
$params['show_subtype'] = true;
echo elgg_view('lists/objects', $params);
