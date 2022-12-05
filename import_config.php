<?php

/*
 * Config file with each demo data
 */

$square = array(
    'main' => array(
        'name' => 'Square - Customizer Demo',
        'external_url' => 'https://hashthemes.com/import-files/square/main.zip',
        'image' => 'https://hashthemes.com/import-files/square/screen/square.jpg',
        'preview_url' => 'https://demo.hashthemes.com/square',
        'options_array' => array('sfm_settings'),
        'menu_array' => array(
            'primary' => 'Primary Menu'
        ),
        'home_slug' => 'home-page',
        'blog_slug' => 'blog',
        'plugins' => array(
            'simple-floating-menu' => array(
                'name' => 'Simple Floating Menu',
                'source' => 'wordpress',
                'file_path' => 'simple-floating-menu/simple-floating-menu.php',
            ),
            'contact-form-7' => array(
                'name' => 'Contact Form 7',
                'source' => 'wordpress',
                'file_path' => 'contact-form-7/wp-contact-form-7.php'
            ),
        )
    ),
    'el-main' => array(
        'name' => 'Square - Elementor Demo',
        'external_url' => 'https://hashthemes.com/import-files/square/el-main.zip',
        'image' => 'https://hashthemes.com/import-files/square/screen/square.jpg',
        'preview_url' => 'https://demo.hashthemes.com/square',
        'options_array' => array('sfm_settings'),
        'menu_array' => array(
            'primary' => 'Primary Menu'
        ),
        'home_slug' => 'home-page',
        'blog_slug' => 'blog',
        'plugins' => array(
            'simple-floating-menu' => array(
                'name' => 'Simple Floating Menu',
                'source' => 'wordpress',
                'file_path' => 'simple-floating-menu/simple-floating-menu.php',
            ),
            'elementor' => array(
                'name' => 'Elementor',
                'source' => 'wordpress',
                'file_path' => 'elementor/elementor.php',
            ),
            'hash-elements' => array(
                'name' => 'Hash Elements',
                'source' => 'wordpress',
                'file_path' => 'hash-elements/hash-elements.php'
            ),
            'contact-form-7' => array(
                'name' => 'Contact Form 7',
                'source' => 'wordpress',
                'file_path' => 'contact-form-7/wp-contact-form-7.php'
            ),
        )
    )
);

$squarepress = array(
    'main' => array(
        'name' => 'SquarePress',
        'external_url' => 'https://hashthemes.com/import-files/squarepress/main.zip',
        'image' => 'https://hashthemes.com/import-files/squarepress/screen/main.jpg',
        'preview_url' => 'https://demo.hashthemes.com/squarepress',
        'options_array' => array('sfm_settings'),
        'menu_array' => array(
            'primary' => 'Primary Menu'
        ),
        'home_slug' => 'home-page',
        'blog_slug' => 'blog',
        'plugins' => array(
            'simple-floating-menu' => array(
                'name' => 'Simple Floating Menu',
                'source' => 'wordpress',
                'file_path' => 'simple-floating-menu/simple-floating-menu.php',
            ),
            'contact-form-7' => array(
                'name' => 'Contact Form 7',
                'source' => 'wordpress',
                'file_path' => 'contact-form-7/wp-contact-form-7.php'
            ),
        )
    )
);

$total = array(
    'agency' => array(
        'name' => 'Agency - Customizer Version',
        'external_url' => 'https://hashthemes.com/import-files/total/agency.zip',
        'image' => 'https://hashthemes.com/import-files/total/screen/agency.jpg',
        'preview_url' => 'https://demo.hashthemes.com/total/agency',
        'options_array' => array('sfm_settings'),
        'menu_array' => array(
            'primary' => 'Primary Menu'
        ),
        'home_slug' => 'home-page',
        'blog_slug' => 'blog',
        'plugins' => array(
            'simple-floating-menu' => array(
                'name' => 'Simple Floating Menu',
                'source' => 'wordpress',
                'file_path' => 'simple-floating-menu/simple-floating-menu.php',
            ),
            'wpforms-lite' => array(
                'name' => 'Contact Form by WPForms',
                'source' => 'wordpress',
                'file_path' => 'wpforms-lite/wpforms.php',
            )
        )
    ),
    'el-agency' => array(
        'name' => 'Agency - Elementor Version',
        'external_url' => 'https://hashthemes.com/import-files/total/el-agency.zip',
        'image' => 'https://hashthemes.com/import-files/total/screen/agency.jpg',
        'preview_url' => 'https://demo.hashthemes.com/total/el-agency',
        'options_array' => array('sfm_settings'),
        'menu_array' => array(
            'primary' => 'Primary Menu'
        ),
        'home_slug' => 'home-page',
        'blog_slug' => 'blog',
        'plugins' => array(
            'elementor' => array(
                'name' => 'Elementor',
                'source' => 'wordpress',
                'file_path' => 'elementor/elementor.php',
            ),
            'hash-elements' => array(
                'name' => 'Hash Elements',
                'source' => 'wordpress',
                'file_path' => 'hash-elements/hash-elements.php'
            ),
            'simple-floating-menu' => array(
                'name' => 'Simple Floating Menu',
                'source' => 'wordpress',
                'file_path' => 'simple-floating-menu/simple-floating-menu.php',
            ),
            'wpforms-lite' => array(
                'name' => 'Contact Form by WPForms',
                'source' => 'wordpress',
                'file_path' => 'wpforms-lite/wpforms.php',
            )
        )
    ),
    'education' => array(
        'name' => 'Education - Elementor Version',
        'external_url' => 'https://hashthemes.com/import-files/total/education.zip',
        'image' => 'https://hashthemes.com/import-files/total/screen/education.jpg',
        'preview_url' => 'https://demo.hashthemes.com/total/education',
        'options_array' => array('sfm_settings'),
        'menu_array' => array(
            'primary' => 'Main Menu'
        ),
        'home_slug' => 'home',
        'blog_slug' => 'news-events',
        'element_kit_slug' => 'total-education-kit',
        'plugins' => array(
            'elementor' => array(
                'name' => 'Elementor',
                'source' => 'wordpress',
                'file_path' => 'elementor/elementor.php',
            ),
            'hash-elements' => array(
                'name' => 'Hash Elements',
                'source' => 'wordpress',
                'file_path' => 'hash-elements/hash-elements.php'
            ),
            'simple-floating-menu' => array(
                'name' => 'Simple Floating Menu',
                'source' => 'wordpress',
                'file_path' => 'simple-floating-menu/simple-floating-menu.php',
            ),
            'learnpress' => array(
                'name' => 'LearnPress – WordPress LMS Plugin',
                'source' => 'wordpress',
                'file_path' => 'learnpress/learnpress.php',
            ),
            'wpforms-lite' => array(
                'name' => 'Contact Form by WPForms',
                'source' => 'wordpress',
                'file_path' => 'wpforms-lite/wpforms.php',
            )
        )
    ),
    'law' => array(
        'name' => 'Law - Elementor Version',
        'external_url' => 'https://hashthemes.com/import-files/total/law.zip',
        'image' => 'https://hashthemes.com/import-files/total/screen/law.jpg',
        'preview_url' => 'https://demo.hashthemes.com/total/law',
        'options_array' => array('sfm_settings'),
        'menu_array' => array(
            'primary' => 'Main Menu'
        ),
        'home_slug' => 'front-page',
        'blog_slug' => 'blogs',
        'element_kit_slug' => 'total-law-kit',
        'plugins' => array(
            'elementor' => array(
                'name' => 'Elementor',
                'source' => 'wordpress',
                'file_path' => 'elementor/elementor.php',
            ),
            'hash-elements' => array(
                'name' => 'Hash Elements',
                'source' => 'wordpress',
                'file_path' => 'hash-elements/hash-elements.php'
            ),
            'simple-floating-menu' => array(
                'name' => 'Simple Floating Menu',
                'source' => 'wordpress',
                'file_path' => 'simple-floating-menu/simple-floating-menu.php',
            ),
            'wpforms-lite' => array(
                'name' => 'Contact Form by WPForms',
                'source' => 'wordpress',
                'file_path' => 'wpforms-lite/wpforms.php',
            )
        )
    )
);

