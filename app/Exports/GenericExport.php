<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GenericExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    protected $data;
    protected $headings;
    protected $mappingCallback;
    protected $title;
    protected $headerStyle;

    /**
     * Constructor de la clase GenericExport
     *
     * @param Collection $data - Los datos ya filtrados para exportar
     * @param array $headings - Encabezados de las columnas
     * @param callable|null $mappingCallback - Función para mapear cada fila
     * @param string|null $title - Título de la hoja de Excel
     * @param array $headerStyle - Estilos personalizados para el encabezado
     */
    public function __construct(
        Collection $data, 
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
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4A5568'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ], $headerStyle);
    }

    /**
     * Retorna la colección de datos
     */
    public function collection()
    {
        return $this->data;
    }

    /**
     * Define los encabezados de las columnas
     */
    public function headings(): array
    {
        return $this->headings;
    }

    /**
     * Mapea cada fila de datos
     */
    public function map($row): array
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
     * Define el título de la hoja
     */
    public function title(): string
    {
        return $this->title;
    }

    /**
     * Aplica estilos a la hoja de Excel
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => $this->headerStyle, // Aplicar estilo a la primera fila (encabezados)
        ];
    }

    /**
     * Obtiene valores anidados de objetos/arrays de forma inteligente
     */
    private function getNestedValue($object, $key)
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
}