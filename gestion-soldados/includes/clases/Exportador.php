<?php
// Prevent direct access
if (!defined('ABSPATH')) exit;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Mpdf\Mpdf;

class Exportador {
    private $soldiers;

    public function __construct($soldiers = array()) {
        $this->soldiers = $soldiers;
    }

    public function export_excel() {
        try {
            // Create new Spreadsheet object
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set headers
            $headers = array(
                'ID Militar',
                'Nick',
                'Nombre Completo',
                'Pasaporte',
                'Fecha Contrato',
                'Fecha Nacimiento',
                'Teléfono Ucrania',
                'Nacionalidad',
                'Profesión',
                'Contacto Emergencia 1',
                'Contacto Emergencia 2',
                'Grupo Sanguíneo',
                'Arma y Número',
                'Número Fiscal',
                'Talla Uniforme',
                'Talla Camisa',
                'Talla Botas',
                'Cuenta Bancaria',
                'IBAN',
                'Dirección'
            );

            // Style the header row
            $headerStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
            ];

            // Apply headers
            foreach ($headers as $col => $header) {
                $cell = $sheet->getCellByColumnAndRow($col + 1, 1);
                $cell->setValue($header);
                $sheet->getStyleByColumnAndRow($col + 1, 1)->applyFromArray($headerStyle);
            }

            // Add data
            $row = 2;
            foreach ($this->soldiers as $soldier) {
                $data = $soldier->get_data();
                $col = 1;
                
                $sheet->setCellValueByColumnAndRow($col++, $row, $data['id_militar']);
                $sheet->setCellValueByColumnAndRow($col++, $row, $data['nick']);
                $sheet->setCellValueByColumnAndRow($col++, $row, $data['nombre_completo']);
                $sheet->setCellValueByColumnAndRow($col++, $row, $data['pasaporte']);
                $sheet->setCellValueByColumnAndRow($col++, $row, $data['fecha_contrato']);
                $sheet->setCellValueByColumnAndRow($col++, $row, $data['fecha_nacimiento']);
                $sheet->setCellValueByColumnAndRow($col++, $row, $data['telefono_ucrania']);
                $sheet->setCellValueByColumnAndRow($col++, $row, $data['nacionalidad']);
                $sheet->setCellValueByColumnAndRow($col++, $row, $data['profesion']);
                $sheet->setCellValueByColumnAndRow($col++, $row, $data['contacto_emergencia_1']);
                $sheet->setCellValueByColumnAndRow($col++, $row, $data['contacto_emergencia_2']);
                $sheet->setCellValueByColumnAndRow($col++, $row, $data['grupo_sanguineo']);
                $sheet->setCellValueByColumnAndRow($col++, $row, $data['arma_numero']);
                $sheet->setCellValueByColumnAndRow($col++, $row, $data['numero_fiscal']);
                $sheet->setCellValueByColumnAndRow($col++, $row, $data['talla_uniforme']);
                $sheet->setCellValueByColumnAndRow($col++, $row, $data['talla_camisa']);
                $sheet->setCellValueByColumnAndRow($col++, $row, $data['talla_botas']);
                $sheet->setCellValueByColumnAndRow($col++, $row, $data['cuenta_bancaria']);
                $sheet->setCellValueByColumnAndRow($col++, $row, $data['iban']);
                $sheet->setCellValueByColumnAndRow($col++, $row, $data['direccion']);
                
                $row++;
            }

            // Auto-size columns
            foreach (range('A', $sheet->getHighestColumn()) as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Create writer and save file
            $writer = new Xlsx($spreadsheet);
            $filename = 'soldados-' . date('Y-m-d-His') . '.xlsx';
            $filepath = wp_upload_dir()['path'] . '/' . $filename;
            $writer->save($filepath);

            return $filepath;

        } catch (Exception $e) {
            gs_log_error('Excel export error: ' . $e->getMessage());
            return new WP_Error('excel_export_error', __('Error al exportar a Excel', 'gestion-soldados'));
        }
    }

    public function export_pdf() {
        try {
            // Initialize mPDF
            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 15,
                'margin_bottom' => 15,
            ]);

            // Set document metadata
            $mpdf->SetTitle('Registro de Soldados');
            $mpdf->SetAuthor('Sistema de Gestión de Soldados');