$totally = array(
    'main' => array(
        'name' => 'Totally',
        'external_url' => 'https://hashthemes.com/import-files/totally/main.zip',
        'image' => 'https://hashthemes.com/import-files/totally/screen/main.jpg',
        'preview_url' => 'https://demo.hashthemes.com/totally',
        'options_array' => array('sfm_settings'),
        'menu_array' => array(
            'primary' => 'Primary Menu'
        ),
        'home_slug' => 'home-page',
        'blog_slug' => 'blog',
        'plugins' => array(
            'simple-floating-menu' => array(
                'name' => 'Simple Floating Menu',
                'source' => 'wordpress',
                'file_path' => 'simple-floating-menu/simple-floating-menu.php',
            ),
        )
    )
);

$hashone = array(
    'main' => array(
        'name' => 'HashOne',
        'external_url' => 'https://hashthemes.com/import-files/hashone/main.zip',
        'image' => 'https://hashthemes.com/import-files/hashone/screen/main.jpg',
        'preview_url' => 'https://demo.hashthemes.com/hashone',
        'options_array' => array('sfm_settings'),
        'menu_array' => array(
            'primary' => 'Primary Menu'
        ),
        'home_slug' => 'home-page',
        'blog_slug' => 'blog'
    )
);

$viral = array(
    'demo1' => array(
        'name' => 'Demo One - Customizer Version',
        'external_url' => 'https://hashthemes.com/import-files/viral/demo1.zip',
        'image' => 'https://hashthemes.com/import-files/viral/screen/demo1.jpg',
        'preview_url' => 'https://demo.hashthemes.com/viral/demo1',
        'menu_array' => array(
            'primary' => 'Primary Menu',
            'top-menu' => 'Top Menu'
        ),
        'options_array' => array('sfm_settings'),
        'home_slug' => 'home-page',
        'blog_slug' => 'blog',
        'plugins' => array(
            'simple-floating-menu' => array(
                'name' => 'Simple Floating Menu',
                'source' => 'wordpress',
                'file_path' => 'simple-floating-menu/simple-floating-menu.php',
            ),
            'smart-blocks' => array(
                'name' => 'Smart Blocks - Wordpress Gutenberg Blocks',
                'source' => 'wordpress',
                'file_path' => 'smart-blocks/smart-blocks.php',
            )
        ),
        'tags' => array(
            'free' => 'Free'
        ),
        'pagebuilder' => array(
            'customizer' => 'Customizer'
        )
    ),
    'demo5' => array(
        'name' => 'Demo One - Elementor Version',
        'external_url' => 'https://hashthemes.com/import-files/viral/demo5.zip',
        'image' => 'https://hashthemes.com/import-files/viral/screen/demo1.jpg',
        'preview_url' => 'https://demo.hashthemes.com/viral/demo5',
        'menu_array' => array(
            'primary' => 'Primary Menu',
            'top-menu' => 'Top Menu'
        ),
        'options_array' => array('sfm_settings'),
        'home_slug' => 'home-page',
        'blog_slug' => 'blog',
        'plugins' => array(
            'simple-floating-menu' => array(
                'name' => 'Simple Floating Menu',
                'source' => 'wordpress',
                'file_path' => 'simple-floating-menu/simple-floating-menu.php',
            ),
            'elementor' => array(
                'name' => 'Elementor',
                'source' => 'wordpress',
                'file_path' => 'elementor/elementor.php',
            ),
            'hash-elements' => array(
                'name' => 'Hash Elements',
                'source' => 'wordpress',
                'file_path' => 'hash-elements/hash-elements.php'
            ),
            'smart-blocks' => array(
                'name' => 'Smart Blocks - Wordpress Gutenberg Blocks',
                'source' => 'wordpress',
                'file_path' => 'smart-blocks/smart-blocks.php',
            )
        ),
        'tags' => array(
            'free' => 'Free'
        ),
        'pagebuilder' => array(
            'elementor' => 'Elementor'
        )
    ),
    'demo2' => array(
        'name' => 'Demo Two - Customizer Version',
        'external_url' => 'https://hashthemes.com/import-files/viral/demo2.zip',
        'image' => 'https://hashthemes.com/import-files/viral/screen/demo2.jpg',
        'preview_url' => 'https://demo.hashthemes.com/viral/demo2',
        'menu_array' => array(
            'primary' => 'Primary Menu',
            'top-menu' => 'Top Menu'
        ),
        'options_array' => array('sfm_settings'),
        'home_slug' => 'home-page',
        'blog_slug' => 'blog',
        'plugins' => array(
            'simple-floating-menu' => array(
                'name' => 'Simple Floating Menu',
                'source' => 'wordpress',
                'file_path' => 'simple-floating-menu/simple-floating-menu.php',
            ),
            'smart-blocks' => array(
                'name' => 'Smart Blocks - Wordpress Gutenberg Blocks',
                'source' => 'wordpress',
                'file_path' => 'smart-blocks/smart-blocks.php',
            )
        ),
        'tags' => array(
            'free' => 'Free'
        ),
        'pagebuilder' => array(
            'customizer' => 'Customizer'
        )
    ),
    'demo6' => array(
        'name' => 'Demo Two - Elementor Version',
        'external_url' => 'https://hashthemes.com/import-files/viral/demo6.zip',
        'image' => 'https://hashthemes.com/import-files/viral/screen/demo2.jpg',
        'preview_url' => 'https://demo.hashthemes.com/viral/demo6',
        'menu_array' => array(
            'primary' => 'Primary Menu',
            'top-menu' => 'Top Menu'
        ),
        'options_array' => array('sfm_settings'),
        'home_slug' => 'home-page',
        'blog_slug' => 'blog',
        'plugins' => array(
            'simple-floating-menu' => array(
                'name' => 'Simple Floating Menu',
                'source' => 'wordpress',
                'file_path' => 'simple-floating-menu/simple-floating-menu.php',
            ),
            'elementor' => array(
                'name' => 'Elementor',
                'source' => 'wordpress',
                'file_path' => 'elementor/elementor.php',
            ),
            'hash-elements' => array(
                'name' => 'Hash Elements',
                'source' => 'wordpress',
                'file_path' => 'hash-elements/hash-elements.php'
            ),
            'smart-blocks' => array(
                'name' => 'Smart Blocks - Wordpress Gutenberg Blocks',
                'source' => 'wordpress',
                'file_path' => 'smart-blocks/smart-blocks.php',
            )
        ),
        'tags' => array(
            'free' => 'Free'
        ),
        'pagebuilder' => array(
            'elementor' => 'Elementor'
        )
    ),
    'demo3' => array(
        'name' => 'Demo Three - Customizer Version',
        'external_url' => 'https://hashthemes.com/import-files/viral/demo3.zip',
        'image' => 'https://hashthemes.com/import-files/viral/screen/demo3.jpg',
        'preview_url' => 'https://demo.hashthemes.com/viral/demo3',
        'menu_array' => array(
            'primary' => 'Primary Menu',
            'top-menu' => 'Top Menu'
        ),
        'options_array' => array('sfm_settings'),
        'home_slug' => 'home-page',
        'blog_slug' => 'blog',
        'plugins' => array(
            'simple-floating-menu' => array(
                'name' => 'Simple Floating Menu',
                'source' => 'wordpress',
                'file_path' => 'simple-floating-menu/simple-floating-menu.php',
            ),
            'smart-blocks' => array(
                'name' => 'Smart Blocks - Wordpress Gutenberg Blocks',
                'source' => 'wordpress',
                'file_path' => 'smart-blocks/smart-blocks.php',
            )
        ),
        'tags' => array(
            'free' => 'Free'
        ),
        'pagebuilder' => array(
            'customizer' => 'Customizer'
        )
    ),
    'demo7' => array(
        'name' => 'Demo Three - Elementor Version',
        'external_url' => 'https://hashthemes.com/import-files/viral/demo7.zip',
        'image' => 'https://hashthemes.com/import-files/viral/screen/demo3.jpg',
        'preview_url' => 'https://demo.hashthemes.com/viral/demo7',
        'menu_array' => array(
            'primary' => 'Primary Menu',
            'top-menu' => 'Top Menu'
        ),
        'options_array' => array('sfm_settings'),
        'home_slug' => 'home-page',
        'blog_slug' => 'blog',
        'plugins' => array(
            'simple-floating-menu' => array(
                'name' => 'Simple Floating Menu',
                'source' => 'wordpress',
                'file_path' => 'simple-floating-menu/simple-floating-menu.php',
            ),
            'elementor' => array(
                'name' => 'Elementor',
                'source' => 'wordpress',
                'file_path' => 'elementor/elementor.php',
            ),
            'hash-elements' => array(
                'name' => 'Hash Elements',
                'source' => 'wordpress',
                'file_path' => 'hash-elements/hash-elements.php'
            ),
            'smart-blocks' => array(
                'name' => 'Smart Blocks - Wordpress Gutenberg Blocks',
                'source' => 'wordpress',
                'file_path' => 'smart-blocks/smart-blocks.php',
            )
        ),
        'tags' => array(
            'free' => 'Free'
        ),
        'pagebuilder' => array(
            'elementor' => 'Elementor'
        )
    ),
    'demo4' => array(
        'name' => 'Demo Four (Boxed) - Customizer Version',
        'external_url' => 'https://hashthemes.com/import-files/viral/demo4.zip',
        'image' => 'https://hashthemes.com/import-files/viral/screen/demo4.jpg',
        'preview_url' => 'https://demo.hashthemes.com/viral/demo4',
        'menu_array' => array(
            'primary' => 'Primary Menu',
            'top-menu' => 'Top Menu'
        ),
        'options_array' => array('sfm_settings'),
        'home_slug' => 'home-page',
        'blog_slug' => 'blog',
        'plugins' => array(
            'simple-floating-menu' => array(
                'name' => 'Simple Floating Menu',
                'source' => 'wordpress',
                'file_path' => 'simple-floating-menu/simple-floating-menu.php',
            ),
            'smart-blocks' => array(
                'name' => 'Smart Blocks - Wordpress Gutenberg Blocks',
                'source' => 'wordpress',
                'file_path' => 'smart-blocks/smart-blocks.php',
            )
        ),
        'tags' => array(
            'free' => 'Free'
        ),
        'pagebuilder' => array(
            'customizer' => 'Customizer'
        )
    ),
    'demo8' => array(
        'name' => 'Demo Five (Framed) - Elementor Version',
        'external_url' => 'https://hashthemes.com/import-files/viral/demo8.zip',
        'image' => 'https://hashthemes.com/import-files/viral/screen/demo5.jpg',
        'preview_url' => 'https://demo.hashthemes.com/viral/demo8',
        'menu_array' => array(
            'primary' => 'Primary Menu',
            'top-menu' => 'Top Menu'
        ),
        'options_array' => array('sfm_settings'),
        'home_slug' => 'home-page',
        'blog_slug' => 'blog',
        'plugins' => array(
            'simple-floating-menu' => array(
                'name' => 'Simple Floating Menu',
                'source' => 'wordpress',
                'file_path' => 'simple-floating-menu/simple-floating-menu.php',
            ),
            'elementor' => array(
                'name' => 'Elementor',
                'source' => 'wordpress',
                'file_path' => 'elementor/elementor.php',
            ),
            'hash-elements' => array(
                'name' => 'Hash Elements',
                'source' => 'wordpress',
                'file_path' => 'hash-elements/hash-elements.php'
            ),
            'smart-blocks' => array(
                'name' => 'Smart Blocks - Wordpress Gutenberg Blocks',
                'source' => 'wordpress',
                'file_path' => 'smart-blocks/smart-blocks.php',
            )
        ),
        'tags' => array(
            'free' => 'Free'
        ),
        'pagebuilder' => array(
            'elementor' => 'Elementor'
        )
    ),
    'demo9' => array(
        'name' => 'Demo Six (Dark) - Elementor Version',
        'external_url' => 'https://hashthemes.com/import-files/viral/demo9.zip',
        'image' => 'https://hashthemes.com/import-files/viral/screen/demo6.jpg',
        'preview_url' => 'https://demo.hashthemes.com/viral/demo1',
        'menu_array' => array(
            'primary' => 'Primary Menu',
            'top-menu' => 'Top Menu'
        ),
        'options_array' => array('sfm_settings'),
        'home_slug' => 'home-page',
        'blog_slug' => 'blog',
        'plugins' => array(
            'simple-floating-menu' => array(
                'name' => 'Simple Floating Menu',
                'source' => 'wordpress',
                'file_path' => 'simple-floating-menu/simple-floating-menu.php',
            ),
            'elementor' => array(
                'name' => 'Elementor',
                'source' => 'wordpress',
                'file_path' => 'elementor/elementor.php',
            ),
            'hash-elements' => array(
                'name' => 'Hash Elements',
                'source' => 'wordpress',
                'file_path' => 'hash-elements/hash-elements.php'
            ),
            'smart-blocks' => array(
                'name' => 'Smart Blocks - Wordpress Gutenberg Blocks',
                'source' => 'wordpress',
                'file_path' => 'smart-blocks/smart-blocks.php',
            )
        ),
        'tags' => array(
            'free' => 'Free'
        ),
        'pagebuilder' => array(
            'elementor' => 'Elementor'
        )
    )
);

