<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\ProductColumnTableVisibility;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportProduct implements FromQuery, WithHeadings, WithStyles, WithMapping
{
    private $visibleColumns;
    private $columnMapping;

    public function __construct()
    {
        // Fetch the visible columns
        $this->visibleColumns = ProductColumnTableVisibility::where('is_visible', true)
            ->pluck('column_Table')
            ->toArray();

        // Define a mapping of column_table to actual database fields
        $this->columnMapping = [
            'productId' => 'id',
            'productImage' => 'image',
            'productName' => 'name',
            'productBrand' => 'brand',
            'productPrice' => 'price',
            'productCategory' => 'category',
            'productTotalStock' => 'total_stock',
            'productStock' => 'stock',
            'productSold' => 'sold',
            'productStatus' => 'status',
            'productExpiry' => 'expDate',
        ];
    }

    /**
     * Return the query of products.
     */
    public function query()
    {
        return Product::query();
    }

    /**
     * Map the data to be exported based on visibility settings.
     */
    public function map($product): array
    {
        $data = [];

        foreach ($this->visibleColumns as $column) {
            // Fetch the actual column name from the mapping
            $field = $this->columnMapping[$column] ?? null;

            // Add the value to the export data
            $data[] = $field ? $product->{$field} : null;
        }

        return $data;
    }

    /**
     * Define headings for the visible columns.
     */
    public function headings(): array
    {
        return array_map(function ($column) {
            return ucwords(str_replace(['_', 'product'], [' ', ''], $column));
        }, $this->visibleColumns);
    }

    /**
     * Apply styles to the sheet, especially for the header row.
     */
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:Z1')->applyFromArray([
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
            1 => ['font' => ['bold' => true]],
        ];
    }
}