=== Plugin Name ===
Contributors: mrefghi
Donate link: http://www.mattrefghi.com/
Tags: credits, reviewer, editor, writer, co-writer, thanks
Requires at least: 2.9
Tested up to: 3.0.4
Stable tag: 0.4.2

Credit those who helped by including a stylish list of names after any post, potentially including a Gravatar and link for each.

== Description ==

Ever wanted to give recognition to the people that helped you produce a given blog post? All Due Credit allows you to include a 
list of names along with any post you write, to serve this very purpose. Each name listed is accompanied by text label that reveals 
what their role was.

You can add the following labels:
 
*	Written by
 
*	Co-written by
 
*	Edited by
 
*	Reviewed by
 
*	Thanks to

Each name can be accompanied by a Gravatar, as well as a homepage link. 

All Due Credit currently requires some manual steps to use. For example, whenever you want to add a name to a post, you have to 
add a Custom Field manually, respecting the right spelling of text label. (See the 'Usage' tab for more information)
While this is completely functional, I will eventually make it friendlier, where you'll be able to select from a dropdown, or 
something simple like that. Despite this, I'm happy with using All Due Credit on [my own blog](http://www.mattrefghi.com/wordpress/), 
and figured some of you may appreciate the same opportunity.

The design of All Due Credit was inspired by [Funnyordie.com](http://www.funnyordie.com/).

== Installation ==

1. Upload the `all-due-credit` directory to your `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

**How to Use**
 
1. Edit a post
1. Scroll down to the 'Custom Fields' section, and locate the 'Add New Custom Field' form.
1. If the 'Name' field is a dropdown that says '-- Select --', click the 'Enter New' link right below it.
1. In the 'Name' field (now a textbox), enter one of the following labels: 
 
*	Written by
 
*	Co-written by
 
*	Edited by
 
*	Reviewed by
 
*	Thanks to
 
1. In the 'Value' field, you have some flexibility.

You can simply add just name:

>	`John Doe`

Or you can provide additional information, such as:
(Commas (,) are used to separate the different pieces of information)

A name and an e-mail: (E-mail enables Gravatar)

>	`John Doe,jdoe@hisemail.com`

A name, an e-mail, and a website:

>	`John Doe,jdoe@hisemail.com,jdoeswebsite.com`

You can even leave out the e-mail, and still specify a website:

>	`John Doe,,jdoeswebsite`

Additionally, if the label you selected was 'Thanks to', you can list multiple persons in the same 'Value' box. 
To do this, simply add a semi-colon (;) in between each entry, like this:

>	`John Doe,jdoe@hisemail.com,jdoeswebsite.com;Jane Doe,jane@heremail.com,janeswebsite.com`

== Changelog ==

= 0.4.2 =
*	Added two new options: Show on home page and Show in feed. They allow you to control where All Due Credit will be included.

= 0.4.0 =
*	Added an options page, which can be found in the Settings menu group. Currently, three options are available: Maximum names per row, Gravatar size, and Gravatar default image. The first two can be useful if you're having trouble getting All Due Credit looking right in your theme.

= 0.3.6 =
*	Greatly improved compatibility with themes that use custom image styles. Future updates will improve this logic to handle themes that have heavy CSS changes.
*	Fixed minor performance flaw that was calling JavaScript methods needlessly.

= 0.3.5 =
*	Gravatars are now hotlinked (same effect as clicking on the name)
*	Greatly improved compatibility with themes, ensuring that names will always be visible and unclipped. Next update will improve how avatars look in templates that modify image styles.
*	Fixed bug where very long names would break the overall layout

= 0.3 =
Initial Release

== Upgrade Notice ==

= 0.4.2 =
Added two new options.

= 0.4.0 =
Added three options, two of which are useful for theme compatibility.

= 0.3.6 =
Significant theme compatibility improvements, and one bug fix.

= 0.3.5 =
Major improvements made to ensure compatibility with most templates. Gravatars are now hotlinked, and a significant bug has been fixed.

= 0.3 =
Initial Release

== Frequently Asked Questions ==

= Will this plugin change my post contents? =

No. The All Due Credit control is generated on-the-fly, and it is added right after the post content. Furthermore, All 
Due Credit will never appear on the home page of your blog, only when you view a specific post.

= Why does All Due Credit appear broken in my blog? =

There are a huge variety of Wordpress themes available out there, some of which make major modifications to the styles of Wordpress. Since All Due Credit inherits these styles, sometimes this results in layout issues. I've been progressing on handling differences introduced by themes, so keep an eye out for new versions. If you have problems with a specific theme, I'd be happy to take a look and see if I can help.

= Can I have multiple names per label, in the same post? =

Only with the 'Thanks to' field, at the moment. Multiple names in the 'Thanks to' field, delimited by semi-colon (;),
 will automatically be converted to multiple 'Thanks to' entries in All Due Credit. The other four labels don't currently 
 support this.

= If I enter an e-mail, will it ever be seen by my readers? =

No. The e-mail is encrypted right before we use it, and is only used to request the person's Gravatar.

== Screenshots ==

1. An example of what All Due Credit looks like when used. It will automatically inherit styles from its host blog.

`<?php code(); // goes in backticks ?>`