$viral_news = array(
    'demo1' => array(
        'name' => 'Demo One - Customizer Version',
        'external_url' => 'https://hashthemes.com/import-files/viral-news/demo1.zip',
        'image' => 'https://hashthemes.com/import-files/viral-news/screen/demo1-screenshot.jpg',
        'preview_url' => 'https://demo.hashthemes.com/viral-news/demo1',
        'options_array' => array('sfm_settings'),
        'menu_array' => array(
            'viral-news-primary-menu' => 'Primary Menu',
            'viral-news-top-menu' => 'Top Menu'
        ),
        'home_slug' => 'home',
        'blog_slug' => 'blog',
        'tags' => array(
            'free' => 'Free'
        ),
        'pagebuilder' => array(
            'customizer' => 'Customizer'
        ),
        'plugins' => array(
            'simple-floating-menu' => array(
                'name' => 'Simple Floating Menu',
                'source' => 'wordpress',
                'file_path' => 'simple-floating-menu/simple-floating-menu.php',
            ),
            'smart-blocks' => array(
                'name' => 'Smart Blocks - Wordpress Gutenberg Blocks',
                'source' => 'wordpress',
                'file_path' => 'smart-blocks/smart-blocks.php',
            )
        ),
    ),
    'elementor-demo1' => array(
        'name' => 'Demo One - Elementor Version',
        'external_url' => 'https://hashthemes.com/import-files/viral-news/elementor-demo1.zip',
        'image' => 'https://hashthemes.com/import-files/viral-news/screen/demo1-screenshot.jpg',
        'preview_url' => 'https://demo.hashthemes.com/viral-news/elementor-demo1',
        'options_array' => array('sfm_settings'),
        'menu_array' => array(
            'viral-news-primary-menu' => 'Primary Menu',
            'viral-news-top-menu' => 'Top Menu'
        ),
        'home_slug' => 'home',
        'blog_slug' => 'blog',
        'plugins' => array(
            'simple-floating-menu' => array(
                'name' => 'Simple Floating Menu',
                'source' => 'wordpress',
                'file_path' => 'simple-floating-menu/simple-floating-menu.php',
            ),
            'elementor' => array(
                'name' => 'Elementor',
                'source' => 'wordpress',
                'file_path' => 'elementor/elementor.php',
            ),
            'hash-elements' => array(
                'name' => 'Hash Elements',
                'source' => 'wordpress',
                'file_path' => 'hash-elements/hash-elements.php'
            ),
            'smart-blocks' => array(
                'name' => 'Smart Blocks - Wordpress Gutenberg Blocks',
                'source' => 'wordpress',
                'file_path' => 'smart-blocks/smart-blocks.php',
            )
        ),
        'tags' => array(
            'free' => 'Free'
        ),
        'pagebuilder' => array(
            'elementor' => 'Elementor'
        )
    ),
    'demo2' => array(
        'name' => 'Demo Two - Customizer Version',
        'external_url' => 'https://hashthemes.com/import-files/viral-news/demo2.zip',
        'image' => 'https://hashthemes.com/import-files/viral-news/screen/demo2-screenshot.jpg',
        'preview_url' => 'https://demo.hashthemes.com/viral-news/demo2',
        'options_array' => array('sfm_settings'),
        'menu_array' => array(
            'viral-news-primary-menu' => 'Primary Menu',
            'viral-news-top-menu' => 'Top Menu'
        ),
        'home_slug' => 'home',
        'blog_slug' => 'blog',
        'tags' => array(
            'free' => 'Free'
        ),
        'pagebuilder' => array(
            'customizer' => 'Customizer'
        ),
        'plugins' => array(
            'simple-floating-menu' => array(
                'name' => 'Simple Floating Menu',
                'source' => 'wordpress',
                'file_path' => 'simple-floating-menu/simple-floating-menu.php',
            ),
            'smart-blocks' => array(
                'name' => 'Smart Blocks - Wordpress Gutenberg Blocks',
                'source' => 'wordpress',
                'file_path' => 'smart-blocks/smart-blocks.php',
            )
        ),
    ),
    'elementor-demo2' => array(
        'name' => 'Demo Two - Elementor Version',
        'external_url' => 'https://hashthemes.com/import-files/viral-news/elementor-demo2.zip',
        'image' => 'https://hashthemes.com/import-files/viral-news/screen/demo2-screenshot.jpg',
        'preview_url' => 'https://demo.hashthemes.com/viral-news/elementor-demo2',
        'options_array' => array('sfm_settings'),
        'menu_array' => array(
            'viral-news-primary-menu' => 'Primary Menu',
            'viral-news-top-menu' => 'Top Menu'
        ),
        'home_slug' => 'home',
        'blog_slug' => 'blog',
        'plugins' => array(
            'simple-floating-menu' => array(
                'name' => 'Simple Floating Menu',
                'source' => 'wordpress',
                'file_path' => 'simple-floating-menu/simple-floating-menu.php',
            ),
            'elementor' => array(
                'name' => 'Elementor',
                'source' => 'wordpress',
                'file_path' => 'elementor/elementor.php',
            ),
            'hash-elements' => array(
                'name' => 'Hash Elements',
                'source' => 'wordpress',
                'file_path' => 'hash-elements/hash-elements.php'
            ),
            'smart-blocks' => array(
                'name' => 'Smart Blocks - Wordpress Gutenberg Blocks',
                'source' => 'wordpress',
                'file_path' => 'smart-blocks/smart-blocks.php',
            )
        ),
        'tags' => array(
            'free' => 'Free'
        ),
        'pagebuilder' => array(
            'elementor' => 'Elementor'
        )
    ),
    'demo3' => array(
        'name' => 'Demo Three - Customizer Version',
        'external_url' => 'https://hashthemes.com/import-files/viral-news/demo3.zip',
        'image' => 'https://hashthemes.com/import-files/viral-news/screen/demo3-screenshot.jpg',
        'preview_url' => 'https://demo.hashthemes.com/viral-news/demo3',
        'options_array' => array('sfm_settings'),
        'menu_array' => array(
            'viral-news-primary-menu' => 'Primary Menu',
            'viral-news-top-menu' => 'Top Menu'
        ),
        'home_slug' => 'home',
        'blog_slug' => 'blog',
        'tags' => array(
            'free' => 'Free'
        ),
        'pagebuilder' => array(
            'customizer' => 'Customizer'
        ),
        'plugins' => array(
            'simple-floating-menu' => array(
                'name' => 'Simple Floating Menu',
                'source' => 'wordpress',
                'file_path' => 'simple-floating-menu/simple-floating-menu.php',
            ),
            'smart-blocks' => array(
                'name' => 'Smart Blocks - Wordpress Gutenberg Blocks',
                'source' => 'wordpress',
                'file_path' => 'smart-blocks/smart-blocks.php',
            )
        ),
    ),
    'elementor-demo3' => array(
        'name' => 'Demo Three - Elementor Version',
        'external_url' => 'https://hashthemes.com/import-files/viral-news/elementor-demo3.zip',
        'image' => 'https://hashthemes.com/import-files/viral-news/screen/demo3-screenshot.jpg',
        'preview_url' => 'https://demo.hashthemes.com/viral-news/elementor-demo3',
        'options_array' => array('sfm_settings'),
        'menu_array' => array(
            'viral-news-primary-menu' => 'Primary Menu',
            'viral-news-top-menu' => 'Top Menu'
        ),
        'home_slug' => 'home',
        'blog_slug' => 'blog',
        'plugins' => array(
            'simple-floating-menu' => array(
                'name' => 'Simple Floating Menu',
                'source' => 'wordpress',
                'file_path' => 'simple-floating-menu/simple-floating-menu.php',
            ),
            'elementor' => array(
                'name' => 'Elementor',
                'source' => 'wordpress',
                'file_path' => 'elementor/elementor.php',
            ),
            'hash-elements' => array(
                'name' => 'Hash Elements',
                'source' => 'wordpress',
                'file_path' => 'hash-elements/hash-elements.php'
            ),
            'smart-blocks' => array(
                'name' => 'Smart Blocks - Wordpress Gutenberg Blocks',
                'source' => 'wordpress',
                'file_path' => 'smart-blocks/smart-blocks.php',
            )
        ),
        'tags' => array(
            'free' => 'Free'
        ),
        'pagebuilder' => array(
            'elementor' => 'Elementor'
        )
    ),
    'demo4' => array(
        'name' => 'Demo Four - Customizer Version',
        'external_url' => 'https://hashthemes.com/import-files/viral-news/demo4.zip',
        'image' => 'https://hashthemes.com/import-files/viral-news/screen/demo4-screenshot.jpg',
        'preview_url' => 'https://demo.hashthemes.com/viral-news/demo4',
        'options_array' => array('sfm_settings'),
        'menu_array' => array(
            'viral-news-primary-menu' => 'Primary Menu',
            'viral-news-top-menu' => 'Top Menu'
        ),
        'home_slug' => 'home',
        'blog_slug' => 'blog',
        'tags' => array(
            'free' => 'Free'
        ),
        'pagebuilder' => array(
            'customizer' => 'Customizer'
        ),
        'plugins' => array(
            'simple-floating-menu' => array(
                'name' => 'Simple Floating Menu',
                'source' => 'wordpress',
                'file_path' => 'simple-floating-menu/simple-floating-menu.php',
            ),
            'smart-blocks' => array(
                'name' => 'Smart Blocks - Wordpress Gutenberg Blocks',
                'source' => 'wordpress',
                'file_path' => 'smart-blocks/smart-blocks.php',
            )
        ),
    ),
    'elementor-demo4' => array(
        'name' => 'Demo Four - Elementor Version',
        'external_url' => 'https://hashthemes.com/import-files/viral-news/elementor-demo4.zip',
        'image' => 'https://hashthemes.com/import-files/viral-news/screen/demo4-screenshot.jpg',
        'preview_url' => 'https://demo.hashthemes.com/viral-news/elementor-demo4',
        'options_array' => array('sfm_settings'),
        'menu_array' => array(
            'viral-news-primary-menu' => 'Primary Menu',
            'viral-news-top-menu' => 'Top Menu'
        ),
        'home_slug' => 'home',
        'blog_slug' => 'blog',
        'plugins' => array(
            'simple-floating-menu' => array(
                'name' => 'Simple Floating Menu',
                'source' => 'wordpress',
                'file_path' => 'simple-floating-menu/simple-floating-menu.php',
            ),
            'elementor' => array(
                'name' => 'Elementor',
                'source' => 'wordpress',
                'file_path' => 'elementor/elementor.php',
            ),
            'hash-elements' => array(
                'name' => 'Hash Elements',
                'source' => 'wordpress',
                'file_path' => 'hash-elements/hash-elements.php'
            ),
            'smart-blocks' => array(
                'name' => 'Smart Blocks - Wordpress Gutenberg Blocks',
                'source' => 'wordpress',
                'file_path' => 'smart-blocks/smart-blocks.php',
            )
        ),
        'tags' => array(
            'free' => 'Free'
        ),
        'pagebuilder' => array(
            'elementor' => 'Elementor'
        )
    ),
    'demo5' => array(
        'name' => 'Demo Five - Customizer Version',
        'external_url' => 'https://hashthemes.com/import-files/viral-news/demo5.zip',
        'image' => 'https://hashthemes.com/import-files/viral-news/screen/demo5-screenshot.jpg',
        'preview_url' => 'https://demo.hashthemes.com/viral-news/demo5',
        'options_array' => array('sfm_settings'),
        'menu_array' => array(
            'viral-news-primary-menu' => 'Primary Menu',
            'viral-news-top-menu' => 'Top Menu'
        ),
        'home_slug' => 'home',
        'blog_slug' => 'blog',
        'tags' => array(
            'free' => 'Free'
        ),
        'pagebuilder' => array(
            'customizer' => 'Customizer'
        ),
        'plugins' => array(
            'simple-floating-menu' => array(
                'name' => 'Simple Floating Menu',
                'source' => 'wordpress',
                'file_path' => 'simple-floating-menu/simple-floating-menu.php',
            ),
            'smart-blocks' => array(
                'name' => 'Smart Blocks - Wordpress Gutenberg Blocks',
                'source' => 'wordpress',
                'file_path' => 'smart-blocks/smart-blocks.php',
            )
        ),
    ),
    'elementor-demo5' => array(
        'name' => 'Demo Five - Elementor Version',
        'external_url' => 'https://hashthemes.com/import-files/viral-news/elementor-demo5.zip',
        'image' => 'https://hashthemes.com/import-files/viral-news/screen/demo5-screenshot.jpg',
        'preview_url' => 'https://demo.hashthemes.com/viral-news/elementor-demo5',
        'options_array' => array('sfm_settings'),
        'menu_array' => array(
            'viral-news-primary-menu' => 'Primary Menu',
            'viral-news-top-menu' => 'Top Menu'
        ),
        'home_slug' => 'home',
        'blog_slug' => 'blog',
        'plugins' => array(
            'simple-floating-menu' => array(
                'name' => 'Simple Floating Menu',
                'source' => 'wordpress',
                'file_path' => 'simple-floating-menu/simple-floating-menu.php',
            ),
            'elementor' => array(
                'name' => 'Elementor',
                'source' => 'wordpress',
                'file_path' => 'elementor/elementor.php',
            ),
            'hash-elements' => array(
                'name' => 'Hash Elements',
                'source' => 'wordpress',
                'file_path' => 'hash-elements/hash-elements.php'
            ),
            'smart-blocks' => array(
                'name' => 'Smart Blocks - Wordpress Gutenberg Blocks',
                'source' => 'wordpress',
                'file_path' => 'smart-blocks/smart-blocks.php',
            )
        ),
        'tags' => array(
            'free' => 'Free'
        ),
        'pagebuilder' => array(
            'elementor' => 'Elementor'
        )
    ),
    'demo6' => array(
        'name' => 'Demo Six - Customizer Version',
        'external_url' => 'https://hashthemes.com/import-files/viral-news/demo6.zip',
        'image' => 'https://hashthemes.com/import-files/viral-news/screen/demo6-screenshot.jpg',
        'preview_url' => 'https://demo.hashthemes.com/viral-news/demo6',
        'options_array' => array('sfm_settings'),
        'menu_array' => array(
            'viral-news-primary-menu' => 'Primary Menu',
            'viral-news-top-menu' => 'Top Menu'
        ),
        'tags' => array(
            'free' => 'Free'
        ),
        'plugins' => array(
            'simple-floating-menu' => array(
                'name' => 'Simple Floating Menu',
                'source' => 'wordpress',
                'file_path' => 'simple-floating-menu/simple-floating-menu.php',
            ),
            'smart-blocks' => array(
                'name' => 'Smart Blocks - Wordpress Gutenberg Blocks',
                'source' => 'wordpress',
                'file_path' => 'smart-blocks/smart-blocks.php',
            )
        ),
        'pagebuilder' => array(
            'customizer' => 'Customizer'
        )
    ),
    'elementor-demo6' => array(
        'name' => 'Demo Seven - Elementor Version',
        'external_url' => 'https://hashthemes.com/import-files/viral-news/elementor-demo6.zip',
        'image' => 'https://hashthemes.com/import-files/viral-news/screen/elementor-demo6-screenshot.jpg',
        'preview_url' => 'https://demo.hashthemes.com/viral-news/elementor-demo6',
        'options_array' => array('sfm_settings'),
        'menu_array' => array(
            'viral-news-primary-menu' => 'Primary Menu',
            'viral-news-top-menu' => 'Top Menu'
        ),
        'home_slug' => 'home',
        'blog_slug' => 'blog',
        'plugins' => array(
            'simple-floating-menu' => array(
                'name' => 'Simple Floating Menu',
                'source' => 'wordpress',
                'file_path' => 'simple-floating-menu/simple-floating-menu.php',
            ),
            'elementor' => array(
                'name' => 'Elementor',
                'source' => 'wordpress',
                'file_path' => 'elementor/elementor.php',
            ),
            'hash-elements' => array(
                'name' => 'Hash Elements',
                'source' => 'wordpress',
                'file_path' => 'hash-elements/hash-elements.php'
            ),
            'smart-blocks' => array(
                'name' => 'Smart Blocks - Wordpress Gutenberg Blocks',
                'source' => 'wordpress',
                'file_path' => 'smart-blocks/smart-blocks.php',
            )
        ),
        'tags' => array(
            'free' => 'Free'
        ),
        'pagebuilder' => array(
            'elementor' => 'Elementor'
        )
    ),
);

