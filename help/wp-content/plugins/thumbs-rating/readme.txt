=== Thumbs Rating ===
Contributors: quicoto
Tags: ratings, thumbs, votes, AJAX, rating, thumb, vote, page, post
Requires at least: 4.8
Tested up to: 5.0
Stable tag: 3.4.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Thumbs Rating does what you'd expect. It allows you to add a thumbs up/down to your content (posts, pages, custom post types...).

== Description ==

I needed a simple and light plugin to add Thumbs Rating, I couldn't find any so I built my own.

This plugin allows you to add a thumb up/down rating to your content. You can set it up to show anywhere you want, check out the Installation instructions.

The output is very basic, no images, no fonts, no fancy CSS. Customize the ouput overriding the CSS classes in your __style.css__ file.

= Features =

*   No output printed by default, __check the Installation instructions__.
*   Stores the votes values for each content in the current post table (no new database tables are created).
*   Uses HTML5 LocalStorage to prevent the users from voting twice.
*   Easy to customize the output using CSS.
*   Show the most voted (positive/negative) items using shortcodes.
*   Show the buttons using shortcodes.

= Languages =

*	Arabic: ar (by [iFlendra](https://profiles.wordpress.org/iFlendra/))
*	Catalan: ca
*	Chinese (China): zh_CN (by [suifengtec](http://wordpress.org/support/profile/suifengtec))
* Czech: cs_CZ (by [togur](http://wordpress.org/support/profile/togur/))
* Danish dk_DK (by BJARNE)
* Dutch nl_NL (by [Thijs](http://wordpress.org/support/profile/thijsku))
*	English
*	French: fr_FR (by Arnaud)
* German: de_DE (by [webserviceXXL](http://profiles.wordpress.org/hvbx/))
* Italian: it_IT (by [marcochiesi](http://profiles.wordpress.org/marcochiesi/))
* Japanese: ja (by heySister721)
*	Lithuanian: lt_LT (by Andrius)
*	Persian: fa_IR (by [Hamed.T](http://wordpress.org/support/profile/hamedt))
*	Portuguese: pt_BR (by Felipe)
*	Polish: pl_PL (by [Fafu](https://wordpress.org/support/profile/fafu))
*	Romanian: ro_RO by (by [AlexCruz1989](https://wordpress.org/support/profile/alexcruz1989))
*	Russian: ru_RU (by [anatolt](http://wordpress.org/support/profile/anatolt))
*	Serbian: sr_RS (by Andrijana Nikolic)
*	Spanish: es_ES
*	Turkish: tr_TR (by [CrimsonIdol](https://profiles.wordpress.org/CrimsonIdol/))

Give me a hand and translate the plugin in your language, it's just a few words.

= Requests =

Feel free to post a request but let's keep it simple and light.

= Ping me / Blame me =

Are you using the plugin? Do you like it? Do you hate it? Let me know!

* Twitter: [@ricard_dev](http://twitter.com/ricard_dev)
* Blog: [Rick's code](http://php.quicoto.com/)

== Installation ==

First of all activate the Plugin, then:

A) Add the shortcode to the posts or pages you want the Thumb Rating buttons to appear:

`[thumbs-rating-buttons]`


B) If you want to show the thumbs after all your content (posts, pages, custom post types) paste this snippet at the end of your __functions.php__ file of your theme:

`function thumbs_rating_print($content)
{
  return $content.thumbs_rating_getlink();
}
add_filter('the_content', 'thumbs_rating_print');`

C) Alternatively you can print the buttons only in certain parts of your theme. Paste the following snippet wherever you want them to show:

`<?=function_exists('thumbs_rating_getlink') ? thumbs_rating_getlink() : ''?>`

== Frequently Asked Questions ==

= I activated the plugin and I don't see the buttons =

You must specify where do you want to show the thumbs within your theme, __check out the Installation instructions__.

= Can I customize the colors? =

Absolutely. Check out the CSS within the plugin (__thumbs-rating/css/style.css__) and override the classes from your theme's __style.css__ file.

= When I sort the admin columns some posts disappear =

If the post/page has 0 votes for the column your trying to sort, WordPress hides it.
It only shows the posts/pages with at least +1 or -1 votes.

= How do I show the number of votes in other parts of my theme? =

Paste the following snippets inside the loop:

`<?=function_exists('thumbs_rating_show_up_votes') ? thumbs_rating_show_up_votes() : ''?>`

`<?=function_exists('thumbs_rating_show_down_votes') ? thumbs_rating_show_down_votes() : ''?>`

(Both functions accept the post ID as a parameter in case you need it)

= Shortcode =

The shortcode [thumbs_rating_top] accept the following parameters:

*	type: positive (default) / negative
*	posts_per_page: 5 (default)
*	category: ID (default = all)
* show_votes: yes (default) / no
* post_type: any (default) / post / page / books
* show_both: no (default) / yes
* order: DESC (default) / ASC
* orderby: DESC (default)
* exclude_posts: "133,2,54,234" (ID of posts separated by commas)
* include_posts: "133,2,54,234" (ID of posts separated by commas)

Here's an example using some parameters:

`[thumbs_rating_top type="positive" posts_per_page="10" post_type="post" show_votes="no" order="DESC"] `

= The shortcode in Widgets or Comments doesn't work =

You might need to allow shortcodes in that sections, [here's how](http://php.quicoto.com/how-to-allow-shortcodes-to-wordpress-comments/).

== Screenshots ==

1. Basic output with the default CSS with the TwentyThirteen theme.
2. This text is shown if you try to vote again.

== Changelog ==

= 3.4.2 =
* Fixes critial bug with the shortcode not showing results.

= 3.4.1 =
* Fixes critial bug with the shortcode not showing results.

= 3.4 =
* Add include_posts parameter in shortcode.

= 3.3 =
* Add orderby option in shortcode

= 3.2 =
* Add exclude_posts option in shortcode (props @hpd2311)

= 3.1 =
* Author column should not be removed from admin (spotted by sosojni)

= 3.0 =
* Added Arabic translation ar


== Upgrade Notice ==

= 3.3 =
* Add orderby option in shortcode

= 3.2 =
* Add exclude_posts option in shortcode (props @hpd2311)

= 3.1 =
* Author column is not visible in admin, as it should.

= 3.0 =
* Added Arabic translation ar
