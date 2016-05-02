# Tag Swapper Plugin

Status:  *In Beta*

The Tag Swapper allows you to swap out the HTML tag element based upon the configured attribute and its value. For example, let's say you inherit an older database that needs to convert some `p` tags to `h1` tags for SEO (just one use case).  Tag Swapper finds all of the `p` tags that match the `attribute` and its value that you configure, swaps the tag, and then saves it back to the database.  No more having to go from post-to-post or page-to-page to change the tags.  Tag Swapper takes care of it for you.

Example:

You have this content: `<p class="large-text headline">some content</p>`.  You want to replace the `p` tag with a `h1` when the `class` has a `headline` value.  Tag Swapper finds only those HTML elements with this pattern and changes it to: `<h1 class="large-text headline">some content</h1>`.

Every single occurrence in the `p` tag that also has a `class` attribute value of `headline`, regardless if it also has other styling classes, is changed.

## FAQ

#### How does it decide what to swap?

It uses the options that you configure on the `Tools > Tag Swapper` page.  First it fetches all of the nodes that use the HTML tag you want to replace.  For example, let's say you want to replace all `p` tags that have a `class` attribute with a styling class of `headline`.  The first step is to get all of the nodes that are `p` tag elements.  Then it checks if the `class` attribute has the styling class you selected.  If it does, then it swaps the `p` tag for the one you specified, e.g. `h1`.

#### Does it update the database?

Yes.  Once it's done swapping the tags, then it will save only those records that were swapped back to the database.  It does this in one query to speed things up (i.e. verses doing an update on each and every record).

#### Does it work on post meta or custom database tables?

Not yet. That is a future enhancement.  Right now, it only works with the `wp_posts` database table, i.e. where all of the posts, pages, custom post types, navigation, media, etc. are all stored.

#### What does it change in the database?

It fetches only the `post_content` column (well plus the post `ID`).  This column is where the content is stored, i.e. the content that appears in the WordPress tinyMCE editor.  If it finds a match, it will make the tag swap and then save the updated version back to the database.

#### Does it swap both the opening and closing tag elements?

Yes.  That's the beauty of using PHP `DomDocument`, as it handles this for you.  So if you want to swap a `p` tag with a `h1`, it handles both the opening and closing tags. Cool, eh?

#### Does it use REGEX?

Nope.  REGEX is not good with searching for all the different patterns and combinations.  Instead, this plugin converts the content into a HTML document, i.e. using PHP `DOMDocument`.  It then traverses through the native HTML nodes.  This technique allows the plugin to only fetch the content that has the tag you want to replace and deal with the individual attributes.  No REGEX or pattern matching required.

#### What are the PHP Warnings when running the tag swapper?

If you are running a plugin such as [Query Monitor](https://wordpress.org/plugins/query-monitor/) or similar, you may see the PHP `DOMDocument` warnings if you have malformed HTML.  The plugin suppresses these warnings by default.  A future enhancement will include a report to help you identify where the issues are in your HTML.

The warnings are generated *if and when* you have malformed or invalid HTML markup.  For example, if you are missing closing `</div>` tags, have duplicate `ID` attributes, or have invalid markup, then `DOMDocument` generates warnings to alert you.  As these warnings point to where the HTML is loaded in the Swapper, they do not help you find the issues in your markup.  Therefore, a future enhancement will gather up these issues and generate a handy report for you.

## Features

You configure the parameters including:

1. the HTML tag element to be replaced
2. the new HTML tag element, e.g. `p`, `h1`, `div`, etc.
3. the search attribute, e.g. `class`, `id`, or `data`
4. the attribute value, e.g. `class` attribute of `headline`
5. the post type, as WordPress' posts database table contains posts, pages, revisions, media, custom post types, and more

Other features include:

1. Count the records to know how many will be swapped.
2. Suppress HTML malformed errors, just in case your HTML is not fully compliant
3. Limits `h1` tag swap to only one occurrence per record (document).

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
7. Then click on the "Run the Tag Swapper" button.

## Yet to Do

Before I release this officially to [WordPress.org](https://worpress.org), there a few more things to do including:

1. Add error messaging
2. Test on a really HUGE database, i.e. over 5k pages or posts
3. Run it through more beta testers.

Future Enhancements:

1. Expand it to more than just the posts database table.
2. Generate a report to show you where invalid HTML is occurring in your content.
3. Generate report for non-swapped tags when `h1` is limited to only one occurrence.

## Contributions

All feedback, bug reports, and pull requests are welcome.


## Special Thanks

A special thank you to [Jackie D'Elia](http://jackiedelia.com) for beta testing this plugin.