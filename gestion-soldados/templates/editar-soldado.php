<?php
// Prevent direct access
if (!defined('ABSPATH')) exit;

// Check if user has permission to edit
if (!RolesManager::can_edit_soldiers()) {
    wp_die(__('No tienes permiso para editar soldados', 'gestion-soldados'));
}

$soldier_id = get_the_ID();
$soldier = new Soldado($soldier_id);
$data = $soldier->get_data();
$is_new = empty($soldier_id);

// Get options for select fields
$blood_types = gs_get_blood_types();
$nationalities = gs_get_nationalities();
$uniform_sizes = gs_get_uniform_sizes();
$boot_sizes = gs_get_boot_sizes();
?>

<div class="gs-container">
    <form id="gs-soldier-form" class="gs-edit-form" method="post" enctype="multipart/form-data">
        <?php wp_nonce_field('gs_edit_soldier', 'gs_nonce'); ?>
        <input type="hidden" name="action" value="gs_save_soldier">
        <?php if (!$is_new): ?>
            <input type="hidden" name="soldier_id" value="<?php echo esc_attr($soldier_id); ?>">
        <?php endif; ?>

        <!-- Form Header -->
        <div class="gs-form-header">
            <h1><?php echo $is_new ? __('Nuevo Soldado', 'gestion-soldados') : __('Editar Soldado', 'gestion-soldados'); ?></h1>
        </div>

        <!-- Personal Information -->
        <div class="gs-form-section">
            <h2><?php _e('Información Personal', 'gestion-soldados'); ?></h2>
            
            <div class="gs-form-grid">
                <div class="gs-form-field">
                    <label for="nick"><?php _e('Nick/Apodo:', 'gestion-soldados'); ?></label>
                    <input type="text" id="nick" name="nick" value="<?php echo esc_attr($data['nick'] ?? ''); ?>">
                </div>

                <div class="gs-form-field required">
                    <label for="nombre_completo"><?php _e('Nombre Completo:', 'gestion-soldados'); ?></label>
                    <input type="text" id="nombre_completo" name="nombre_completo" 
                           value="<?php echo esc_attr($data['nombre_completo'] ?? ''); ?>" required>
                </div>

                <div class="gs-form-field required">
                    <label for="id_militar"><?php _e('ID Militar:', 'gestion-soldados'); ?></label>
                    <input type="text" id="id_militar" name="id_militar" 
                           value="<?php echo esc_attr($data['id_militar'] ?? ''); ?>" 
                           pattern="MIL\d{6}" title="<?php _e('Formato: MIL seguido de 6 números', 'gestion-soldados'); ?>" required>
                </div>

                <div class="gs-form-field required">
                    <label for="pasaporte"><?php _e('Número de Pasaporte:', 'gestion-soldados'); ?></label>
                    <input type="text" id="pasaporte" name="pasaporte" 
                           value="<?php echo esc_attr($data['pasaporte'] ?? ''); ?>" required>
                </div>

                <div class="gs-form-field required">
                    <label for="fecha_nacimiento"><?php _e('Fecha de Nacimiento:', 'gestion-soldados'); ?></label>
                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" 
                           value="<?php echo esc_attr($data['fecha_nacimiento'] ?? ''); ?>" required>
                </div>

                <div class="gs-form-field">
                    <label for="nacionalidad"><?php _e('Nacionalidad:', 'gestion-soldados'); ?></label>
                    <select id="nacionalidad" name="nacionalidad[]" multiple>
                        <?php foreach ($nationalities as $code => $name): ?>
                            <option value="<?php echo esc_attr($code); ?>" 
                                <?php echo in_array($code, (array)($data['nacionalidad'] ?? array())) ? 'selected' : ''; ?>>
                                <?php echo esc_html($name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="gs-form-field">
                    <label for="profesion"><?php _e('Profesión:', 'gestion-soldados'); ?></label>
                    <input type="text" id="profesion" name="profesion" 
                           value="<?php echo esc_attr($data['profesion'] ?? ''); ?>">
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="gs-form-section">
            <h2><?php _e('Información de Contacto', 'gestion-soldados'); ?></h2>
            
            <div class="gs-form-grid">
                <div class="gs-form-field required">
                    <label for="telefono_ucrania"><?php _e('Teléfono en Ucrania:', 'gestion-soldados'); ?></label>
                    <input type="tel" id="telefono_ucrania" name="telefono_ucrania" 
                           value="<?php echo esc_attr($data['telefono_ucrania'] ?? ''); ?>" required>
                </div>

                <div class="gs-form-field">
                    <label for="direccion"><?php _e('Dirección:', 'gestion-soldados'); ?></label>
                    <textarea id="direccion" name="direccion"><?php echo esc_textarea($data['direccion'] ?? ''); ?></textarea>
                </div>

                <div class="gs-form-field required">
                    <label for="contacto_emergencia_1"><?php _e('Contacto de Emergencia 1:', 'gestion-soldados'); ?></label>
                    <input type="text" id="contacto_emergencia_1" name="contacto_emergencia_1" 
                           value="<?php echo esc_attr($data['contacto_emergencia_1'] ?? ''); ?>" required>
                </div>

                <div class="gs-form-field">
                    <label for="contacto_emergencia_2"><?php _e('Contacto de Emergencia 2:', 'gestion-soldados'); ?></label>
                    <input type="text" id="contacto_emergencia_2" name="contacto_emergencia_2" 
                           value="<?php echo esc_attr($data['contacto_emergencia_2'] ?? ''); ?>">
                </div>
            </div>
        </div>

        <!-- Military Information -->
        <div class="gs-form-section">
            <h2><?php _e('Información Militar', 'gestion-soldados'); ?></h2>
            
            <div class="gs-form-grid">
                <div class="gs-form-field required">
                    <label for="grupo_sanguineo"><?php _e('Grupo Sanguíneo:', 'gestion-soldados'); ?></label>
                    <select id="grupo_sanguineo" name="grupo_sanguineo" required>
                        <option value=""><?php _e('Seleccionar', 'gestion-soldados'); ?></option>
                        <?php foreach ($blood_types as $type => $label): ?>
                            <option value="<?php echo esc_attr($type); ?>" 
                                <?php selected($data['grupo_sanguineo'] ?? '', $type); ?>>
                                <?php echo esc_html($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="gs-form-field required">
                    <label for="arma_numero"><?php _e('Arma y N° de Serie:', 'gestion-soldados'); ?></label>
                    <input type="text" id="arma_numero" name="arma_numero" 
                           value="<?php echo esc_attr($data['arma_numero'] ?? ''); ?>" required>
                </div>

                <div class="gs-form-field required">
                    <label for="fecha_contrato"><?php _e('Fecha de Contrato:', 'gestion-soldados'); ?></label>
                    <input type="date" id="fecha_contrato" name="fecha_contrato" 
                           value="<?php echo esc_attr($data['fecha_contrato'] ?? ''); ?>" required>
                </div>

                <div class="gs-form-field required">
                    <label for="contrato_file"><?php _e('Documento de Contrato:', 'gestion-soldados'); ?></label>
                    <input type="file" id="contrato_file" name="contrato_file" accept=".pdf,.doc,.docx">
                    <?php if (!empty($data['contrato_file_url'])): ?>
                        <p class="gs-file-info"><?php _e('Archivo actual:', 'gestion-soldados'); ?> 
                            <a href="<?php echo esc_url($data['contrato_file_url']); ?>" target="_blank">
                                <?php _e('Ver documento', 'gestion-soldados'); ?>
                            </a>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Equipment Information -->
        <div class="gs-form-section">
            <h2><?php _e('Equipamiento', 'gestion-soldados'); ?></h2>
            
            <div class="gs-form-grid">
                <div class="gs-form-field required">
                    <label for="talla_uniforme"><?php _e('Talla Uniforme:', 'gestion-soldados'); ?></label>
                    <select id="talla_uniforme" name="talla_uniforme" required>
                        <option value=""><?php _e('Seleccionar', 'gestion-soldados'); ?></option>
                        <?php foreach ($uniform_sizes as $size => $label): ?>
                            <option value="<?php echo esc_attr($size); ?>" 
                                <?php selected($data['talla_uniforme'] ?? '', $size); ?>>
                                <?php echo esc_html($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="gs-form-field required">
                    <label for="talla_camisa"><?php _e('Talla Camisa:', 'gestion-soldados'); ?></label>
                    <select id="talla_camisa" name="talla_camisa" required>
                        <option value=""><?php _e('Seleccionar', 'gestion-soldados'); ?></option>
                        <?php foreach ($uniform_sizes as $size => $label): ?>
                            <option value="<?php echo esc_attr($size); ?>" 
                                <?php selected($data['talla_camisa'] ?? '', $size); ?>>
                                <?php echo esc_html($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="gs-form-field required">
                    <label for="talla_botas"><?php _e('Talla Botas:', 'gestion-soldados'); ?></label>
                    <select id="talla_botas" name="talla_botas" required>
                        <option value=""><?php _e('Seleccionar', 'gestion-soldados'); ?></option>
                        <?php foreach ($boot_sizes as $size => $label): ?>
                            <option value="<?php echo esc_attr($size); ?>" 
                                <?php selected($data['talla_botas'] ?? '', $size); ?>>
                                <?php echo esc_html($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Financial Information -->
        <div class="gs-form-section">
            <h2><?php _e('Información Financiera', 'gestion-soldados'); ?></h2>
            
            <div class="gs-form-grid">
                <div class="gs-form-field required">
                    <label for="numero_fiscal"><?php _e('Número Fiscal:', 'gestion-soldados'); ?></label>
                    <input type="text" id="numero_fiscal" name="numero_fiscal" 
                           value="<?php echo esc_attr($data['numero_fiscal'] ?? ''); ?>" required>
                </div>

                <div class="gs-form-field required">
                    <label for="cuenta_bancaria"><?php _e('Cuenta Bancaria:', 'gestion-soldados'); ?></label>
                    <input type="text" id="cuenta_bancaria" name="cuenta_bancaria" 
                           value="<?php echo esc_attr($data['cuenta_bancaria'] ?? ''); ?>" required>
                </div>

                <div class="gs-form-field required">
                    <label for="iban"><?php _e('IBAN:', 'gestion-soldados'); ?></label>
                    <input type="text" id="iban" name="iban" 
                           value="<?php echo esc_attr($data['iban'] ?? ''); ?>" required>
                </div>
            </div>
        </div>

        <!-- Documents -->
        <div class="gs-form-section">
            <h2><?php _e('Documentos', 'gestion-soldados'); ?></h2>
            
            <div class="gs-form-grid">
                <div class="gs-form-field">
                    <label for="foto_pasaporte"><?php _e('Foto Pasaporte:', 'gestion-soldados'); ?></label>
                    <input type="file" id="foto_pasaporte" name="foto_pasaporte" accept="image/*">
                    <?php if (!empty($data['foto_pasaporte_url'])): ?>
                        <div class="gs-preview-image">
                            <img src="<?php echo esc_url($data['foto_pasaporte_url']); ?>" alt="<?php _e('Pasaporte', 'gestion-soldados'); ?>">
                        </div>
                    <?php endif; ?>
                </div>

                <div class="gs-form-field">
                    <label for="foto_traduccion"><?php _e('Foto Traducción Pasaporte:', 'gestion-soldados'); ?></label>
                    <input type="file" id="foto_traduccion" name="foto_traduccion" accept="image/*">
                    <?php if (!empty($data['foto_traduccion_url'])): ?>
                        <div class="gs-preview-image">
                            <img src="<?php echo esc_url($data['foto_traduccion_url']); ?>" alt="<?php _e('Traducción', 'gestion-soldados'); ?>">
                        </div>
                    <?php endif; ?>
                </div>

                <div class="gs-form-field">
                    <label for="comprobante_banco"><?php _e('Comprobante Bancario:', 'gestion-soldados'); ?></label>
                    <input type="file" id="comprobante_banco" name="comprobante_banco" accept="image/*,.pdf">
                    <?php if (!empty($data['comprobante_banco_url'])): ?>
                        <div class="gs-preview-image">
                            <?php if (pathinfo($data['comprobante_banco_url'], PATHINFO_EXTENSION) === 'pdf'): ?>
                                <a href="<?php echo esc_url($data['comprobante_banco_url']); ?>" target="_blank">
                                    <?php _e('Ver PDF', 'gestion-soldados'); ?>
                                </a>
                            <?php else: ?>
                                <img src="<?php echo esc_url($data['comprobante_banco_url']); ?>" alt="<?php _e('Comprobante', 'gestion-soldados'); ?>">
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="gs-form-actions">
            <button type="submit" class="gs-btn gs-btn-primary">
                <?php echo $is_new ? __('Crear Soldado', 'gestion-soldados') : __('Guardar Cambios', 'gestion-soldados'); ?>
            </button>
            <a href="<?php echo esc_url(get_permalink()); ?>" class="gs-btn gs-btn-secondary">
                <?php _e('Cancelar', 'gestion-soldados'); ?>
            </a>
        </div>
    </form>
</div>
