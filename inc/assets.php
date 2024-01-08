<?php

function MbFilteradminStyle(){
    // Register Syle
    wp_register_style('mb-filter-admin', MB_FILTER_URL . '/assets/css/mb-filter-admin.css', [], filemtime( MB_FILTER_DIR_PATH . '/assets/css/mb-filter-admin.css'), 'all');

    // Enqueue Style
    wp_enqueue_style('mb-filter-admin');
}
add_action('admin_enqueue_scripts', 'MbFilteradminStyle' );

function MbFilterfrontendStyles(){
    // Register Syle
    wp_register_style('mb-filter', MB_FILTER_URL . '/assets/css/mb-filter.css', [], filemtime( MB_FILTER_DIR_PATH . '/assets/css/mb-filter.css'), 'all');

    // Enqueue Style
    wp_enqueue_style('mb-filter');
}
add_action( 'wp_enqueue_scripts' , 'MbFilterfrontendStyles');

function MbFilterfrontendScripts(){
    // Register Scripts
    wp_register_script( 'mb-filter', MB_FILTER_URL . '/assets/js/mb-filter.js', ['jquery'], filemtime( MB_FILTER_DIR_PATH . '/assets/js/mb-filter.js'), true );

    // Enqueue Script
    wp_enqueue_script('mb-filter');
}
add_action( 'wp_enqueue_scripts' , 'MbFilterfrontendScripts' );