# Tag Swapper Plugin

This plugin is currently in development.

The Tag Swapper allows you to swap out the HTML tag element based upon the configured attribute and its value.  It queries the post database records, searches using `DomDocument`, swaps out any matching tags, and then saves the records back to the database.  Migrating an old database or needing a quick way to replace out HTML, ok, here it is.

### How does it decide what to swap?

It uses the options that you configure on the `Tools > Tag Swapper` page.  First it fetches all of the nodes that use the HTML tag you want to replace.  For example, let's say you want to replace all `p` tags that have a `class` attribute with a styling class of `headline`.  The first step is to get all of the nodes that are `p` tag elements.  Then it checks if the `class` attribute has the styling class you selected.  If it does, then it swaps the `p` tag for the one you specified, e.g. `h1`.

### Does it update the database?

Yes.  Once it's done swapping the tags, then it will save only those records that were swapped back to the database.  It does this in one query to speed things up (i.e. verses doing an update on each and every record).

## Features

You configure the parameters including:

1. the HTML tag element to be replaced
2. the new HTML tag element, e.g. `p`, `h1`, `div`, etc.
3. the search attribute, e.g. `class`, `id`, or `data`
4. the attribute value, e.g. `class` attribute of `headline`
5. the post type, as WordPress' posts database table contains posts, pages, revisions, media, custom post types, and more

Other features include:

1. Count the records to know how many will be swapped.
2. Suppress HTML malformed errors, just in cause your HTML is fully compliant

## Installation

1. Download it.
2. Put into your `wp-content/plugins/` folder
3. Extract it
4. Go into the new folder
5. Run `composer install --no-dev` in terminal to bring in the dependencies and install Composer locally.
6. Back up your database

Installation from GitHub is as simple as cloning the repo onto your local machine.  To clone the repo, do the following:

1. Using PhpStorm, open your project and navigate to `wp-content/plugins/`. (Or open terminal and navigate there).
2. Then type: `git clone https://github.com/hellofromtonya/Tag-Swapper`.
3. Go into the new folder
4. Run `composer install --no-dev` in terminal to bring in the dependencies and install Composer locally.
5. Back up your database

## Configuring the Tag Swapper

1. Set the HTML tag you want to find and replace (default is `p`).
2. Set the HTML tag you want to replace it with (default is `h1`).
3. Set the search attribute (the default is `class`).
4. Type in the search attribute value.  For example, if you specified a `class` attribute, then type in the styling class that you want to find.
5. Select the Post Type, such as Posts or Pages.
6. If you want to only count the records that will be swapped, click on "Yes" for the "Just count the records" option.
7. By default, the HTML malformed errors are suppressed.  However, if you want to see them, then click on the "No" option.
8. Then click on the "Run the Tag Swapper" button.

## Contributions

All feedback, bug reports, and pull requests are welcome.