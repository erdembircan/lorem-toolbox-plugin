=== Lorem Toolbox ===
Contributors: erdembircan
Tags: development, lorem, post, generate
Requires at least: 5.1
Tested up to: 5.2
Requires PHP: 7.2
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Plugin for auto content generation for development and testing purposes.

== Description ==
Lorem toolbox is the ultimate content creation tool for your development and testing. With various methods(see *Usage* section of the readme) you can easily generate content and focus on the more important topics for your development.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/lorem-toolbox` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Use the Settings->lorem-toolbox settings screen to configure the plugin

== Usage ==
There are a couple of ways to generate content with lorem toolbox;

* You can use shortcode ([lorem]) to directly put lorem content in your post. Here are shortcut attributes you can use directly with shortcode:
  * p = number of paragraphs
  * pmin = minimum paragraph length
  * pmax = maximum paragraph length
  * smin = minimum sentence length
  * smax = maximum sentence length

* By checking *Add lorem to post* checkbox from post edit meta box. Also you can use the same meta box for removing the generated content from the post.

* Batch generation from settings page. With this you can generate thousands of posts with a single click and even better, you can remove all generated content with a single button.

== Screenshots ==

1. This screenshot shows auto generated content with default lorem-ipsum phrases

2. In this screeenshot content is generated with a paragraph used from Lord of the Rings. As you see, content is more easy to the eye and almost makes sense.

== Changelog ==

= 1.0 =
* Release