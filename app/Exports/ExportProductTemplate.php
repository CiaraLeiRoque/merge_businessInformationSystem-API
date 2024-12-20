<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportProductTemplate implements WithHeadings, WithStyles
{
    /**
     * Return the query of products.
     */

    /**
     * Map the data to be exported.
     */

    /**
     * Define headings for all columns.
     */
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Description',
            'Brand',
            'Price',
            'Category',
            'Total Stock',
            'Stock',
            'Sold',


            'Status',
            'Expiration Date',
            'Image',

            'Featured',
            'On Sale',
            'On Sale Price',
            
            
            'Business ID',


        ];
    }

    /**
     * Apply styles to the sheet, especially for the header row.
     */
    public function styles(Worksheet $sheet)
    {
        // Apply styles to the first row (header)
        $sheet->getStyle('A1:P1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'], // White text
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF000000'], // Black background
            ],
        ]);

        return [
            // Apply this style only to the first row (header)
            1 => ['font' => ['bold' => true]],
        ];
    }
    public function prepareForValidation($data)
{
    unset($data['created_at'], $data['updated_at']); // Remove unnecessary fields
    return $data;
}
}