=== Collapsing Archives ===
Contributors: robfelty
Donate link: http://robfelty.com/wordpress-plugins
License: GPLv2
Tags: collapse, archives, sidebar, widget, accordion
Requires at least: 2.8
Tested up to: 6.6.1
Stable tag: 3.0.6

This plugin uses Javascript to dynamically expand or collapse the set of months for each year and posts for each month in the archive listing of your blog..

== Description ==

Create collapsible archives by year or month. Features include: link to archive pages, display of individual posts and support for custom post-types. 

== Installation ==

= WIDGET INSTALLATION =

The easiest way to use this plugin is as a widget. 

After you have installed the Collapsing Archives plugin,  then simply go the Appearance > Widgets section in wp-admin and add the Collapsing Archives Widget to whatever sidebar or widget section you like, and then configure to your heart's content.

= MANUAL INSTALLATION =

It is also possible to use the plugin on any page you like with a little bit of PHP code.  Simply change the following here appropriate (most likely sidebar.php):

Change From:

    <ul>
     `<?php wp_get_archives(); ?>`
    </ul>

To something of the following:
`
    <?php
     if( function_exists('collapsArch') ) {
      collapsArch();
     } else {
      echo "<ul>\n";
      wp_get_archives();
      echo "</ul>\n";
     }
    ?>
`
= OPTIONS AND CONFIGURATIONS =

`$defaults=array(
  'noTitle' => '',
  'inExcludeCat' => 'exclude',
  'inExcludeCats' => '',
  'inExcludeYear' => 'exclude',
  'inExcludeYears' => '',
  'sort' => 'DESC',
  'showPages' => false, 
  'linkToArch' => true,
  'showYearCount' => true,
  'expandCurrentYear' => true,
  'expandMonths' => true,
  'expandYears' => true,
  'expandCurrentMonth' => true,
  'showMonthCount' => true,
  'showPostTitle' => true,
  'expand' => '0',
  'showPostDate' => false,
  'postDateFormat' => 'm/d',
  'postDateAppenc' => 'after',
  'accordion' => 0,
  'postTitleLength' => '',
  'post_type' => 'post',
  'debug' => '0',
  );
`

* noTitle
    * If your posts don't have title, specify a string to show in place of the
      title
* inExcludeCat
    * Whether to include or exclude certain categories 
        * 'exclude' (default) 
        * 'include'
* inExcludeCats
    * The categories which should be included or excluded
* inExcludeYear
    * Whether to include or exclude certain years 
        * 'exclude' (default) 
        * 'include'
* inExcludeYears
    * The years which should be included or excluded
* showPages
    * Whether or not to include pages as well as posts. Default if false
* showYearCount
    *  When true, the number of posts in the year will be shown in parentheses 
* showMonthCount
    *  When true, the number of posts in the month will be shown in parentheses 
* linkToArch
    * 1 (true), clicking on a the month or year will link to the archive (default)
    * 0 (false), clicking on a month or year expands and collapses 
* sort
    * Whether posts should be sorted in chronological  or reverse
      chronological order. Possible values:
        * 'DESC' reverse chronological order (default)
        * 'ASC' chronological order
* expand
    * The symbols to be used to mark expanding and collapsing. Possible values:
        * '0' Triangles (default)
        * '1' + -
        * '2' [+] [-]
        * '3' images (you can upload your own if you wish)
        * '4' custom symbols
* customExpand
    * If you have selected '4' for the expand option, this character will be
      used to mark expandable link categories
* customCollapse
    * If you have selected '4' for the expand option, this character will be
      used to mark collapsible link categories
 
* expandYears
    * 1 (true): Years collapse and expand to show months (default)
    * 0 (false): Only links to yearly archives are shown
* expandMonths
    * 1 (true): Months collapse and expand to show posts (default)
    * 0 (false): Only links to yearly and monthly archives are shown
* expandCurrentMonth
    * When true, the current month will be expanded by default
* expandCurrentYear
    * When true, the current year will be expanded by default
* showPostTitle
    * 1 (true): The title of each post is shown (default)
* showPostDate
    * 1 (true): Show the date of each post 
* postDateFormat
    * The format in which the date should be shown (default: 'm/d')
* postDateAppend
    * after: The post date comes after the title (default)
    * before: The post date comes before the title 
* postTitleLength
    * Truncate post titles to this number of characters (default: 0 = don't
      truncate)
* post_type 
    * post (default)
    * page
    * all (includes regular post types plus any custom post types - excludes
      pages, revisions, wp_nav_items, and attachments)
    * custom post type that you have registered (e.g. recipe)
