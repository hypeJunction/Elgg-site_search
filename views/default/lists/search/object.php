<?php

$base_url = elgg_normalize_url('search/object') . '?' . parse_url(current_page_url(), PHP_URL_QUERY);

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
	'list_id' => 'search-object',
	'item_view' => 'search/entity',
);

$subtype = get_input('entity_subtype', ELGG_ENTITIES_NO_VALUE);
if (!$subtype) {
	$types = get_registered_entity_types();
	$types = elgg_trigger_plugin_hook('search_types', 'get_queries', $params, $types);
	$subtype = elgg_extract('object', $types);
}

$owner_guid = get_input('owner_guid', ELGG_ENTITIES_ANY_VALUE);
$container_guid = get_input('container_guid', ELGG_ENTITIES_ANY_VALUE);

$getter_options = array(
	'type' => 'object',
	'subtype' => $subtype,
	'owner_guid' => $owner_guid,
	'container_guid' => $container_guid,
	'search_type' => 'entities',
	'query' => elgg_extract('query', $vars),
	'preload_owner' => true,
	'preload_containers' => true,
	'search_tags' => true,
);

$options = array_merge($list_options, $options, $getter_options);

$params = $vars;
$params['options'] = $options;
$params['callback'] = 'elgg_list_entities';
$params['show_search'] = true;
$params['show_sort'] = true;
$params['show_subtype'] = true;
echo elgg_view('lists/objects', $params);

// @todo: implement subtype search hooks
//$results = null;
//if ($subtype) {
//	$results = elgg_trigger_plugin_hook('search', "object:$subtype", $options, NULL);
//}
//if (empty($results) && $results !== false) {
//	$results = elgg_trigger_plugin_hook('search', 'object', $options, array());
//}
//
//if (empty($results)) {
//	$entities = array();
//} else {
//	$entities = elgg_extract('entities', $results);
//}
//
//echo elgg_view_entity_list($entities, $options);
