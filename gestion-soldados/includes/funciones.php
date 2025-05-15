<?php
// Prevent direct access
if (!defined('ABSPATH')) exit;

// Sanitize text input
function gs_sanitize_text($text) {
    return sanitize_text_field($text);
}

// Validate date
function gs_validate_date($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

// Validate phone number
function gs_validate_phone($phone) {
    return preg_match('/^[0-9+\-\s()]+$/', $phone);
}

// Validate file upload
function gs_validate_file($file) {
    // Allowed file types: image and PDF/documents
    $allowed_mimes = array(
        'image/jpeg',
        'image/png',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    );

    if (!in_array($file['type'], $allowed_mimes)) {
        return new WP_Error('invalid_file', __('El tipo de archivo no es permitido', 'gestion-soldados'));
    }

    if ($file['error']) {
        return new WP_Error('upload_error', __('Error en la subida del archivo', 'gestion-soldados'));
    }

    // Check file size (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        return new WP_Error('file_too_large', __('El archivo es demasiado grande. Máximo 5MB', 'gestion-soldados'));
    }

    return true;
}

// Get blood type options
function gs_get_blood_types() {
    return array(
        'A+' => 'A+',
        'A-' => 'A-',
        'B+' => 'B+',
        'B-' => 'B-',
        'AB+' => 'AB+',
        'AB-' => 'AB-',
        'O+' => 'O+',
        'O-' => 'O-'
    );
}

// Format IBAN
function gs_format_iban($iban) {
    $iban = preg_replace('/\s+/', '', $iban);
    return trim(chunk_split($iban, 4, ' '));
}

// Validate IBAN
function gs_validate_iban($iban) {
    $iban = preg_replace('/\s+/', '', $iban);
    return preg_match('/^[A-Z]{2}\d{2}[A-Z0-9]{1,30}$/', $iban);
}

// Log plugin errors
function gs_log_error($error_message) {
    if (WP_DEBUG === true) {
        error_log('[Gestión Soldados] ' . $error_message);
    }
}

// Check user permissions
function gs_user_can_edit() {
    $user = wp_get_current_user();
    return in_array('administrator', $user->roles) || 
           in_array('gs_root', $user->roles) || 
           in_array('gs_editor', $user->roles);
}

// Check if user can only view
function gs_user_can_view() {
    $user = wp_get_current_user();
    return in_array('gs_visualizador', $user->roles) || gs_user_can_edit();
}

// Generate unique military ID
function gs_generate_military_id() {
    $prefix = 'MIL';
    $random = substr(str_shuffle('0123456789'), 0, 6);
    return $prefix . $random;
}

// Validate military ID format
function gs_validate_military_id($id) {
    return preg_match('/^MIL\d{6}$/', $id);
}

// Get nationality options
function gs_get_nationalities() {
    return array(
        'ESP' => __('Española', 'gestion-soldados'),
        'UKR' => __('Ucraniana', 'gestion-soldados'),
        'USA' => __('Estadounidense', 'gestion-soldados'),
        'GBR' => __('Británica', 'gestion-soldados'),
        'FRA' => __('Francesa', 'gestion-soldados'),
        'DEU' => __('Alemana', 'gestion-soldados'),
        'POL' => __('Polaca', 'gestion-soldados'),
        'ITA' => __('Italiana', 'gestion-soldados'),
        // Add more nationalities as needed
    );
}

// Get uniform sizes
function gs_get_uniform_sizes() {
    return array(
        'XS' => 'XS',
        'S' => 'S',
        'M' => 'M',
        'L' => 'L',
        'XL' => 'XL',
        'XXL' => 'XXL',
        'XXXL' => 'XXXL'
    );
}

// Get boot sizes
function gs_get_boot_sizes() {
    $sizes = array();
    for ($i = 36; $i <= 47; $i++) {
        $sizes[$i] = $i;
    }
    return $sizes;
}
