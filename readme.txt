=== StatusNet Widget ===
Contributors: zhenech
Donate link: https://flattr.com/thing/57411/StatusNet-Widget-for-WordPress
Tags: twitter, identica, statusnet, widget
Requires at least: 2.8
Tested up to: 3.0.4
Stable tag: 0.1

StatusNet Widget provides a widget to pull your status from StatusNet
installations like identi.ca. Multiple sources are supported, so is Twitter
and Ohloh (even if they are not StatusNet instances).

== Description ==

This widget aims to provide an easy integration of your StatusNet (identi.ca)
Twitter and Ohloh timelines into your WordPress.
You can provide multiple sources (= links to your profiles at identi.ca, Twitter
or your own StatusNet installation), which will get fetched, merged (if you want
it to do so, like when you are posting the same content to Twitter AND identi.ca)
and displayed in your sidebar (with #hashtag, !group and @people links).


== Installation ==

1. Upload the `statusnet-widget` directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Add the Widget in the 'Widgets' menu in Wordpress
   * 'Merge Mode' means duplicate statuses will be ignored
   * 'Source List' should contain one source per line
     (where source is something like http://identi.ca/evgeni or http://twitter.com/zhenech)

== Changelog ==

= 0.2 =
* Ohloh support by Michal Hrusecky <Michal@Hrusecky.net>
* Initial i18n support and German translation.

= 0.1 =
* First public release.
