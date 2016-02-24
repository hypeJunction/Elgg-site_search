<?php

$base_url = elgg_normalize_url('search/group') . '?' . parse_url(current_page_url(), PHP_URL_QUERY);

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
	'list_id' => 'search-group',
	'item_view' => 'search/entity',
);

$subtype = get_input('entity_subtype', ELGG_ENTITIES_NO_VALUE);
if (!$subtype) {
	$types = get_registered_entity_types();
	$types = elgg_trigger_plugin_hook('search_types', 'get_queries', $params, $types);
	$subtype = elgg_extract('group', $types);
}

$owner_guid = get_input('owner_guid', ELGG_ENTITIES_ANY_VALUE);
$container_guid = get_input('container_guid', ELGG_ENTITIES_ANY_VALUE);

$getter_options = array(
	'type' => 'group',
	'subtype' => $subtype,
	'owner_guid' => $owner_guid,
	'container_guid' => $container_guid,
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
$params['show_subtype'] = true;
$params['show_rel'] = false;
echo elgg_view('lists/groups', $params);
