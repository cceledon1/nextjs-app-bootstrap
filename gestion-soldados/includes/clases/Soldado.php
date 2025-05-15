<?php
// Prevent direct access
if (!defined('ABSPATH')) exit;

class Soldado {
    private $post_id;
    private $data;

    public function __construct($post_id = null) {
        $this->post_id = $post_id;
        if ($post_id) {
            $this->load_data();
        }
    }

    private function load_data() {
        $post = get_post($this->post_id);
        if (!$post || $post->post_type !== 'soldado') {
            return false;
        }

        // Get all meta data
        $this->data = array(
            'nick' => get_post_meta($this->post_id, 'nick', true),
            'nombre_completo' => get_post_meta($this->post_id, 'nombre_completo', true),
            'id_militar' => get_post_meta($this->post_id, 'id_militar', true),
            'pasaporte' => get_post_meta($this->post_id, 'pasaporte', true),
            'fecha_contrato' => get_post_meta($this->post_id, 'fecha_contrato', true),
            'fecha_nacimiento' => get_post_meta($this->post_id, 'fecha_nacimiento', true),
            'telefono_ucrania' => get_post_meta($this->post_id, 'telefono_ucrania', true),
            'nacionalidad' => get_post_meta($this->post_id, 'nacionalidad', true),
            'profesion' => get_post_meta($this->post_id, 'profesion', true),
            'contacto_emergencia_1' => get_post_meta($this->post_id, 'contacto_emergencia_1', true),
            'contacto_emergencia_2' => get_post_meta($this->post_id, 'contacto_emergencia_2', true),
            'grupo_sanguineo' => get_post_meta($this->post_id, 'grupo_sanguineo', true),
            'arma_numero' => get_post_meta($this->post_id, 'arma_numero', true),
            'numero_fiscal' => get_post_meta($this->post_id, 'numero_fiscal', true),
            'talla_uniforme' => get_post_meta($this->post_id, 'talla_uniforme', true),
            'talla_camisa' => get_post_meta($this->post_id, 'talla_camisa', true),
            'talla_botas' => get_post_meta($this->post_id, 'talla_botas', true),
            'cuenta_bancaria' => get_post_meta($this->post_id, 'cuenta_bancaria', true),
            'iban' => get_post_meta($this->post_id, 'iban', true),
            'direccion' => get_post_meta($this->post_id, 'direccion', true),
        );

        return true;
    }

    public function save($data) {
        // Validate required fields
        if (empty($data['nombre_completo']) || empty($data['id_militar'])) {
            return new WP_Error('missing_required', __('Faltan campos requeridos', 'gestion-soldados'));
        }

        // Validate military ID format
        if (!gs_validate_military_id($data['id_militar'])) {
            return new WP_Error('invalid_military_id', __('ID Militar inválido', 'gestion-soldados'));
        }

        // Create or update post
        $post_data = array(
            'post_type' => 'soldado',
            'post_title' => $data['nombre_completo'],
            'post_status' => 'publish'
        );

        if ($this->post_id) {
            $post_data['ID'] = $this->post_id;
            $post_id = wp_update_post($post_data);
        } else {
            $post_id = wp_insert_post($post_data);
        }

        if (is_wp_error($post_id)) {
            return $post_id;
        }

        // Save meta data
        $meta_fields = array(
            'nick', 'nombre_completo', 'id_militar', 'pasaporte', 'fecha_contrato',
            'fecha_nacimiento', 'telefono_ucrania', 'nacionalidad', 'profesion',
            'contacto_emergencia_1', 'contacto_emergencia_2', 'grupo_sanguineo',
            'arma_numero', 'numero_fiscal', 'talla_uniforme', 'talla_camisa',
            'talla_botas', 'cuenta_bancaria', 'iban', 'direccion'
        );

        foreach ($meta_fields as $field) {
            if (isset($data[$field])) {
                update_post_meta($post_id, $field, gs_sanitize_text($data[$field]));
            }
        }

        // Handle file uploads
        $file_fields = array(
            'contrato_file' => 'contrato_file_url',
            'foto_pasaporte' => 'foto_pasaporte_url',
            'foto_traduccion' => 'foto_traduccion_url',
            'comprobante_banco' => 'comprobante_banco_url'
        );

        foreach ($file_fields as $field => $url_field) {
            if (isset($_FILES[$field]) && !empty($_FILES[$field]['name'])) {
                $file = $_FILES[$field];
                $upload_result = $this->handle_file_upload($file);
                
                if (!is_wp_error($upload_result)) {
                    update_post_meta($post_id, $url_field, $upload_result);
                }
            }
        }

        $this->post_id = $post_id;
        return $post_id;
    }

    private function handle_file_upload($file) {
        // Validate file
        $validation = gs_validate_file($file);
        if (is_wp_error($validation)) {
            return $validation;
        }

        // Setup WordPress upload directory
        $upload_dir = wp_upload_dir();
        $target_dir = $upload_dir['basedir'] . '/soldados-docs/';

        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
            wp_mkdir_p($target_dir);
        }

        // Generate unique filename
        $filename = wp_unique_filename($target_dir, $file['name']);
        $target_path = $target_dir . $filename;

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $target_path)) {
            return new WP_Error('upload_error', __('Error al mover el archivo subido', 'gestion-soldados'));
        }

        return $upload_dir['baseurl'] . '/soldados-docs/' . $filename;
    }

    public function delete() {
        if (!$this->post_id) {
            return false;
        }

        // Delete associated files
        $file_fields = array(
            'contrato_file_url',
            'foto_pasaporte_url',
            'foto_traduccion_url',
            'comprobante_banco_url'
        );

        foreach ($file_fields as $field) {
            $file_url = get_post_meta($this->post_id, $field, true);
            if ($file_url) {
                $file_path = str_replace(wp_upload_dir()['baseurl'], wp_upload_dir()['basedir'], $file_url);
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
        }

        // Delete post and meta data
        return wp_delete_post($this->post_id, true);
    }

    public function get_data() {
        return $this->data;
    }

    public static function get_all($args = array()) {
        $default_args = array(
            'post_type' => 'soldado',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC'
        );

        $args = wp_parse_args($args, $default_args);
        $posts = get_posts($args);
        
        $soldiers = array();
        foreach ($posts as $post) {
            $soldier = new self($post->ID);
            $soldiers[] = $soldier;
        }

        return $soldiers;
    }
}
