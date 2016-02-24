Site Search for Elgg
====================
![Elgg 2.0](https://img.shields.io/badge/Elgg-2.0.x-orange.svg?style=flat-square)

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