$viral_mag = array(
    'news-mag' => array(
        'name' => 'News Mag',
        'external_url' => 'https://hashthemes.com/import-files/viral-mag/news-mag.zip',
        'image' => 'https://hashthemes.com/import-files/viral-mag/screen/news.jpg',
        'preview_url' => 'https://demo.hashthemes.com/viral-mag/news',
        'options_array' => array('sfm_settings'),
        'menu_array' => array(
            'viral-mag-primary-menu' => 'Main Menu',
        ),
        'home_slug' => 'home',
        'blog_slug' => 'blog',
        'plugins' => array(
            'hash-elements' => array(
                'name' => 'Hash Elements',
                'source' => 'wordpress',
                'file_path' => 'hash-elements/hash-elements.php'
            ),
            'simple-floating-menu' => array(
                'name' => 'Simple Floating Menu',
                'source' => 'wordpress',
                'file_path' => 'simple-floating-menu/simple-floating-menu.php',
            ),
            'elementor' => array(
                'name' => 'Elementor',
                'source' => 'wordpress',
                'file_path' => 'elementor/elementor.php',
            ),
            'smart-blocks' => array(
                'name' => 'Smart Blocks - Wordpress Gutenberg Blocks',
                'source' => 'wordpress',
                'file_path' => 'smart-blocks/smart-blocks.php',
            )
        ),
        'tags' => array(
            'free' => 'Free'
        ),
        'pagebuilder' => array(
            'elementor' => 'Elementor'
        )
    ),
    'gadgets' => array(
        'name' => 'Gadgets',
        'external_url' => 'https://hashthemes.com/import-files/viral-mag/gadgets.zip',
        'image' => 'https://hashthemes.com/import-files/viral-mag/screen/gadgets.jpg',
        'preview_url' => 'https://demo.hashthemes.com/viral-mag/gadgets',
        'options_array' => array('sfm_settings'),
        'menu_array' => array(
            'viral-mag-primary-menu' => 'Primary Menu',
            'viral-news-top-menu' => 'Header Menu'
        ),
        'home_slug' => 'home',
        'blog_slug' => 'blog',
        'plugins' => array(
            'hash-elements' => array(
                'name' => 'Hash Elements',
                'source' => 'wordpress',
                'file_path' => 'hash-elements/hash-elements.php'
            ),
            'simple-floating-menu' => array(
                'name' => 'Simple Floating Menu',
                'source' => 'wordpress',
                'file_path' => 'simple-floating-menu/simple-floating-menu.php',
            ),
            'elementor' => array(
                'name' => 'Elementor',
                'source' => 'wordpress',
                'file_path' => 'elementor/elementor.php',
            ),
            'smart-blocks' => array(
                'name' => 'Smart Blocks - Wordpress Gutenberg Blocks',
                'source' => 'wordpress',
                'file_path' => 'smart-blocks/smart-blocks.php',
            )
        ),
        'tags' => array(
            'free' => 'Free'
        ),
        'pagebuilder' => array(
            'elementor' => 'Elementor'
        )
    ),
    'v-magazine' => array(
        'name' => 'Magazine',
        'external_url' => 'https://hashthemes.com/import-files/viral-mag/v-magazine.zip',
        'image' => 'https://hashthemes.com/import-files/viral-mag/screen/magazine.jpg',
        'preview_url' => 'https://demo.hashthemes.com/viral-mag/magazine',
        'options_array' => array('sfm_settings'),
        'menu_array' => array(
            'viral-mag-primary-menu' => 'Main Menu'
        ),
        'home_slug' => 'home',
        'blog_slug' => 'blog',
        'plugins' => array(
            'hash-elements' => array(
                'name' => 'Hash Elements',
                'source' => 'wordpress',
                'file_path' => 'hash-elements/hash-elements.php'
            ),
            'simple-floating-menu' => array(
                'name' => 'Simple Floating Menu',
                'source' => 'wordpress',
                'file_path' => 'simple-floating-menu/simple-floating-menu.php',
            ),
            'elementor' => array(
                'name' => 'Elementor',
                'source' => 'wordpress',
                'file_path' => 'elementor/elementor.php',
            ),
            'smart-blocks' => array(
                'name' => 'Smart Blocks - Wordpress Gutenberg Blocks',
                'source' => 'wordpress',
                'file_path' => 'smart-blocks/smart-blocks.php',
            )
        ),
        'tags' => array(
            'free' => 'Free'
        ),
        'pagebuilder' => array(
            'elementor' => 'Elementor'
        )
    ),
    'tech' => array(
        'name' => 'Technology',
        'external_url' => 'https://hashthemes.com/import-files/viral-mag/tech.zip',
        'image' => 'https://hashthemes.com/import-files/viral-mag/screen/tech.jpg',
        'preview_url' => 'https://demo.hashthemes.com/viral-mag/tech',
        'options_array' => array('sfm_settings'),
        'menu_array' => array(
            'viral-mag-primary-menu' => 'Primary Menu'
        ),
        'home_slug' => 'home',
        'blog_slug' => 'blog',
        'plugins' => array(
            'hash-elements' => array(
                'name' => 'Hash Elements',
                'source' => 'wordpress',
                'file_path' => 'hash-elements/hash-elements.php'
            ),
            'simple-floating-menu' => array(
                'name' => 'Simple Floating Menu',
                'source' => 'wordpress',
                'file_path' => 'simple-floating-menu/simple-floating-menu.php',
            ),
            'elementor' => array(
                'name' => 'Elementor',
                'source' => 'wordpress',
                'file_path' => 'elementor/elementor.php',
            ),
            'smart-blocks' => array(
                'name' => 'Smart Blocks - Wordpress Gutenberg Blocks',
                'source' => 'wordpress',
                'file_path' => 'smart-blocks/smart-blocks.php',
            )
        ),
        'tags' => array(
            'free' => 'Free'
        ),
        'pagebuilder' => array(
            'elementor' => 'Elementor'
        )
    ),
    'health' => array(
        'name' => 'Health',
        'external_url' => 'https://hashthemes.com/import-files/viral-mag/health.zip',
        'image' => 'https://hashthemes.com/import-files/viral-mag/screen/health.jpg',
        'preview_url' => 'https://demo.hashthemes.com/viral-mag/health',
        'options_array' => array('sfm_settings'),
        'menu_array' => array(
            'viral-mag-primary-menu' => 'Primary Menu'
        ),
        'home_slug' => 'home',
        'blog_slug' => 'blog',
        'plugins' => array(
            'hash-elements' => array(
                'name' => 'Hash Elements',
                'source' => 'wordpress',
                'file_path' => 'hash-elements/hash-elements.php'
            ),
            'simple-floating-menu' => array(
                'name' => 'Simple Floating Menu',
                'source' => 'wordpress',
                'file_path' => 'simple-floating-menu/simple-floating-menu.php',
            ),
            'elementor' => array(
                'name' => 'Elementor',
                'source' => 'wordpress',
                'file_path' => 'elementor/elementor.php',
            ),
            'smart-blocks' => array(
                'name' => 'Smart Blocks - Wordpress Gutenberg Blocks',
                'source' => 'wordpress',
                'file_path' => 'smart-blocks/smart-blocks.php',
            )
        ),
        'tags' => array(
            'free' => 'Free'
        ),
        'pagebuilder' => array(
            'elementor' => 'Elementor'
        )
    ),
    'v-food' => array(
        'name' => 'Food',
        'external_url' => 'https://hashthemes.com/import-files/viral-mag/v-food.zip',
        'image' => 'https://hashthemes.com/import-files/viral-mag/screen/food.jpg',
        'preview_url' => 'https://demo.hashthemes.com/viral-mag/food',
        'options_array' => array('sfm_settings'),
        'menu_array' => array(
            'viral-mag-primary-menu' => 'Primary Menu'
        ),
        'home_slug' => 'home',
        'blog_slug' => 'blog',
        'plugins' => array(
            'hash-elements' => array(
                'name' => 'Hash Elements',
                'source' => 'wordpress',
                'file_path' => 'hash-elements/hash-elements.php'
            ),
            'simple-floating-menu' => array(
                'name' => 'Simple Floating Menu',
                'source' => 'wordpress',
                'file_path' => 'simple-floating-menu/simple-floating-menu.php',
            ),
            'elementor' => array(
                'name' => 'Elementor',
                'source' => 'wordpress',
                'file_path' => 'elementor/elementor.php',
            ),
            'smart-blocks' => array(
                'name' => 'Smart Blocks - Wordpress Gutenberg Blocks',
                'source' => 'wordpress',
                'file_path' => 'smart-blocks/smart-blocks.php',
            )
        ),
        'tags' => array(
            'free' => 'Free'
        ),
        'pagebuilder' => array(
            'elementor' => 'Elementor'
        )
    )
);

$active_theme = str_replace('-', '_', get_option('stylesheet'));

if (isset($$active_theme)) {
    $demo_array = $$active_theme;
} else {
    $demo_array = array();
}

return apply_filters('hdi_import_files', $demo_array);
