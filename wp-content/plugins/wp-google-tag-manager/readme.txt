=== WP Google Tag Manager ===
Contributors: conlabzgmbh
Tags: google, tag, manager, tags, tagmanager, snippet, code
Tested up to: 3.4
Stable tag: 1.1
Requires at least: 3.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


Includes tags (code snippets) from Google Tag Manager into your site

== Description ==
Your wordpress website includes third party scripts and tools like website traffic analytics, ad banners counter by feedburner or some top lists. Google Tag Manager provides a comprehensive user interface to manage all these scripts (tags) and include them with a single plugin into your wordpress powered website.

This plugin includes both the javascript code to bring those tags into your site and the fall back iframe code to still provide functionality if javascript is disabled. Installation requires just two simple steps

 1. Install the plugin.
 2. Input the container ID in preferences

Instead of copy and pasting lots of script tags into you wordpress theme files or managing a couple of plugins for diverse services you can now serve this all from a single source - the Tag Manager. Tags are comfortably managed in the Google user interface, no need to further touch you wordpress installation.

Registration for Tag Manager (https://www.google.com/tagmanager/) is two simple steps before you receive the Container ID which is the only information the plugin requires to work properly. From now on, you have a couple of advantages:

 * a comprehensive overview of all tags you are using
 * tags can be deactivated in the Tag Manager
 * you can preview changes on your site before putting them live for your visitors
 * provide access to tags management for marketing staff - no code changes needed
 * use all the rules and macros to fine grained adjust tag usage

Besides management of tags you'll notice better loading times of your website due to asynchonous loading of scripts.

[youtube http://www.youtube.com/watch?v=KRvbFpeZ11Y]



Technical stuff:

Wordpress only allows placement of tag-manager-code in the footer through wp_footer action hook. So if you want to place the code directly behind the body tag you have to edit your theme like this:

...

&lt;body&gt;

&lt;?php global $wp_google_tag_manager;

if(is_object($wp_google_tag_manager) &amp;&amp; is_a($wp_google_tag_manager,"WpGoogleTagManager")){

$wp_google_tag_manager->output_manual();

} ?&gt;

...


== Changelog ==

= 1.0 =
* Initial release

= 1.1 =
* Added possibility to place tag manager code manually in themes, see description

== Installation ==
1. Upload folder `wp_google_tag_manager` into `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Enter your container id in 'Settings' --> 'WP Google Tag Manager' menu in wordpress

== Frequently Asked Questions ==

= What is google tag manager =

See [google tag manager](http://www.google.com/tagmanager/ "google tag manager").

= Where can I find my container id =

Log in [Google Tag Manager](https://www.google.com/tagmanager/)

* Navigate to
"Container" --> "Container Settings" --> "Container Snippet" --> iframe src="//www.googletagmanager.com/ns.html?id=**GTM-1A23**"
GTM-1A23 would be your container id.
See Screenshot 2.
* Or Navigate to
"Versions" --> highestNumber --> "Container Public ID"
GTM-1A23 would be your container id.
See Screenshot 3.


== Screenshots ==

1. Wp Google Tag Manager settings
2. Container id location 1
3. Container id location 2

== Upgrade Notice ==

* version 1.0
* version 1.1