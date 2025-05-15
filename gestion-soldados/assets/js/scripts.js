jQuery(document).ready(function($) {
    // Form validation
    $('#gs-soldier-form').on('submit', function(e) {
        let isValid = true;
        const errors = [];

        // Clear previous error messages
        $('.gs-error-message').remove();
        $('.gs-form-field').removeClass('error');

        // Required fields validation
        $(this).find('input[required], select[required], textarea[required]').each(function() {
            if (!$(this).val()) {
                isValid = false;
                const fieldName = $(this).prev('label').text().replace(':', '');
                errors.push(`${fieldName} es obligatorio`);
                $(this).closest('.gs-form-field').addClass('error');
            }
        });

        // Military ID validation
        const militaryId = $('#id_militar').val();
        if (militaryId && !militaryId.match(/^MIL\d{6}$/)) {
            isValid = false;
            errors.push('ID Militar debe tener el formato MIL seguido de 6 números');
            $('#id_militar').closest('.gs-form-field').addClass('error');
        }

        // Phone number validation
        const phone = $('#telefono_ucrania').val();
        if (phone && !phone.match(/^[0-9+\-\s()]+$/)) {
            isValid = false;
            errors.push('Número de teléfono inválido');
            $('#telefono_ucrania').closest('.gs-form-field').addClass('error');
        }

        // IBAN validation
        const iban = $('#iban').val();
        if (iban && !validateIBAN(iban)) {
            isValid = false;
            errors.push('IBAN inválido');
            $('#iban').closest('.gs-form-field').addClass('error');
        }

        // File size validation
        $('input[type="file"]').each(function() {
            if (this.files.length > 0) {
                const file = this.files[0];
                const maxSize = 5 * 1024 * 1024; // 5MB
                
                if (file.size > maxSize) {
                    isValid = false;
                    const fieldName = $(this).prev('label').text().replace(':', '');
                    errors.push(`${fieldName}: El archivo es demasiado grande (máximo 5MB)`);
                    $(this).closest('.gs-form-field').addClass('error');
                }
            }
        });

        // Display errors if any
        if (!isValid) {
            e.preventDefault();
            const errorHtml = '<div class="gs-error-message"><ul>' + 
                errors.map(error => '<li>' + error + '</li>').join('') + 
                '</ul></div>';
            $(this).prepend(errorHtml);
            
            // Scroll to error message
            $('html, body').animate({
                scrollTop: $('.gs-error-message').offset().top - 100
            }, 500);
        }
    });

    // File preview
    $('input[type="file"]').on('change', function() {
        const file = this.files[0];
        const previewContainer = $(this).siblings('.gs-preview-image');
        
        if (previewContainer.length === 0) {
            $(this).after('<div class="gs-preview-image"></div>');
        }
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                if (file.type.startsWith('image/')) {
                    previewContainer.html('<img src="' + e.target.result + '" alt="Preview">');
                } else if (file.type === 'application/pdf') {
                    previewContainer.html('<p class="gs-file-info">PDF seleccionado: ' + file.name + '</p>');
                }
            };
            reader.readAsDataURL(file);
        } else {
            previewContainer.empty();
        }
    });

    // Format IBAN as user types
    $('#iban').on('input', function() {
        let value = $(this).val().replace(/\s+/g, '').toUpperCase();
        let formatted = '';
        
        for (let i = 0; i < value.length; i++) {
            if (i > 0 && i % 4 === 0) {
                formatted += ' ';
            }
            formatted += value[i];
        }
        
        $(this).val(formatted);
    });

    // IBAN validation function
    function validateIBAN(iban) {
        const ibanRegex = /^[A-Z]{2}\d{2}[A-Z0-9]{1,30}$/;
        return ibanRegex.test(iban.replace(/\s+/g, ''));
    }

    // Dynamic form fields
    function updateRequiredFields() {
        const isNew = !$('input[name="soldier_id"]').length;
        
        if (isNew) {
            $('#foto_pasaporte, #foto_traduccion, #comprobante_banco').prop('required', true);
        }
    }

    // Initialize dynamic fields
    updateRequiredFields();

    // Confirm delete
    $('.gs-delete-soldier').on('click', function(e) {
        if (!confirm('¿Estás seguro de que deseas eliminar este soldado? Esta acción no se puede deshacer.')) {
            e.preventDefault();
        }
    });

    // Export dropdown
    $('.gs-dropdown').hover(
        function() {
            $(this).find('.gs-dropdown-content').stop(true, true).fadeIn(200);
        },
        function() {
            $(this).find('.gs-dropdown-content').stop(true, true).fadeOut(200);
        }
    );

    // Handle file input styling
    $('input[type="file"]').each(function() {
        const input = $(this);
        const label = input.prev('label');
        
        input.on('change', function(e) {
            let fileName = '';
            
            if (this.files && this.files.length > 0) {
                fileName = this.files[0].name;
            }
            
            if (fileName) {
                label.find('.gs-file-name').remove();
                label.append('<span class="gs-file-name"> - ' + fileName + '</span>');
            }
        });
    });

    // Nationality multiple select enhancement
    if ($.fn.select2) {
        $('#nacionalidad').select2({
            placeholder: 'Seleccionar nacionalidades',
            allowClear: true,
            width: '100%'
        });
    }

    // Date picker enhancement
    if ($.fn.datepicker) {
        $('.gs-date-input').datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+0'
        });
    }

    // Phone number formatting
    $('#telefono_ucrania').on('input', function() {
        let value = $(this).val().replace(/[^\d+\-()]/g, '');
        $(this).val(value);
    });

    // Success message auto-hide
    setTimeout(function() {
        $('.gs-success-message').fadeOut(500);
    }, 5000);
});
