=== WPostGrabber ===
Contributors: fherryfherry
Tags: grabber, grab, robot, post grab, aggregation, atom, autoblogging, bot, rss to post, syndication, writing auto, autoposter, autoblog, auto poster, post by url, parsing html, parser html, parsing content, parser content, grab content, content grabber, content scrape, scraping, scrapper, auto scrape, robot blog, grab content easy, get content by url
Donate link: http://websprogramming.com
Requires at least: 2.9
Tested up to: 4.3
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/license-list.html#GPLCompatibleLicenses

WPostGrabber, this plugin will help you to get your content on the website that you want instantly.

== Description ==
WPostGrabber, this plugin will help you to get your content on the website that you want instantly. You only need to enter the URL of the intended content. Then WPostGrabber will perform the extraction of title and content.

The following are the main features :

1. Smart Content Grabber (Include Images)
1. Title Grabber
1. Auto-create tag
1. RSS Feed Viewer
1. Auto move external image host to your local host. (even if you do not use WPostGrabber a.k.a grab)
1. Auto set Featured Image (even if you do not use WPostGrabber a.k.a grab)
1. Auto Article Spin ( UPCOMING !! )

If you want to restrict the words in the title or content, you can fill in your own words that you think are not allowed in the input box "Disallow Word Title" for the title, and "Disallow Word Content" for content. You can fill it by separating each word with a comma ",".

After WPostGrabber finish grab url, title, content will be auto place on Wordpress Input and Textarea.

WPostGrabber plugin supports only url of website that contains articles or news, I do not guarantee to support the website pictures or website video. Also i do not guarantee any rss / feed can grab. 

= Setting Location =
Please navigate to Settings -> WPostGrabber

= System Requirements = 
1. curl (allow_url_fopen)
1. php >= 5.2
1. mb_convert_encoding
1. Wrapper

= Agreement Statement =
 I am willing and agree that I am solely responsible and accepts any risk that may occur upon the use of this plugin. And I agree that I am using this plugin with the "DO WITH YOUR OWN RISKS !"

* Fanspage : 
http://www.facebook.com/wpostgrabber/
* Homepage : 
http://websprogramming.com

== Installation ==
1. Upload zip  to the `/wp-content/plugins/` directory and extract, or directly upload from your Plugin management page.
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Done.

== Frequently Asked Questions ==
= How prevent duplicate images (Thumbnail) at Media Library ? =
As general solution, please navigate to Settings -> Media . Set ALL VALUE to 0. (thumbnail, medium, large size)

= Is it possible to perform a grab on the website image? =
Basically WPostGrabber will take content and images on the destination url. But I can not guarantee, because this time the plugin is not intended specifically for the website picture.

= Can do grab the video website? =
Sorry there is WPostGrabber can not.

= What are the success factors taking the content of the website url destination? =
There are several factors that must be considered:
1. The power of your server
1. Internet connection you when the process because there is a lag time to wait when the process, so if your internet is bad, can cause this process to fail.
1. Server Website The purpose is good or bad (bad meaning that the destination server is slow or unstable).

= Image not read properly when finished grab? =
There are several factors that must be considered:
1. URL of the image is not FULL URL url aliases only partially.
1. Website aim of protecting the image, so the image can not be attached to other websites.

== Screenshots ==
1. This screenshot description about location this plugin
2. 1.0.7 version with RSS Viewer

== Changelog ==
= 1.0.0 = 
* First Release

= 1.0.1 =
* fix ajax timeout and error handle
* fix footnote link at result content
* fix timeout to 15000 ms

= 1.0.2 = 
* improve content html5 grab
* increase memory needed

= 1.0.3 =
* improve copy image source to local server
* improve auto add featured image

= 1.0.4 =
* fix activate error_reporting
* fix "update" plugin setting to "update_options"
* improve gethtml() function with content-type / mime checker, as we only need html content-type
* fix bug cache gethtml()

= 1.0.5 = 
* fix general function name
* fix short tags
* fix rearrange ajax script

= 1.0.6 = 
* fix bug cache html

= 1.0.7 =
* New RSS viewer feature
* improve desain
* change setting to own page setting

= 1.0.8 = 
* fix str_ireplace to str_replace 

= 1.0.9 = 
* fix image copy from destination to local host
* fix animation after click grab
* redesain setting panel

= 1.1.0 =
* add encoding UTF-8 to DOMDocument
* update FAQ ( duplicate images )
* add alternate function to prevent create images thumbnail
* fix problem images uploaded wheter post publish or not

= 1.1.1 =
* improve grab function

= 2.0.0 =
* Increase limit grab to 250 times per day
* Add API KEY method
* Add More URL Feed
* Fix bug : blank content when manual / standard posting

= 2.0.1 =
* fix file_get_contents bug on some server

= 2.0.2 =
* fix client side grab server

= 2.0.3 =
* change server to ip address

= 2.0.4 = 
* remove api key verification

== Upgrade Notice ==

= 1.0.0 =
First release

= 1.0.1 = 
there are some bug and fix it

= 1.0.2 = 
improving plugin performance so more powerfull

= 1.0.3 = 
improving plugin so it can save imges and auto featured image

= 1.0.4 = 
To prevent blind error php, i remove error_reporting. I also change method of update setting from file based to wp tables options.
to prevent any url content-type can submitted, i put mime/content-type checker every url submmited will be check. Because only HTML content-type that will accept.

= 1.0.5 = 
prevent conflict / duplicate function name & rearrange ajax script.

= 1.0.6 = 
fix bug cache html

= 1.0.7 =
* New RSS viewer feature
* improve desain
* change setting to own page setting

= 1.0.8 = 
* fix str_ireplace to str_replace 

= 1.0.9 = 
* fix image copy from destination to local host
* fix animation after click grab
* redesain setting panel

= 1.1.0 =
* add encoding UTF-8 to DOMDocument
* update FAQ ( duplicate images )
* add alternate function to prevent create images thumbnail
* fix problem images uploaded wheter post publish or not

= 1.1.1 =
* improve grab function

= 2.0.0 =
* Increase limit grab to 250 times per day
* Add API KEY method
* Add More URL Feed
* Fix bug : blank content when manual / standard posting

= 2.0.1 =
* fix file_get_contents bug on some server

= 2.0.2 =
* fix client side grab server

= 2.0.3 =
* change server to ip address

= 2.0.4 = 
* remove api key verification