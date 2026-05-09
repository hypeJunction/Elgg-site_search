## Elgg 7.x Migration (2026-05-09)

- Bumped `elgg/elgg` requirement to `~7.0.0`, `php` to `>=8.3`
- Docker test stack added for Elgg 7.x (docker/elgg7/) with PHP 8.3, MySQL 8.0, PHPUnit 10.5+
- No PHP or CSS breaking changes found in this plugin (no CSS Crush syntax, no direct ElggObject instantiation, no removed APIs used)
- No data migration needed

## Elgg 6.x Migration (2026-05-09)

- Bumped `elgg/elgg` requirement to `~6.1.0`, `php` to `>=8.1`, added `ext-intl`
- Converted AMD `define(function(require){...})` modules to ES modules in bundled subplugins (object_sort, user_sort, group_sort sort.js files)
- Replaced `elgg.get()` AJAX with `elgg/Ajax` module; fixed `.on(this)` → `.bind(this)` JS bug in all sort.js files
- Docker test stack added for Elgg 6.x (docker/elgg6/) with MySQL 8.0, PHPUnit 10.5
- No deprecated hook functions (`elgg_trigger_event_results` already used)
- No data migration needed

<a name="5.0.0"></a>
## 5.0.0 (2026-05-08)

### Migration: Elgg 4.x → 5.x

* Unified events: `elgg_trigger_plugin_hook` → `elgg_trigger_event_results` in `filters/search.php` and `search/entity.php`
* PHP requirement bumped to `>=8.2`, Elgg requirement to `^5.0`
* Docker stack updated to `php:8.2-apache`, `mysql:8.0`, `elgg/elgg 5.1.12`
* Tests updated: `\Elgg\Hook` → `\Elgg\Event`, `elgg_register_plugin_hook_handler` → `elgg_register_event_handler`
* Added `phpcs.xml.dist` to exclude legacy `mod/` subplugins from coding standard checks
* No data migration required

<a name="4.0.0"></a>
## 4.0.0 (2026-04-17)

### Migration: Elgg 4.x

* Removed `manifest.xml` — metadata moved to `elgg-plugin.php` `'plugin'` key
* Removed `start.php` and `autoloader.php` (no custom init logic needed)
* Fixed `elgg_get_registered_tag_metadata_names()` → `elgg_get_config('registered_tag_metadata_names')`
* Fixed `get_subtype_id()` in `mod/object_sort` — subtypes are strings in Elgg 4.x
* Fixed `elgg_get_metastring_id()` in `mod/object_sort` — metastrings removed in 3.x+
* Updated `composer.json`: `elgg/elgg: ^4.0`, `composer/installers: ^2.0`, `extra.elgg-plugin.id`


<a name="1.0.1"></a>
## [1.0.1](https://github.com/hypeJunction/Elgg-site_search/compare/1.0.0...v1.0.1) (2016-02-24)


### Bug Fixes

* **listing:** do not display redundant info in search listings ([e6efd72](https://github.com/hypeJunction/Elgg-site_search/commit/e6efd72))



<a name="1.0.0"></a>
# 1.0.0 (2016-02-24)


### Features

* **releases:** initial commit ([b754c33](https://github.com/hypeJunction/Elgg-site_search/commit/b754c33))



