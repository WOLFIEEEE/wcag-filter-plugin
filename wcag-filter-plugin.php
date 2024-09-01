<?php
/*
Plugin Name: WCAG Filter Plugin
Description: A plugin to display and filter WCAG criteria, allowing users to download filtered data as Excel or PDF. Admins can edit the JSON data.
Version: 1.0
Author: Your Name
*/

// Enqueue scripts and styles
function wcag_filter_enqueue_scripts() {
    wp_enqueue_style('wcag-public-style', plugin_dir_url(__FILE__) . 'public/css/public-style.css');
    wp_enqueue_script('wcag-public-script', plugin_dir_url(__FILE__) . 'public/js/public-script.js', array('jquery'), null, true);

    // Enqueue external libraries
    wp_enqueue_script('sheetjs', 'https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.2/xlsx.full.min.js', array(), null, true);
    wp_enqueue_script('jspdf', 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js', array(), null, true);
    wp_enqueue_script('jspdf-autotable', 'https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.17/jspdf.plugin.autotable.min.js', array('jspdf'), null, true);

    wp_localize_script('wcag-public-script', 'wcagData', array(
        'jsonUrl' => plugin_dir_url(__FILE__) . 'data/wcag-data.json',
        'ajaxUrl' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'wcag_filter_enqueue_scripts');

// Include public-facing code
require_once plugin_dir_path(__FILE__) . 'public/wcag-display.php';

// Admin functionality
if (is_admin()) {
    wp_enqueue_style('wcag-admin-style', plugin_dir_url(__FILE__) . 'admin/css/admin-style.css');
    wp_enqueue_script('wcag-admin-script', plugin_dir_url(__FILE__) . 'admin/js/admin-script.js', array('jquery'), null, true);
    require_once plugin_dir_path(__FILE__) . 'admin/wcag-json-editor.php';
}
