=== StatusNet Widget ===
Contributors: zhenech
Donate link: https://flattr.com/thing/57411/StatusNet-Widget-for-WordPress
Tags: twitter, identica, statusnet, widget, ohloh
Requires at least: 2.8
Tested up to: 3.2
Stable tag: 0.3

StatusNet Widget provides a widget to pull your status from StatusNet
sites like identi.ca. Multiple sources are supported, so is Twitter
and Ohloh.

== Description ==

This widget aims to provide an easy integration of your StatusNet (identi.ca),
Twitter and Ohloh timelines into your WordPress.
It can also be used to display timelines for tags or groups.
You can provide multiple sources (= links to your profiles at identi.ca, Twitter
or your own StatusNet installation), which will get fetched, merged (if you want
it to do so, like when you are posting the same content to Twitter AND identi.ca)
and displayed in your sidebar (with #hashtag, !group and @people links).


== Installation ==

1. Upload the `statusnet-widget` directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Add the Widget in the 'Widgets' menu in Wordpress
   * 'Merge Mode' means duplicate statuses will be ignored
   * 'Prefer content with markup' means the markup delivered by StatusNet/Twitter etc will be used instead of parsing the message for @users, #tags, !groups and links ourself.
   * 'Source List' should contain one source per line
     (where source is something like http://identi.ca/evgeni or http://twitter.com/zhenech)

== Frequently Asked Questions ==

= How do I fetch a #tag/!group from identi.ca (StatusNet)? =
Add http://identi.ca/tag/debian or http://identi.ca/group/debian as a source.

= How do I fetch a #tag from Twitter? =
Add http://search.twitter.com/#debian as a source.

= How do I fetch a user/project from Ohloh? =
Add https://www.ohloh.net/accounts/EvgeniGolov or http://www.ohloh.net/p/debian as a source.

= What is 'Prefer content with markup'? =
When this is checked, arbitrary markup is parsed from the source. I personally do not like the idea of displaying markup from a remote/unknown/untrusted source in my site. In theory Twitter and co could inject any markup (incl JavaScript!) into my site.

However, in certain setups, such as StatusNet's federation, this will fix username links when replying to people across instances (i.e. from a personal StatusNet site to indenti.ca).

Consider the risks, and decided if this works for you.

== Screenshots ==

1. StatusNet Widget in action at http://www.die-welt.net, fetching twitter.com/zhenech
   and identi.ca/evgeni
2. StatusNet Widget settings in the WP admin panel

== Changelog ==

= 0.3 =
* search.twitter.com support, via http://search.twitter.com/what_to_search
* ability do display StatusNet/Twitter/etc markup instead of parsing messages ourself
* support statusnet instances in subdirs

= 0.2 =
* Ohloh support by Michal Hrusecky <Michal@Hrusecky.net>
* Initial i18n support and German translation.
* Czech translation by Michal Hrusecky <Michal@Hrusecky.net>
* Spanish (es_MX and es_ES) translation by Fernando C. Estrada <fcestrada@fcestrada.com>

= 0.1 =
* First public release.
