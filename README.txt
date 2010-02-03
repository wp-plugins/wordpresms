=== Plugin Name ===
Contributors: Christoph Burgdorfer, coANDcoUK.com / coUNDco.ch
Donate link: http://www.wordpresms.com
Tags: sms, mobile, cms, text, text messages, long sms, long messages, germany, austria, switzerland, uk
Requires at least: 2.8
Tested up to: 2.9
Stable tag: 0.06


Use WordPress as a mobile content/SMS CMS with WordpreSMS. People text in and get SMS content up to 600 characters long back (concatenated SMS).

== Description ==

The WordpreSMS plugin allows you to use Wordpress as a mobile SMS content management system. You can set up a Keyword on a given Short Code. If users send that keyword to the short code, the system will reply with an up to 600 characters long SMS to the user. You need an account on [www.wordpresms.com](http://www.wordpresms.com "WordpreSMS") to get started.

Due to the nature of SMS services and short codes, the usage is limited to certain countries. At the moment, WordpreSMS is supported by mobile phone operators in:

* United Kingdom
* Germany
* Switzerland
* Austria

Live demo under [demo.wordpresms.com](http://demo.wordpresms.com "WordpreSMS Demo")

**Important:** This project is still in Beta phase. All feedback, recommendations and ideas should be fed back to [feedback@wordpresms.com](mailto:feedback@wordpresms.com "WordpreSMS Feedback")



== Installation ==

1. Upload `wordpreSMS.php` to the `/wp-content/plugins/wordpreSMS` directory
1. Get an account on [www.wordpresms.com](http://www.wordpresms.com "WordpreSMS")
1. Use the activation token you get on [www.wordpresms.com](ttp://www.wordpresms.com "WordpreSMS") in the plugin settings to activate the plugin.

== Frequently Asked Questions ==

= How does the end user request content? =

The end user sends a couple of keywords to a short code to get the content. The default keyword is BLOG followed by an account keyword of your choice (e.g. BOB) and an optional subkeyword (e.g. ADDRESS). The keywords are not case sensitive.

The end user will send: **BLOG BOB ADDRESS** to your country short code and receive the Address of your blog back as an SMS.

You can also choose either a dedicated keyword or a dedicated short code at additional costs if you're not happy with the defaults.

= What are the short codes in the countries? =

* UK 83333
* DE 31000
* CH 266
* AT 0900414141

= Do pictures or videos work with this? =

No, this service works only with text content. However the text can contain links to URLs which are mobile capable so the user can be pointed to a mobile site via SMS.

= But if the text is longer than 160 characters? =

Today's mobile phones can cope with so called 'long SMS' of up to 800 characters. WordpreSMS supports long SMS up to 480 characters. But please be aware that the end user pays per 160 characters. If an SMS is concatenated of multiple messages, he has to pay each SMS.

= Does this service cost? =

No, this service is free to you as a blogger. However for people requesting information, there will be a small premium charge to cover the messaging costs.

= Does this service work all over the world? =

No. This plugin only works in certain countries. We are constantly expanding the reach of WordpreSMS however we focus on the European markets, currently.

= Can I change my keywords as much as I want? =

Yes, you can change your keywords as much as you like. The only restriction is that it needs to be available when you change it. Keywords are given away on a "First come, first serve" basis.

= What Keywords can I use? =

Keywords must consist of letters and numbers and must not contain special characters. Furthermore they must be 3 characters or more. Other than that, there is no restriction.

== Screenshots ==

1. Choosing a keyword from the WordpreSMS.com interface screenshot-1.png
2. How your post interface will look like screenshot-2.png
3. How your WordpreSMS settings in the WordPress dashboard will look like screenshot-3.png

== Changelog ==

= 0.06 =
* Initial Version

== Upgrade Notice ==

= 0.06 =
N/A