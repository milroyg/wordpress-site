=== Gettext override translations ===
Contributors: Ramon Fincken
Donate link: https://donate.ramonfincken.com
Tags: gettext, text, translation, translations, override
Requires at least: 4.0
Tested up to: 6.5.3
Stable tag: 2.0.2

GUI in backend to override texts and translations without any programming knowledge.

== Description ==

Lets you override default texts from your admin panel.<br>
Originally built to override texts from Woocommerce, but also works for all neatly added texts by WordPress Core and plugins.
<br>
It will translate all _e('') or __('') string calls, so check the PHP sourcecode of the plugin or theme you need to translate.
<br>
It will NOT translate any dynamic strings like %s or %d, so "%s has been added to your cart." is not translatable.
<br>
It will NOT translate any strings that contain HTML tags at all.
<br>
Some WooCommerce examples:<br>
WooCommerce "Select option" (dropdown value) setting
<a href="http://s.wordpress.org/extend/plugins/gettext-override-translations/screenshot-3.png">Fullscreen Screenshot 3</a><br>
WooCommerce "Select option" (dropdown value) translation result
<a href="http://s.wordpress.org/extend/plugins/gettext-override-translations/screenshot-4.png">Fullscreen Screenshot 4</a><br>
WooCommerce "Order notes" setting
<a href="http://s.wordpress.org/extend/plugins/gettext-override-translations/screenshot-5.png">Fullscreen Screenshot 5</a><br>
WooCommerce "Order notes" translation result
<a href="http://s.wordpress.org/extend/plugins/gettext-override-translations/screenshot-6.png">Fullscreen Screenshot 6</a><br>
<br>
<br>
<br>Coding by: <a href="https://www.mijnpress.nl">MijnPress.nl</a> <a href="https://twitter.com/#!/ramonfincken">Twitter profile</a> <a href="https://profiles.wordpress.org/ramon-fincken/">More plugins</a>

== Installation ==

1. Upload directory `Gettext override translations` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Click settings in your plugin list (or visit plugin submenu)
4. You are ready to go!

== Frequently Asked Questions ==

= How does this plugin work? =
It uses a gettext WordPress filter

= I have a lot of questions and I want support where can I go? =

<a href="http://pluginsupport.mijnpress.nl/">http://pluginsupport.mijnpress.nl/</a> or drop me a tweet to notify me of your support topic over here.<br>
I always check my tweets, so mention my name with @ramonfincken and your problem.


== Changelog ==
= 2.0.2 =
Bugfix: Improved for stored values 2.0.0

= 2.0.1 =
Bugfix: Improved for stored values 2.0.0

= 2.0.0 =
Bugfix: Fixed potential XSS input, by stripping all evil HTML.

= 1.0.1 =
Bugfix: Small PHP warning fixed

= 1.0.0 =
First public release


== Screenshots ==

1. Default view
<a href="http://s.wordpress.org/extend/plugins/gettext-override-translations/screenshot-1.png">Fullscreen Screenshot 1</a><br>

2. Override "Save Changes" text with "Store right away"
<a href="http://s.wordpress.org/extend/plugins/gettext-override-translations/screenshot-2.png">Fullscreen Screenshot 2</a><br>

3. WooCommerce "Select option" (dropdown value) setting
<a href="http://s.wordpress.org/extend/plugins/gettext-override-translations/screenshot-3.png">Fullscreen Screenshot 3</a><br>

4. WooCommerce "Select option" (dropdown value) translation result
<a href="http://s.wordpress.org/extend/plugins/gettext-override-translations/screenshot-4.png">Fullscreen Screenshot 4</a><br>

5. WooCommerce "Order notes" setting
<a href="http://s.wordpress.org/extend/plugins/gettext-override-translations/screenshot-5.png">Fullscreen Screenshot 5</a><br>

6. WooCommerce "Order notes" translation result
<a href="http://s.wordpress.org/extend/plugins/gettext-override-translations/screenshot-6.png">Fullscreen Screenshot 6</a><br>
