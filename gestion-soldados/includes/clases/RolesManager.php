<?php
// Prevent direct access
if (!defined('ABSPATH')) exit;

class RolesManager {
    // Role constants
    const ROLE_ROOT = 'gs_root';
    const ROLE_EDITOR = 'gs_editor';
    const ROLE_VISUALIZADOR = 'gs_visualizador';

    /**
     * Set up custom roles and capabilities
     */
    public static function setup_custom_roles() {
        // Root Role - Full access
        add_role(
            self::ROLE_ROOT,
            __('GS Root', 'gestion-soldados'),
            array(
                'read' => true,
                'gs_manage_soldiers' => true,
                'gs_edit_soldiers' => true,
                'gs_delete_soldiers' => true,
                'gs_export_soldiers' => true,
                'gs_manage_users' => true,
                'gs_view_soldiers' => true,
                'upload_files' => true
            )
        );

        // Editor Role - Can create, edit, and export
        add_role(
            self::ROLE_EDITOR,
            __('GS Editor', 'gestion-soldados'),
            array(
                'read' => true,
                'gs_edit_soldiers' => true,
                'gs_export_soldiers' => true,
                'gs_view_soldiers' => true,
                'upload_files' => true
            )
        );

        // Visualizador Role - Can only view
        add_role(
            self::ROLE_VISUALIZADOR,
            __('GS Visualizador', 'gestion-soldados'),
            array(
                'read' => true,
                'gs_view_soldiers' => true
            )
        );

        // Add capabilities to administrator role
        $admin_role = get_role('administrator');
        if ($admin_role) {
            $admin_role->add_cap('gs_manage_soldiers');
            $admin_role->add_cap('gs_edit_soldiers');
            $admin_role->add_cap('gs_delete_soldiers');
            $admin_role->add_cap('gs_export_soldiers');
            $admin_role->add_cap('gs_manage_users');
            $admin_role->add_cap('gs_view_soldiers');
        }
    }

    /**
     * Remove custom roles and capabilities
     */
    public static function remove_custom_roles() {
        remove_role(self::ROLE_ROOT);
        remove_role(self::ROLE_EDITOR);
        remove_role(self::ROLE_VISUALIZADOR);

        // Remove capabilities from administrator role
        $admin_role = get_role('administrator');
        if ($admin_role) {
            $admin_role->remove_cap('gs_manage_soldiers');
            $admin_role->remove_cap('gs_edit_soldiers');
            $admin_role->remove_cap('gs_delete_soldiers');
            $admin_role->remove_cap('gs_export_soldiers');
            $admin_role->remove_cap('gs_manage_users');
            $admin_role->remove_cap('gs_view_soldiers');
        }
    }

    /**
     * Check if current user can manage soldiers
     */
    public static function can_manage_soldiers() {
        return current_user_can('gs_manage_soldiers');
    }

    /**
     * Check if current user can edit soldiers
     */
    public static function can_edit_soldiers() {
        return current_user_can('gs_edit_soldiers');
    }

    /**
     * Check if current user can delete soldiers
     */
    public static function can_delete_soldiers() {
        return current_user_can('gs_delete_soldiers');
    }

    /**
     * Check if current user can export soldiers
     */
    public static function can_export_soldiers() {
        return current_user_can('gs_export_soldiers');
    }

    /**
     * Check if current user can view soldiers
     */
    public static function can_view_soldiers() {
        return current_user_can('gs_view_soldiers');
    }

    /**
     * Check if current user can manage users
     */
    public static function can_manage_users() {
        return current_user_can('gs_manage_users');
    }

    /**
     * Get all plugin roles
     */
    public static function get_plugin_roles() {
        return array(
            self::ROLE_ROOT => __('GS Root', 'gestion-soldados'),
            self::ROLE_EDITOR => __('GS Editor', 'gestion-soldados'),
            self::ROLE_VISUALIZADOR => __('GS Visualizador', 'gestion-soldados')
        );
    }

    /**
     * Add menu access restrictions
     */
    public static function restrict_admin_menu() {
        if (!self::can_manage_soldiers()) {
            remove_menu_page('edit.php?post_type=soldado');
        }
    }

    /**
     * Add custom capabilities to the plugin
     */
    public static function register_capabilities() {
        // Register custom capabilities
        $capabilities = array(
            'gs_manage_soldiers' => __('Gestionar soldados', 'gestion-soldados'),
            'gs_edit_soldiers' => __('Editar soldados', 'gestion-soldados'),
            'gs_delete_soldiers' => __('Eliminar soldados', 'gestion-soldados'),
            'gs_export_soldiers' => __('Exportar soldados', 'gestion-soldados'),
            'gs_manage_users' => __('Gestionar usuarios', 'gestion-soldados'),
            'gs_view_soldiers' => __('Ver soldados', 'gestion-soldados')
        );

        foreach ($capabilities as $cap => $label) {
            if (!get_role('administrator')->has_cap($cap)) {
                get_role('administrator')->add_cap($cap);
            }
        }
    }

    /**
     * Initialize roles manager
     */
    public static function init() {
        // Register capabilities
        add_action('admin_init', array(__CLASS__, 'register_capabilities'));
        
        // Add menu restrictions
        add_action('admin_menu', array(__CLASS__, 'restrict_admin_menu'), 999);
    }
}
