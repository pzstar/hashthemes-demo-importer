=== HashThemes Demo Importer ===
Contributors: hashthemes
Tags: demo, importer, hashthemes
Requires at least: 5.0
Tested up to: 5.3.2
Stable tag: 1.0
Requires PHP: 5.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

HashThemes Demo importer imports the demo with just single click. It is as easy as that. It also install all the recommended or required plugins and also reset the website.

== Description ==

HashThemes Demo Importer imports the full demo with just one click. It is specially developed to add a demo importer functionality in the theme developed by HashThemes but it can also be used by any other themes as well. 

You just need to define the array that includes the location of the demo zip files and other informations. The other information includes name of the demo, preview image, theme option array, menu array, home page and blog page slug(if any), required plugins array and the tags that categorizes the theme.

The demo zip should contain the XML file, customizer (.dat) file, widget (.wie) file, theme option (.json), revolutions slider zip. It is not necessary to add all these files in the demo zip. You can skip the files if your demo does not need it.

<h4>Features</h4>
<ul>
<li>Reset website(Optional)</li>
<li>Install recommended and required plugins automatically</li>
<li>Imports Revolution slider</li>
<li>Imports fully functional demo</li>
</ul>

== Installation ==
The easy way to install the plugin is via WordPress.org plugin directory.

<ol>
<li>Go to WordPress Dashboard > Plugins > Add New</li>
<li>Search for "HashThemes Demo Importer" and install the plugin.</li>
<li>Activate Plugin from "Plugins" menu in WordPress.</li>
</ol>

== Frequently Asked Questions ==

= How to predefine demo imports?  =

An answer to that question.
<code>
<?php
function hdi_import_files_array(){
    return array(
        'demo-slug1' => array( //demo-slug should match the 'external_url' zip file name
            'name' => 'Demo Import One',
            'type' => 'pro', //Options - the value should be either 'free' or 'pro' - default is 'free'
            'buy_url' => 'http://www.your_domain.com/theme-name/', //Optional - only if the 'type' is set to 'pro'
            'external_url' => 'http://www.your_domain.com/import/demo-slug1.zip', // zip file should contain content.xml, customizer.dat, widget.wie, option_name1.json, option_name2.json, revslider.zip - you can skip any of the files if your demo does not need it
            'image' => 'http://www.your_domain.com/import/screenshot.png',
            'preview_url' => 'http://www.your_domain.com/demo-slug',
            'options_array' => array('option_name1','option_name2'), //option_name1.json, option_name2.json file should be included in the zip file
            'menu_array' => array(
                'primary' => 'Primary Menu',
                'secondary' => 'Secondary Menu'
            ),
            'home_slug' => 'home',
            'blog_slug' => 'blog',
            'tags' => array( //Optional
                'creative' => 'Creative',
                'portfolio' => 'Portfolio',
                'blog' => 'Blog'
            )
        ),
        'demo-slug2' => array(
            'name' => 'Demo Import Two',
            'external_url' => 'http://www.your_domain.com/import/demo-slug2.zip',
            'image' => 'http://www.your_domain.com/import/screenshot.png',
            'preview_url' => 'http://www.your_domain.com/demo-slug2',
            'menu_array' => array(
                'primary' => 'Primary Menu'
            ),
            'home_slug' => 'home',
            'blog_slug' => 'blog'
        )
    );
}

add_filter( 'hdi_import_files', 'hdi_import_files_array' );
?>
</code>

== Screenshots ==

 
== Changelog ==

= 1.0 =
* Release

== Upgrade Notice ==

= 1.0 =
Release