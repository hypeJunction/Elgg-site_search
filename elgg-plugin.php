<?php

return [
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
