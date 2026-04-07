<?php

$query = elgg_extract('query', $vars);
$entity = elgg_extract('entity', $vars);
$size = elgg_extract('size', $vars, 'small');

$type = $entity->getType();
$subtype = $entity->getSubtype();
$owner = $entity->getOwnerEntity();
$container = $entity->getContainerEntity();

if (!$entity->getVolatileData('search_matched_title')) {
	$title = htmlspecialchars($entity->getDisplayName(), ENT_QUOTES, 'UTF-8');
	if ($query) {
		$title = preg_replace('/(' . preg_quote($query, '/') . ')/i', '<strong>$1</strong>', $title);
	}
	$entity->setVolatileData('search_matched_title', $title);
}

if (!$entity->getVolatileData('search_matched_description')) {
	$desc = elgg_get_excerpt((string) $entity->description, 200);
	if ($query) {
		$desc = preg_replace('/(' . preg_quote($query, '/') . ')/i', '<strong>$1</strong>', $desc);
	}
	$entity->setVolatileData('search_matched_description', $desc);
}

if (!$entity->getVolatileData('search_matched_extra')) {

	$fields = [];
	$prefix = '';
	$exclude = [];

	switch ($type) {
		case 'user' :
			$fields = array_keys((array) elgg_get_config('profile_fields'));
			$prefix = 'profile';
			$exclude = ['name', 'description', 'briefdescription'];
			break;
		case 'group' :
			$fields = array_keys((array) elgg_get_config('group'));
			$prefix = 'group';
			$exclude = ['name', 'description', 'briefdescription'];
			break;
		case 'object' :
			$fields = elgg_get_registered_tag_metadata_names();
			$prefix = 'tag_names';
			$exclude = ['title', 'description'];
			break;
	}

	$matches = [];
	foreach ($fields as $field) {
		if (in_array($field, $exclude)) {
			continue;
		}
		$metadata = $entity->$field;
		if (is_array($metadata)) {
			foreach ($metadata as $text) {
				if (stristr($text, $query)) {
					$highlighted = preg_replace('/(' . preg_quote($query, '/') . ')/i', '<strong>$1</strong>', htmlspecialchars($text, ENT_QUOTES, 'UTF-8'));
					$matches["$prefix:$field"][] = $highlighted;
				}
			}
		} else {
			if (stristr($metadata, $query)) {
				$highlighted = preg_replace('/(' . preg_quote($query, '/') . ')/i', '<strong>$1</strong>', htmlspecialchars($metadata, ENT_QUOTES, 'UTF-8'));
				$matches["$prefix:$field"][] = $highlighted;
			}
		}
	}

	$extra = [];
	foreach ($matches as $label => $match) {
		$extra[] = elgg_format_element('span', ['class' => 'search-match-extra-label'], elgg_echo($label)) . implode(', ', $match);
	}

	$entity->setVolatileData('search_matched_extra', implode('<br />', $extra));
}

$view_order = [];
if ($subtype) {
	$view_order[] = "search/$type/$subtype/entity";
}
$view_order[] = "search/$type/entity";
$view_order[] = "search/entities/entity";

foreach ($view_order as $view) {
	if (elgg_view_exists($view)) {
		echo elgg_view($view, $vars);
		return;
	}
}

$icon = $entity->getVolatileData('search_icon');
if (!$icon) {
	if ($type == 'user' || $type == 'group') {
		$icon = elgg_view_entity_icon($entity, $size);
	} elseif ($owner instanceof ElggUser) {
		$icon = elgg_view_entity_icon($owner, $size);
	} else if ($container instanceof ElggUser) {
		$icon = elgg_view_entity_icon($entity, $size);
	}
}

$title = $entity->getVolatileData('search_matched_title');
$description = $entity->getVolatileData('search_matched_description');
$extra_info = $entity->getVolatileData('search_matched_extra');

$url = $entity->getVolatileData('search_url');
if (!$url) {
	$url = $entity->getURL();
}

$title = elgg_view('output/url', [
	'text' => $title,
	'href' => $url,
	'class' => 'search-matched-title',
]);

$subtitle = [];

if ($subtype) {
	$type_keys = [
		"$type:$subtype",
		"$type:default",
		"item:$type:$subtype",
		"item:$type",
	];
	foreach ($type_keys as $key) {
		if (elgg_language_key_exists($key)) {
			$subtitle['type'] = elgg_echo($key);
			break;
		}
	}
}

$byline = [];
if ($type == 'object') {
	if ($owner) {
		$owner_link = elgg_view('output/url', [
			'text' => $owner->getDisplayName(),
			'href' => $owner->getURL(),
		]);
		$byline[] = elgg_echo('search:owner', [$owner_link]);
	}
	if ($container && !$container instanceof ElggUser && $container->guid != $owner->guid) {
		$container_link = elgg_view('output/url', [
			'text' => $container->getDisplayName(),
			'href' => $container->getURL(),
		]);
		$byline[] = elgg_echo('search:container', [$container_link]);
	}
}

if ($type == 'object') {
	$time = $entity->getVolatileData('search_time');
	if (!$time) {
		$time = elgg_view_friendly_time($entity->time_created);
	}
	$byline[] = $time;
}

if (!empty($byline)) {
	$subtitle['byline'] = implode(' ', $byline);
}

$last_action = $entity->getVolatileData('select:last_action') ? : max($entity->last_action, $entity->time_created);
if ($last_action) {
	$subtitle['last_action'] = elgg_echo("search:last_action", [elgg_get_friendly_time($last_action)]);
}

$subtitle = elgg_trigger_plugin_hook('subtitle', "search:$type:$subtype", $vars, $subtitle);

$subtitle_str = '';
foreach ($subtitle as $s) {
	$subtitle_str .= elgg_format_element('span', ['class' => 'elgg-search-subtitle-element'], $s);
}

$content = '';
if ($description) {
	$content .= elgg_format_element('div', ['class' => 'search-matched-description'], $description);
}
if ($extra_info) {
	$content .= elgg_format_element('div', ['class' => 'search-matched-extra'], $extra_info);
}
$summary = elgg_view("$type/elements/summary", [
	'entity' => $entity,
	'tags' => false,
	'title' => $title,
	'subtitle' => $subtitle_str,
	'content' => $content,
]);

echo elgg_view_image_block($icon, $summary);
