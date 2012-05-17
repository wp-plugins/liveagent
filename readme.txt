=== Live Agent free chat & helpdesk===
Contributors: jurajsim
Tags: live chat, live agent, help desk, help desk software, live chat software, live agent software, chat, online support, support, mails, mail handler, qualityunit,twitter,facebook,social
Requires at least: 3.0.1
Tested up to: 3.3.2
Stable tag: 3.0.2
Support Link: http://support.qualityunit.com/

Wordpress LiveAgent plugin integrates well known help desk and live chat software into any Wordpress installation. No HTML knowledge is required.

== Description ==

Wordpress-LiveAgent free plugin integrates full featured help desk and live chat software [Live Agent](href='http://www.qualityunit.com/liveagent/#wordpress) "Live Agent software") into any Wordpress installation.
It can provide complete customer support platform with hybrid chat and email tickets. You will have 100 chats/tickets per month for free.
It will be installed in our cloud. We handle backups, traffic and performance for you.
Simply add "start chat" button by few simple clicks and be live within 5 minutes.

= Features =

1. **Online Chat** 
Your customers can connect with you in real time using live chat button on your website
You can convert your visitors into customers much faster as before (studies shows average 20% increase of sales with live chat on website)

2. **Help Desk** 
Your customers can reach you by emails (you can scan unlimited number of mail accounts and sort emails into multiple departments)
You can answer emails in same way as if it would be chat - customer will get immediate answers

3. **Offline Messages**
In case your support is not online, your customer can leave you offline message, which will be later asnwered by email

4. **Facebook**
With LiveAgent you can monitor all your Facebook fan pages and answer messages from one place. LiveAgent will turn each wall message into ticket, which could be answered by your suport team in the same way as email, chat or tweet.

5. **Twitter**
LiveAgent lets you backup all your tweets and key word mentions. You will be able to search tweets any time in the future and review complete communication with your customers in ticket timeline.

6. **Knowledge Base & Forum**
LiveAgent Knowledge Base will help you to reduce customer support costs and workload while improving overall customer satisfaction. Encourage your customers to help themselves. LiveAgent will help you to deliver answers to common problems much quicker as other knowledge base softwares.

It doesn't matter, if your customer will contact you by email, chat, Facebook or Twitter LiveAgent can handle all types of support channels into one hybrid communication.
You can keep full history of conversations with your customers, you have full overview about the past of customer on one page.
 
You can find more info about Live Agent [here](href='http://www.qualityunit.com/liveagent/#wordpress "Live Agent software")
= Promotional video =
[vimeo http://vimeo.com/34832602]

= Plugin features =

* Integrates wordpress with live chat and help desk sfotware (Live Agent)
* Automatic inclusion of live chat buttons in your pages, no HTML knowledge required

== Installation ==
No HTML knowledge is required and integration will take you less than 5 minutes.
You can also visit our fully described <a href='http://support.qualityunit.com/knowledgebase/live-agent/integration/wordpress-plugin.html' target='_blank' title='Installation guide'>step-by-step guide</a> on our pages.

Here is the basic guide:
1. Unzip `liveagent.zip` to the directory `/wp-content/plugins/liveagent`
2. Activate Live Agent plugin
3. Create Free trial account or install Live Agent on your own server
4. Set account user credentials in menu "Live Agent"
5. Go to menu Live Agent -> Buttons. Choose button you like to have online on your website and click Save.
6. Go to live agent admin panel and start answering chats and emails of your visitors. 

== Frequently Asked Questions ==

= What is Live Agent? =
Live Agent is full featured help desk and live web chat software integrated into one powerful support platform.
For more info check out [this page](href='http://www.qualityunit.com/liveagent/ "Live Agent")

= Is it free? =
Yes it is if you create account with our plugin. You'll get free account which support multiple channels (mail, chat, facebook, twitter, etc.). Only limitation is 50 chats/mails per month.

= Is plugin avaliable also in my language? =
From version 3.0.0 plugin supports translations to any language that WordPress supports. So if you have time and will you can help us to improve it. [Make your own translation](href='http://support.qualityunit.com/634703-Making-custom-translation' "Custom translation") and send it to us. We add it to next plugin release. 


== Screenshots ==

1. Example of conversation detail in LiveAgent
2. Example of Visitor's chat window
3. Account settings configuration screen in Wordpress
4. Account creation screen
5. example of default chat button on page

== Changelog ==

= 3.0.2 =
* add hungarian translation

= 3.0.1 =
* add slovak translation

= 3.0.0 =
* use native Wordpress ui
* user experience enhancements
* better settings handling
* better user privileges handling
* internalization support

= 2.0.1 =
* readme changes only

= 2.0.0 =
* code refactoring
* simplification of user interface and code complexity

= 1.2.11 =
* fixed not functional signup

= 1.2.10 =
* internal speed up
* comaptible with Wordpress 3.3.1

= 1.2.9 =
* internal improvements

= 1.2.5 =
* bugfixes with delayed account creation problem

= 1.2.4 =
* bugfixes

= 1.2.3 =
* fixed empty buttons grid after signup
* added one screenshot

= 1.2.2 =
* bugfixes

= 1.2.1 =
* bugfixes
* new screenshots

= 1.2.0 =
* added account creation wizard

= 1.1.5 =
* short description update 

= 1.1.4 =
* fix error with loguot from agent panel

= 1.1.3 =
* add test classes, now all new code should be unit-tested
* add support for showing Visitors on WordPress page as table in Dashboard
* show notification to admin if no chat button is enabled 

= 1.1.2 =
* fix: Fatal error: Class 'La_Lang' not found in C:\wamp\www\wordpress\wp-content\plugins\liveagent\PhpApi.class.php on line 65

= 1.1.1 =
* minor bugfixes

= 1.1.0 =
* speed optimalisations
* advanced algorithm that inserts buttons into sidebars, should be independent from theme
* fixed crash when theme not using sidebars (only float buttons can be used then)

= 1.0.8 =
* minor code changes

= 1.0.7 =
* fatal error fix with unexistant function

= 1.0.6 =
* automatically add http to domain name if it do not already contains it
* minor account screen positions change

= 1.0.5 =
* minor bugfixes

= 1.0.4 =
* minonr bugfixes

= 1.0.4 =
* look & feel change

= 1.0.3 =
* tested for wordpress 3.1.3 and 3.2 beta

= 1.0.2 =
* minor Bugfixes
* fewer requests to Live Agent installation

= 1.0.1 =
* Bugfixes on plugin startup

== Upgrade Notice ==

= Upgrade from 1.2.X  to 2.0.X =

In this upgrade there were many code and user interface changes.
* Buttons table was removed. User now can input button code from application.
* First enabled button code will be automatically inserted after upgrade
* widgets were removed completely (they will be added later)
* alax calls were fixed
* plugin now uses WP jQuery instead of its own


== Arbitrary section ==

Now, for html form generation purposes php libraby htmlForm from http://stefangabos.blogspot.com/ is used.

If you have any thoughts how to make this plugin better, do not hasitate to leave your ideas in plugin forum, or write an email to support@qualityunit.com.
