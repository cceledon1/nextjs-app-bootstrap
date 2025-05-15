<?php
/*
Plugin Name: Gestión de Soldados
Description: Sistema de gestión de soldados con registro, edición y exportación a Excel/PDF
Version: 1.0
Author: Carlos (Administración Militar)
Text Domain: gestion-soldados
*/

// Prevent direct access
if (!defined('ABSPATH')) exit;

// Define plugin constants
define('GS_PATH', plugin_dir_path(__FILE__));
define('GS_URL', plugin_dir_url(__FILE__));

// Include required files
require_once GS_PATH . 'includes/funciones.php';
require_once GS_PATH . 'includes/clases/Soldado.php';
require_once GS_PATH . 'includes/clases/Exportador.php';
require_once GS_PATH . 'includes/clases/RolesManager.php';

// Activation Hook
register_activation_hook(__FILE__, 'gs_plugin_activate');
function gs_plugin_activate() {
    // Create custom roles
    RolesManager::setup_custom_roles();
    
    // Register custom post type
    gs_register_soldier_post_type();
    
    // Flush rewrite rules
    flush_rewrite_rules();
}

// Register Custom Post Type
function gs_register_soldier_post_type() {
    $labels = array(
        'name' => __('Soldados', 'gestion-soldados'),
        'singular_name' => __('Soldado', 'gestion-soldados'),
        'add_new' => __('Añadir Nuevo', 'gestion-soldados'),
        'add_new_item' => __('Añadir Nuevo Soldado', 'gestion-soldados'),
        'edit_item' => __('Editar Soldado', 'gestion-soldados'),
        'view_item' => __('Ver Soldado', 'gestion-soldados'),
        'search_items' => __('Buscar Soldados', 'gestion-soldados'),
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'show_in_menu' => true,
        'supports' => array('title', 'editor', 'thumbnail'),
        'menu_icon' => 'dashicons-groups',
        'has_archive' => true,
        'rewrite' => array('slug' => 'soldados'),
    );

    register_post_type('soldado', $args);
}

// Enqueue Scripts and Styles
add_action('wp_enqueue_scripts', 'gs_enqueue_assets');
function gs_enqueue_assets() {
    wp_enqueue_style('gs-styles', GS_URL . 'assets/css/styles.css');
    wp_enqueue_script('gs-scripts', GS_URL . 'assets/js/scripts.js', array('jquery'), '1.0', true);
}

// Load plugin text domain
add_action('plugins_loaded', 'gs_load_textdomain');
function gs_load_textdomain() {
    load_plugin_textdomain('gestion-soldados', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
