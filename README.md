Site Search for Elgg
====================
![Elgg 7.x](https://img.shields.io/badge/Elgg-7.x-orange.svg?style=flat-square)

## Features

 * Improved UX/UI of search pages
 * Removes tag search and integrates it with general searches
 * Provides solid foundation for introducing advanced search

![Object Search](https://raw.github.com/hypeJunction/Elgg-site_search/master/screenshots/search.png "Object Search")
![User Search](https://raw.github.com/hypeJunction/Elgg-site_search/master/screenshots/user_search.png "User Search")

## Notes

Currently, due to the limitations of the search plugin, this plugin implements it's own search hooks,
therefore custom search implemented by other plugins might not work.
To implement custom search type, add a view to `lists/search/$search_type` and render your own list.

## Compatibility

| Plugin version | Elgg version |
|---|---|
| 7.0.0   | 7.x  |
| 6.0.0   | 6.x  |
| 5.0.0   | 5.x  |
| 4.0.0   | 4.x  |
| 3.0.0   | 3.x  |
