<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Border;

class GenericExport
{
    protected $data;
    protected $headings;
    protected $mappingCallback;
    protected $title;
    protected $headerStyle;

    /**
     * Constructor de la clase GenericExport
     *
     * @param Collection|SupportCollection $data - Los datos ya filtrados para exportar
     * @param array $headings - Encabezados de las columnas
     * @param callable|null $mappingCallback - Función para mapear cada fila
     * @param string|null $title - Título de la hoja de Excel
     * @param array $headerStyle - Estilos personalizados para el encabezado
     */
    public function __construct(
        $data, 
        array $headings, 
        ?callable $mappingCallback = null, 
        ?string $title = null,
        array $headerStyle = []
    ) {
        $this->data = $data;
        $this->headings = $headings;
        $this->mappingCallback = $mappingCallback;
        $this->title = $title ?? 'Exportación';
        
        // Estilo por defecto para encabezados
        $this->headerStyle = array_merge([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4A5568'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ], $headerStyle);
    }

    /**
     * Genera y descarga el archivo Excel
     */
    public function download($filename = null)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($this->sanitizeSheetTitle($this->title));

        // Escribir encabezados
        $column = 'A';
        foreach ($this->headings as $header) {
            $sheet->setCellValue($column . '1', $header);
            $column++;
        }

        // Aplicar estilos a los encabezados
        $lastColumn = $this->getColumnLetter(count($this->headings));
        $headerRange = 'A1:' . $lastColumn . '1';
        
        $sheet->getStyle($headerRange)->applyFromArray($this->headerStyle);

        // Escribir datos
        $row = 2;
        foreach ($this->data as $item) {
            $mappedData = $this->mapRow($item);
            
            $column = 'A';
            foreach ($mappedData as $value) {
                $sheet->setCellValue($column . $row, $value);
                $column++;
            }
            $row++;
        }

        // Aplicar bordes a todas las celdas con datos
        if ($row > 2) {
            $dataRange = 'A1:' . $lastColumn . ($row - 1);
            $sheet->getStyle($dataRange)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => Color::COLOR_BLACK],
                    ],
                ],
            ]);
        }

        // Ajustar ancho de columnas automáticamente
        foreach (range('A', $lastColumn) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Configurar propiedades del documento
        $spreadsheet->getProperties()
            ->setCreator("Sistema")
            ->setLastModifiedBy("Sistema")
            ->setTitle($this->title)
            ->setSubject($this->title)
            ->setDescription("Exportación generada por el sistema")
            ->setKeywords("exportacion excel")
            ->setCategory("Reportes");

        // Crear el writer
        $writer = new Xlsx($spreadsheet);
        $filename = $filename ?: strtolower(str_replace(' ', '_', $this->title)) . '_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        // Retornar la respuesta de descarga
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Mapea cada fila de datos
     */
    protected function mapRow($row): array
    {
        if ($this->mappingCallback) {
            return call_user_func($this->mappingCallback, $row);
        }

        // Mapeo por defecto si no se proporciona callback
        $mapped = [];
        foreach ($this->headings as $heading) {
            $key = strtolower(str_replace([' ', '/'], ['_', '_'], $heading));
            $mapped[] = $this->getNestedValue($row, $key) ?? 'N/A';
        }
        return $mapped;
    }

    /**
     * Obtiene valores anidados de objetos/arrays de forma inteligente
     */
    protected function getNestedValue($object, $key)
    {
        if (is_array($object)) {
            return $object[$key] ?? null;
        }

        if (is_object($object)) {
            // Intentar acceder como propiedad
            if (property_exists($object, $key)) {
                return $object->$key;
            }
            
            // Intentar acceder como método
            if (method_exists($object, $key)) {
                return $object->$key();
            }
            
            // Intentar acceder como atributo de Eloquent
            try {
                return $object->getAttribute($key);
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }

    /**
     * Método estático para crear y descargar de forma fluida
     */
    public static function create($data, $headings, $mappingCallback = null, $title = null, $headerStyle = [])
    {
        $export = new static($data, $headings, $mappingCallback, $title, $headerStyle);
        return $export;
    }

    /**
     * Sanea el título de la hoja para que sea válido en Excel
     */
    protected function sanitizeSheetTitle($title)
    {
        // Caracteres no permitidos en títulos de hojas de Excel
        $invalidChars = ['/', '\\', '*', '?', '[', ']', ':'];
        
        // Reemplazar caracteres inválidos con guiones
        $sanitized = str_replace($invalidChars, '-', $title);
        
        // Limitar a 31 caracteres (límite de Excel)
        $sanitized = substr($sanitized, 0, 31);
        
        // Eliminar espacios al inicio y final
        $sanitized = trim($sanitized);
        
        // Si queda vacío, usar un título por defecto
        return empty($sanitized) ? 'Exportacion' : $sanitized;
    }

    /**
     * Convierte un número de columna a letra (1=A, 2=B, 27=AA, etc.)
     */
    protected function getColumnLetter($columnNumber)
    {
        $letter = '';
        while ($columnNumber > 0) {
            $columnNumber--;
            $letter = chr($columnNumber % 26 + 65) . $letter;
            $columnNumber = intval($columnNumber / 26);
        }
        return $letter;
    }
}