<?php

/*
 * Config file with each demo data
 */

$active_theme = array();

$square = array(
    'main' => array(
        'name' => 'Square',
        'external_url' => 'https://hashthemes.com/import-files/square/main.zip',
        'image' => 'https://i0.wp.com/themes.svn.wordpress.org/square/1.6.2/screenshot.png',
        'preview_url' => 'https://demo.hashthemes.com/square',
        'menu_array' => array(
            'primary' => 'Primary Menu'
        ),
        'home_slug' => 'home-page',
        'blog_slug' => 'blog'
    )
);

$squarepress = array(
    'main' => array(
        'name' => 'SquarePress',
        'external_url' => 'https://hashthemes.com/import-files/squarepress/main.zip',
        'image' => 'https://i0.wp.com/themes.svn.wordpress.org/squarepress/1.0.4/screenshot.png',
        'preview_url' => 'https://demo.hashthemes.com/squarepress',
        'menu_array' => array(
            'primary' => 'Primary Menu'
        ),
        'home_slug' => 'home-page',
        'blog_slug' => 'blog'
    )
);

$total = array(
    'main' => array(
        'name' => 'Total',
        'external_url' => 'https://hashthemes.com/import-files/total/main.zip',
        'image' => 'https://i0.wp.com/themes.svn.wordpress.org/total/1.2.32/screenshot.png',
        'preview_url' => 'https://demo.hashthemes.com/total',
        'menu_array' => array(
            'primary' => 'Primary Menu'
        ),
        'home_slug' => 'home',
        'blog_slug' => 'blog'
    )
);

$totally = array(
    'main' => array(
        'name' => 'Totally',
        'external_url' => 'https://hashthemes.com/import-files/totally/main.zip',
        'image' => 'https://i0.wp.com/themes.svn.wordpress.org/totally/1.0.9/screenshot.png',
        'preview_url' => 'https://demo.hashthemes.com/totally',
        'menu_array' => array(
            'primary' => 'Primary Menu'
        ),
        'home_slug' => 'home-page',
        'blog_slug' => 'blog'
    )
);

$viral = array(
    'main' => array(
        'name' => 'Viral',
        'external_url' => 'https://hashthemes.com/import-files/viral/main.zip',
        'image' => 'https://i0.wp.com/themes.svn.wordpress.org/viral/1.4.2/screenshot.png',
        'preview_url' => 'https://demo.hashthemes.com/viral',
        'menu_array' => array(
            'primary' => 'Primary Menu'
        ),
        'home_slug' => 'home-page',
        'blog_slug' => 'blog'
    )
);

$active_theme = get_option('stylesheet');

if (isset($$active_theme)) {
    $demo_array = $$active_theme;
} else {
    $demo_array = '';
}

return apply_filters('hdi_demo_files', $demo_array);
