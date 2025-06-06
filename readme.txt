=== HashThemes Demo Importer ===
Contributors: hashthemes
Tags: demo importer, hashthemes, import, one click import
Requires at least: 6.3
Tested up to: 6.8
Stable tag: 1.3.9
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Transforming website setups from headache to 'click, click, done!

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

<h4>Video Guide</h4>
<iframe width="620" height="345" src="https://www.youtube.com/embed/FCViyER0vTo" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

== Installation ==
The easy way to install the plugin is via WordPress.org plugin directory.

<ol>
<li>Go to WordPress Dashboard > Plugins > Add New</li>
<li>Search for "HashThemes Demo Importer" and install the plugin.</li>
<li>Activate Plugin from "Plugins" menu in WordPress.</li>
</ol>

== Frequently Asked Questions ==
= How can I report security bugs? =

You can report security bugs through the Patchstack Vulnerability Disclosure Program. The Patchstack team help validate, triage and handle any security vulnerabilities. [Report a security vulnerability.](https://patchstack.com/database/vdp/9e5fb656-5530-4192-9ef5-b6b005a27e7d)

= How to predefine demo imports?  =

An answer to that question.
<code>
<?php
function hdi_import_files_array(){
    return array(
        'demo-slug1' => array( // demo-slug should match the 'external_url' zip file name
            'name' => 'Demo Import One',
            'type' => 'pro', // the value should be either 'free' or 'pro' - default is 'free'
            'buy_url' => 'http://www.your_domain.com/theme-name/', // optional - only if the 'type' is set to 'pro'
            'external_url' => 'http://www.your_domain.com/import/demo-slug1.zip', // zip file should contain content.xml, customizer.dat, widget.wie, option_name1.json, option_name2.json, revslider.zip(exported slider content from revolution slider) - you can skip any of the files if your demo does not need it
            'image' => 'http://www.your_domain.com/import/screenshot.png',
            'preview_url' => 'http://www.your_domain.com/demo-slug',
            'options_array' => array('option_name1','option_name2'), // option_name1.json, option_name2.json file should be included in the zip file
            'menu_array' => array( // list of menus
                'primary' => 'Primary Menu',
                'secondary' => 'Secondary Menu'
            ),
            'plugins' => array( // these plugins will be installed automatically before demo import
                'simple-floating-menu' => array(
                    'name' => 'Simple Floating Menu', // name of the plugin
                    'source' => 'wordpress', // source is either 'wordpress' for plugins in WordPress directory or 'remote' for external stored 
                    'file_path' => 'simple-floating-menu/simple-floating-menu.php' // path of the main file of the plugin
                ),
                'contact-form-7' => array(
                    'name' => 'Contact Form 7',
                    'source' => 'wordpress',
                    'file_path' => 'contact-form-7/wp-contact-form-7.php'
                )
                'revslider' => array(
                    'name' => 'Slider Revolution',
                    'source' => 'remote',
                    'file_path' => 'revslider/revslider.php',
                    'location' => 'http://www.your_domain.com/import/revslider.zip' // if source is 'remote', add the location of the plugin zip
                )
            ),
            'home_slug' => 'home',
            'blog_slug' => 'blog',
            'tags' => array( // Optional - add filter tab on the header to sort the demo by their type
                'magazine' => 'Magazine',
                'business' => 'Business',
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

 
== Changelog ==
= 1.3.9 - Jun 6, 2025
* Importer issue for Square Theme fixed

= 1.3.8 - May 14, 2025
* Error on offline plugin installer fixed

= 1.3.7 - May 13, 2025
* Demo import failing issue fixed

= 1.3.6 - May 12, 2025
* Readme.txt file updated
* Typo error fixed

= 1.3.5 - Apr 24, 2025
* Demo added for Total Theme

= 1.3.4 - Apr 22, 2025
* Demos added for Viral Express Theme

= 1.3.3 - Mar 24, 2025
* Code to create Hashform database added 

= 1.3.2 - Mar 03, 2025
* Minor updates

= 1.3.1 - Dec 10, 2024
* New Demos added

= 1.3.0 - Sep 18, 2024
* After Demo Import hook - Added

= 1.2.9 - Sep 04, 2024
* Hotel demo added on Total theme

= 1.2.8 - Jul 21, 2024
* Compatibility test with WordPress 6.6

= 1.2.7 - Jul 12, 2024
* Elementor gutenberg optmized enabled by default

= 1.2.6 - Jul 6, 2024
* Minor bug fixes

= 1.2.5 - Jul 3, 2024
* New demo added for Viral News & Total Theme Theme

= 1.2.4 - Jun 19, 2024
* New demo added for Viral News Theme

= 1.2.3 - Jun 13, 2024
* New demo added for Viral Theme

= 1.2.2 - Jun 04, 2024
* Outdated code removed

= 1.2.1 - Dec 22, 2023
* Hash Form Plugin demo added

= 1.2.0 - Sep 01, 2023
* Compatibility test with WordPress version 6.3

= 1.1.9 - May 08, 2022
* RTL Ready - Added

= 1.1.8 - Dec 05, 2022
* Suggested HashElements plugin missing for Viral Mag Theme - added

= 1.1.7 - Nov 24, 2022
* New demos added for Viral Mag

= 1.1.6 - July 25, 2022
* Added recommended plugins

= 1.1.5 - Jan 28, 2022
* Compatibility with the latest WordPress v5.9

= 1.1.4 - Oct 22, 2021
* Typo mistake for Total demo fixes
* Other minor code refinements

= 1.1.3 - Sep 29, 2021
* Total Demo Refined
* Law, Education demo added for Total Theme

= 1.1.1 - Jun 22, 2021
* Viral Mag Demo added

= 1.1.0 - May 15, 2021
* Square Elementor Demo added

= 1.0.9 - Mar 08, 2021
* Total Demo Import added

= 1.0.8 - Oct 12, 2020
* Viral News Menu id mistake in config file fixed

= 1.0.7
* Demos Rearranged

= 1.0.6
* WordPress Importer Updated
* New Demos added for Viral Theme

= 1.0.5
* Added new demo in Viral News theme

= 1.0.4
* Added More demo for Viral News Theme
* Demo Importer Enhancement

= 1.0.3
* Fixed responsive issue in the dashboard

= 1.0.2
* Demo for HashOne theme added

= 1.0
* Release