* accordion
    * When set to true, expanding one year will collapse all other years.
      Expanding one month will collapse all other months in that year
* number
    * If using manually with more than one instance on a page, you can give
      unique ids to each instance with this option. For example, if you had
      one instance with number 1 and another with number 2, the ul for March
      2004 for number 1 would have an id of 'collapsArch-2004-3:1', while the
      id for number 2 would be 'collapsArch-2004-3:2'
* debug
    * When set to true, extra debugging information will be displayed in the
      underlying code of your page (but not visible from the browser). Use
      this option if you are having problems

= Examples =

`collapsArch('accordion=1&sort=ASC&expand=3&inExcludeCat=exclude&inExcludeCats=general,uncategorized')`
This will produce a list with:
* accordion style expanding and collapsing
* shown in chronological order
* using images to mark collapsing and expanding
* exclude posts from  the categories general and uncategorized

`collapsArch('post_type=recipe')`
This will produce a list with:
* only posts of type 'recipe'
* shown in chronological order
* using images to mark collapsing and expanding
* exclude posts from  the categories general and uncategorized

= CAVEAT =

This plugin relies on Javascript, but does degrade
gracefully if it is not present/enabled to show all of the
archive links as usual.

== Frequently Asked Questions ==

=  How do I change the style of the collapsing archives lists? =

  The collapsing archives plugin uses several ids and classes which can be
styled with CSS. These can be changed from the settings page. You may have to
rename some of the id statements. For example, if your sidebar is called
"myawesomesidebar", you would rewrite the line 

  #sidebar li.collapsArch {list-style-type:none}
  to
  #myawesomesidebar li.collapsArch {list-style-type:none}

If you are using the plugin manually (i.e. inserting code into your theme),
you may want to replace #sidebar with #collapsArchList

= There seems to be a newline between the collapsing/expanding symbol and the
category name. How do I fix this? =

If your theme has some css that says something like

#sidebar li a {display:block}

that is the problem. 
You probably want to add a float:left to the .sym class
   
== Screenshots ==

1. Collapsing archives with default theme
2. widget options

== Demo ==

I use this plugin in my blog at http://blog.robfelty.com



== CHANGELOG ==

= 3.0.6 (2024.08.05) =
* Sanitizing input for block.
* Verified works with WP 6.6
* Updated Readme a bit

= 3.0.5 (2023.12.07) =
* accidentally messed up tagging version 3.0.4. Calling it 3.0.5 now

= 3.0.4 (2023.12.01) =
* Got rid of deprecated __experimentalGroup
* Tested with WP 6.4.1

= 3.0.3 (2023.09.11) =
* Fixed PHP warning about undefined key when changing style

= 3.0.2 (2023.06.04) =
* Calling it stable version
* Tested with WP 6.2.2
* Known issue - does not work with Jetpack widget visibility settings

= 3.0.1 (2023.04.01) =
* Fixed linkToArch option
* Fixed taxonomy type option
* Added SameSite attribute to cookies
* A bit of code cleanup

= 3.0.0 (2023.03.28) =
* Converted to Gutenberg block to be usable by full-site editing themes
* Compatible with WP 6.2
* Got rid of dependency on jQuery (no more animations)

= 2.1.4 (2023.01.12) =
* Fixed a different warning about enqueuing scripts

= 2.1.3 (2022.03.10) =
* Fixed a deprecation warning about enqueuing scripts

= 2.1.2 (2022.03.10) =
* Fixed an issue with the full-site editor

= 2.1.1 (2022.03.10) =
* Compatible with WP 5.9.1
* Fixed some deprecated warnings

= 2.1 (2021.06.18) =
* Compatible with WP 5.7
* Incorporated a number of code improvements from my other collapsing plugins
* Tested on WP 5.7.2
* Updated documentation a bit
* Added some screenshots and icons

= 2.0.5 (2017.08.17) =
* Compatible with WP 4.8
* Cleaned up code some to reduce warnings

= 2.0.4 (2017.01.02) =
* Compatible with WP 4.7
* Fixed bug with jquery compatibility
* Removed deprecated mysql debugging info
* Works with PHP 7

= 2.0.3 (2015.08.12) =
* Compatible with WP 4.3

= 2.0.2 (2014.09.24) =
* Fixed bug when expanding years but not showing month links, where the oldest posts were not getting displayed

= 2.0.1 (2014.09.05) =
* Compatible with WP 4.0

= 2.0 (2012.04.09) =
* now using all jquery for javascript stuff
* added accordion option
* added option to not use cookies
* Fixed display issues with IE
* Easier style handling
* Added support for custom post types

= Older versions =
* See changelog.txt
