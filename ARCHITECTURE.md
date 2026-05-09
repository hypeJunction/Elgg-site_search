# site_search — Architecture (Elgg 7.x)

## Summary

Provides site-wide search views, route, and filter tabs for Elgg 6.x. The plugin replaces
Elgg's default search resource with a custom resource view that supports typed search
(objects, users, groups) and per-type list views. It does not register any entities,
actions, or hooks of its own — all integration is via routes, view extensions, and a
declared dependency on Elgg's built-in `search` plugin.

## Plugin Metadata

| Field | Value |
|-------|-------|
| Plugin ID | `site_search` |
| Elgg version | 7.x |
| PHP minimum | 8.3 |
| Composer package | `hypejunction/site_search` |
| Dependencies | `search` (core), `object_sort`, `user_sort`, `group_sort` (bundled in `mod/`) |

## Directory Structure

```
site_search/
├── elgg-plugin.php          — plugin config: route, view_extensions, dependencies
├── composer.json            — package metadata (4.x format: no manifest.xml)
├── languages/en.php         — English translations
├── views/default/
│   ├── filters/search.php   — filter tab view (object/user/group)
│   ├── lists/search/
│   │   ├── object.php       — object search results list
│   │   ├── user.php         — user search results list
│   │   └── group.php        — group search results list
│   ├── resources/search/
│   │   └── index.php        — search resource view (route handler)
│   └── search/
│       ├── entity.php       — individual search result rendering
│       └── entity.css       — search result styles
├── mod/                     — bundled companion plugins
│   ├── object_sort/         — sorting API for object lists
│   ├── user_sort/           — sorting API for user lists
│   ├── group_sort/          — sorting API for group lists
│   └── forms_api/           — form field API (dependency of object_sort)
├── tests/
│   ├── phpunit.xml
│   ├── bootstrap.php
│   └── phpunit/integration/SiteSearch/
│       ├── HookTest.php
│       ├── LanguageTest.php
│       ├── RouteTest.php
│       └── ViewsTest.php
└── docker/                  — per-plugin Elgg Docker test stacks (5.x, 6.x and 7.x)
```

## Registered Routes

| Route name | Path | Resource |
|------------|------|----------|
| `default:search` | `/search/{search_type?}` | `search/index` |

Default `search_type` is `object`.

## View Extensions

| Base view | Extension |
|-----------|-----------|
| `elgg.css` | `search/entity.css` |

## Events Used (not registered)

The `filters/search.php` view fires `elgg_trigger_event_results('search_types', 'get_types', ...)` to
allow other plugins to add search type filter tabs.

The `search/entity.php` view fires `elgg_trigger_event_results('subtitle', "search:$type:$subtype", ...)`
to allow plugins to inject subtitle elements into search results.

## Bundled Sub-Plugins (`mod/`)

These are shipped as part of site_search but installed as separate Elgg plugins via Composer:

- **`object_sort`** — provides `object_sort_add_sort_options()`, `object_sort_get_sort_options()`,
  and `object_sort_add_search_query_options()` APIs for sorted object lists. Uses 2.x-style
  `start.php` bootstrap — needs full 4.x migration (elgg-plugin.php, Bootstrap class).
- **`user_sort`** — same pattern for user lists.
- **`group_sort`** — same pattern for group lists.
- **`forms_api`** — form field registration API, dependency of object_sort.

> **Note:** The `mod/` sub-plugins are installed by Composer into the Elgg `mod/` directory
> at deploy time. They are NOT visible to Elgg from inside `site_search/mod/` — they must
> be installed at the top-level `mod/` directory to be activated.

## Seeding

No seeder required. This plugin owns no entity types, subtypes, or persistent relationship schemas — it is a pure UI/utility/admin plugin with no persisted entity surface of its own.

## Migration Notes (3.x → 4.x)

- Removed `manifest.xml` — metadata moved to `elgg-plugin.php` `'plugin'` key.
- Removed `start.php` and `autoloader.php` — no custom init logic needed.
- Fixed `elgg_get_registered_tag_metadata_names()` → `elgg_get_config('registered_tag_metadata_names')`
  in `views/default/search/entity.php` and `mod/object_sort/start.php`.
- Fixed `get_subtype_id()` in `mod/object_sort/start.php` — subtypes are strings in 4.x;
  `responses_count` sort now uses string literals in the SQL IN clause.
- Fixed `elgg_get_metastring_id()` in `mod/object_sort/start.php` — metastrings removed in
  3.x; `likes_count` sort now joins on `likes.name = 'likes'`.
- Updated tag metadata search join to use Elgg 3.x+ metadata table schema (no metastrings).
- `composer.json` updated: added `elgg/elgg: ^4.0`, bumped `composer/installers` to `^2.0`,
  added `extra.elgg-plugin.id`, removed `version` field.

## Migration Notes (5.x → 6.x)

- `elgg/elgg ~6.1.0`, `php >=8.1`, `ext-intl` added in `composer.json`.
- Bundled sort modules' AMD JS (`define(function(require){...})`) converted to ESM in `object_sort`, `user_sort`, `group_sort`.
- `elgg.get()` AJAX replaced with `elgg/Ajax` module. Pre-existing `.on(this)` bug fixed to `.bind(this)`.
- Docker test stack added for Elgg 6.x (docker/elgg6/).
- No deprecated PHP hook functions found — all already use `elgg_trigger_event_results`.
- No data migration needed.

## Migration Notes (6.x → 7.x)

- `elgg/elgg ~7.0.0`, `php >=8.3` in `composer.json`.
- Docker test stack added for Elgg 7.x (docker/elgg7/) with PHP 8.3.
- No breaking changes in this plugin: no CSS Crush syntax, no direct `ElggObject` instantiation, no removed Elgg APIs.
- No data migration needed.

## Known Limitations

- The `mod/` sub-plugins (`object_sort`, `user_sort`, `group_sort`, `forms_api`) are bundled
  copies that ship as Elgg plugins via Composer. They require activation in the correct order
  (forms_api → object_sort/user_sort/group_sort → site_search).
