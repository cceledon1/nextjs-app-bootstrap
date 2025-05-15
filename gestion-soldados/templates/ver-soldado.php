<?php
// Prevent direct access
if (!defined('ABSPATH')) exit;

// Check if user has permission to view
if (!RolesManager::can_view_soldiers()) {
    wp_die(__('No tienes permiso para ver esta página', 'gestion-soldados'));
}

$soldier_id = get_the_ID();
$soldier = new Soldado($soldier_id);
$data = $soldier->get_data();
?>

<div class="gs-container">
    <div class="gs-soldier-view">
        <!-- Header Section -->
        <div class="gs-header">
            <h1><?php echo esc_html($data['nombre_completo']); ?></h1>
            <div class="gs-header-actions">
                <?php if (RolesManager::can_edit_soldiers()): ?>
                    <a href="<?php echo esc_url(add_query_arg('action', 'edit', get_permalink())); ?>" class="gs-btn gs-btn-primary">
                        <?php _e('Editar', 'gestion-soldados'); ?>
                    </a>
                <?php endif; ?>
                
                <?php if (RolesManager::can_export_soldiers()): ?>
                    <div class="gs-dropdown">
                        <button class="gs-btn gs-btn-secondary"><?php _e('Exportar', 'gestion-soldados'); ?></button>
                        <div class="gs-dropdown-content">
                            <a href="<?php echo esc_url(add_query_arg('export', 'pdf')); ?>"><?php _e('PDF', 'gestion-soldados'); ?></a>
                            <a href="<?php echo esc_url(add_query_arg('export', 'excel')); ?>"><?php _e('Excel', 'gestion-soldados'); ?></a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Main Content -->
        <div class="gs-content">
            <!-- Personal Information -->
            <div class="gs-section">
                <h2><?php _e('Información Personal', 'gestion-soldados'); ?></h2>
                <div class="gs-grid">
                    <div class="gs-field">
                        <label><?php _e('Nick/Apodo:', 'gestion-soldados'); ?></label>
                        <span><?php echo esc_html($data['nick']); ?></span>
                    </div>
                    <div class="gs-field">
                        <label><?php _e('ID Militar:', 'gestion-soldados'); ?></label>
                        <span><?php echo esc_html($data['id_militar']); ?></span>
                    </div>
                    <div class="gs-field">
                        <label><?php _e('Pasaporte:', 'gestion-soldados'); ?></label>
                        <span><?php echo esc_html($data['pasaporte']); ?></span>
                    </div>
                    <div class="gs-field">
                        <label><?php _e('Fecha de Nacimiento:', 'gestion-soldados'); ?></label>
                        <span><?php echo esc_html($data['fecha_nacimiento']); ?></span>
                    </div>
                    <div class="gs-field">
                        <label><?php _e('Nacionalidad:', 'gestion-soldados'); ?></label>
                        <span><?php echo esc_html($data['nacionalidad']); ?></span>
                    </div>
                    <div class="gs-field">
                        <label><?php _e('Profesión:', 'gestion-soldados'); ?></label>
                        <span><?php echo esc_html($data['profesion']); ?></span>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="gs-section">
                <h2><?php _e('Información de Contacto', 'gestion-soldados'); ?></h2>
                <div class="gs-grid">
                    <div class="gs-field">
                        <label><?php _e('Teléfono en Ucrania:', 'gestion-soldados'); ?></label>
                        <span><?php echo esc_html($data['telefono_ucrania']); ?></span>
                    </div>
                    <div class="gs-field">
                        <label><?php _e('Dirección:', 'gestion-soldados'); ?></label>
                        <span><?php echo esc_html($data['direccion']); ?></span>
                    </div>
                </div>

                <!-- Emergency Contacts -->
                <h3><?php _e('Contactos de Emergencia', 'gestion-soldados'); ?></h3>
                <div class="gs-grid">
                    <div class="gs-field">
                        <label><?php _e('Contacto 1:', 'gestion-soldados'); ?></label>
                        <span><?php echo esc_html($data['contacto_emergencia_1']); ?></span>
                    </div>
                    <div class="gs-field">
                        <label><?php _e('Contacto 2:', 'gestion-soldados'); ?></label>
                        <span><?php echo esc_html($data['contacto_emergencia_2']); ?></span>
                    </div>
                </div>
            </div>

            <!-- Military Information -->
            <div class="gs-section">
                <h2><?php _e('Información Militar', 'gestion-soldados'); ?></h2>
                <div class="gs-grid">
                    <div class="gs-field">
                        <label><?php _e('Grupo Sanguíneo:', 'gestion-soldados'); ?></label>
                        <span><?php echo esc_html($data['grupo_sanguineo']); ?></span>
                    </div>
                    <div class="gs-field">
                        <label><?php _e('Arma y N° de Serie:', 'gestion-soldados'); ?></label>
                        <span><?php echo esc_html($data['arma_numero']); ?></span>
                    </div>
                    <div class="gs-field">
                        <label><?php _e('Fecha de Contrato:', 'gestion-soldados'); ?></label>
                        <span><?php echo esc_html($data['fecha_contrato']); ?></span>
                    </div>
                </div>
            </div>

            <!-- Equipment Information -->
            <div class="gs-section">
                <h2><?php _e('Equipamiento', 'gestion-soldados'); ?></h2>
                <div class="gs-grid">
                    <div class="gs-field">
                        <label><?php _e('Talla Uniforme:', 'gestion-soldados'); ?></label>
                        <span><?php echo esc_html($data['talla_uniforme']); ?></span>
                    </div>
                    <div class="gs-field">
                        <label><?php _e('Talla Camisa:', 'gestion-soldados'); ?></label>
                        <span><?php echo esc_html($data['talla_camisa']); ?></span>
                    </div>
                    <div class="gs-field">
                        <label><?php _e('Talla Botas:', 'gestion-soldados'); ?></label>
                        <span><?php echo esc_html($data['talla_botas']); ?></span>
                    </div>
                </div>
            </div>

            <!-- Financial Information -->
            <div class="gs-section">
                <h2><?php _e('Información Financiera', 'gestion-soldados'); ?></h2>
                <div class="gs-grid">
                    <div class="gs-field">
                        <label><?php _e('Número Fiscal:', 'gestion-soldados'); ?></label>
                        <span><?php echo esc_html($data['numero_fiscal']); ?></span>
                    </div>
                    <div class="gs-field">
                        <label><?php _e('Cuenta Bancaria:', 'gestion-soldados'); ?></label>
                        <span><?php echo esc_html($data['cuenta_bancaria']); ?></span>
                    </div>
                    <div class="gs-field">
                        <label><?php _e('IBAN:', 'gestion-soldados'); ?></label>
                        <span><?php echo esc_html($data['iban']); ?></span>
                    </div>
                </div>
            </div>

            <!-- Documents -->
            <div class="gs-section">
                <h2><?php _e('Documentos', 'gestion-soldados'); ?></h2>
                <div class="gs-documents-grid">
                    <?php if (!empty($data['foto_pasaporte_url'])): ?>
                        <div class="gs-document-card">
                            <img src="<?php echo esc_url($data['foto_pasaporte_url']); ?>" alt="<?php _e('Pasaporte', 'gestion-soldados'); ?>">
                            <p><?php _e('Pasaporte', 'gestion-soldados'); ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($data['foto_traduccion_url'])): ?>
                        <div class="gs-document-card">
                            <img src="<?php echo esc_url($data['foto_traduccion_url']); ?>" alt="<?php _e('Traducción Pasaporte', 'gestion-soldados'); ?>">
                            <p><?php _e('Traducción Pasaporte', 'gestion-soldados'); ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($data['comprobante_banco_url'])): ?>
                        <div class="gs-document-card">
                            <img src="<?php echo esc_url($data['comprobante_banco_url']); ?>" alt="<?php _e('Comprobante Bancario', 'gestion-soldados'); ?>">
                            <p><?php _e('Comprobante Bancario', 'gestion-soldados'); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
