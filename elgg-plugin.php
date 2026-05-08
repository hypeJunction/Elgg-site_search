<?php

return [
	'plugin' => [
		'name' => 'Site Search',
		'version' => '5.0.0',
		'dependencies' => [
			'search' => [
				'position' => 'after',
			],
		],
	],
	'routes' => [
		'default:search' => [
			'path' => '/search/{search_type?}',
			'resource' => 'search/index',
			'defaults' => [
				'search_type' => 'object',
			],
		],
	],
	'view_extensions' => [
		'elgg.css' => [
			'search/entity.css' => [],
		],
	],
];