            // Start building HTML content
            $html = '
            <style>
                body { font-family: "dejavusans"; }
                .header { text-align: center; margin-bottom: 20px; }
                .soldier-card { 
                    border: 1px solid #ddd; 
                    padding: 15px; 
                    margin-bottom: 20px;
                    page-break-inside: avoid;
                }
                .soldier-card h2 { 
                    background: #4472C4; 
                    color: white; 
                    padding: 5px; 
                    margin: -15px -15px 15px -15px;
                }
                .field { margin-bottom: 10px; }
                .label { font-weight: bold; }
                .images { 
                    display: flex; 
                    justify-content: space-between; 
                    margin-top: 15px; 
                }
                .image-container {
                    width: 30%;
                    text-align: center;
                }
                img { max-width: 100%; height: auto; }
            </style>
            
            <div class="header">
                <h1>' . __('Registro de Soldados', 'gestion-soldados') . '</h1>
                <p>' . __('Fecha de generación:', 'gestion-soldados') . ' ' . date('d/m/Y H:i') . '</p>
            </div>';

            // Add soldier data
            foreach ($this->soldiers as $soldier) {
                $data = $soldier->get_data();
                
                $html .= '
                <div class="soldier-card">
                    <h2>' . esc_html($data['nombre_completo']) . '</h2>
                    
                    <div class="field">
                        <span class="label">' . __('ID Militar:', 'gestion-soldados') . '</span>
                        <span>' . esc_html($data['id_militar']) . '</span>
                    </div>
                    
                    <div class="field">
                        <span class="label">' . __('Nick:', 'gestion-soldados') . '</span>
                        <span>' . esc_html($data['nick']) . '</span>
                    </div>
                    
                    <div class="field">
                        <span class="label">' . __('Pasaporte:', 'gestion-soldados') . '</span>
                        <span>' . esc_html($data['pasaporte']) . '</span>
                    </div>
                    
                    <div class="field">
                        <span class="label">' . __('Fecha Contrato:', 'gestion-soldados') . '</span>
                        <span>' . esc_html($data['fecha_contrato']) . '</span>
                    </div>
                    
                    <div class="field">
                        <span class="label">' . __('Grupo Sanguíneo:', 'gestion-soldados') . '</span>
                        <span>' . esc_html($data['grupo_sanguineo']) . '</span>
                    </div>
                    
                    <div class="field">
                        <span class="label">' . __('Arma y Número:', 'gestion-soldados') . '</span>
                        <span>' . esc_html($data['arma_numero']) . '</span>
                    </div>';

                // Add images if available
                if (!empty($data['foto_pasaporte_url']) || 
                    !empty($data['foto_traduccion_url']) || 
                    !empty($data['comprobante_banco_url'])) {
                    
                    $html .= '<div class="images">';
                    
                    if (!empty($data['foto_pasaporte_url'])) {
                        $html .= '
                        <div class="image-container">
                            <img src="' . esc_url($data['foto_pasaporte_url']) . '" alt="Pasaporte">
                            <p>' . __('Pasaporte', 'gestion-soldados') . '</p>
                        </div>';
                    }
                    
                    if (!empty($data['foto_traduccion_url'])) {
                        $html .= '
                        <div class="image-container">
                            <img src="' . esc_url($data['foto_traduccion_url']) . '" alt="Traducción">
                            <p>' . __('Traducción', 'gestion-soldados') . '</p>
                        </div>';
                    }
                    
                    if (!empty($data['comprobante_banco_url'])) {
                        $html .= '
                        <div class="image-container">
                            <img src="' . esc_url($data['comprobante_banco_url']) . '" alt="Comprobante">
                            <p>' . __('Comprobante Bancario', 'gestion-soldados') . '</p>
                        </div>';
                    }
                    
                    $html .= '</div>';
                }
                
                $html .= '</div>';
            }

            // Write HTML to PDF
            $mpdf->WriteHTML($html);

            // Generate and save file
            $filename = 'soldados-' . date('Y-m-d-His') . '.pdf';
            $filepath = wp_upload_dir()['path'] . '/' . $filename;
            $mpdf->Output($filepath, 'F');

            return $filepath;

        } catch (Exception $e) {
            gs_log_error('PDF export error: ' . $e->getMessage());
            return new WP_Error('pdf_export_error', __('Error al exportar a PDF', 'gestion-soldados'));
        }
    }
}
