<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class ImportProduct implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * Define the logic to import each row into the Product model.
     */
    public function model(array $row)
    {
        return new Product([
            'id'                     => $row['id'] ?? null,
            'name'                   => $row['name'] ?? null,
            'description'            => $row['description'] ?? null,
            'brand'                  => $row['brand'] ?? null,
            'price'                  => $row['price'] ?? null,
            'category'               => $row['category'] ?? null,
            'total_stock'            => $row['total_stock'] ?? null,
            'stock'                  => $row['stock'] ?? null,
            'sold'                   => $row['sold'] ?? 0,
            'status'                 => $row['status'] ?? null,
            'expDate'                => $this->transformDate($row['expiration_date'] ?? null),
            'image'                  => $row['image'] ?? null,
            'featured'               => $this->transformBoolean($row['featured'] ?? null),
            'on_sale'                => $this->transformOnSale($row['on_sale'] ?? null),
            'on_sale_price'          => $row['on_sale_price'] ?? null,
            'business_id'            => $row['business_id'] ?? null,
            // Exclude 'created_at' and 'updated_at' as they are managed by Eloquent
        ]);
    }

    /**
     * Transform a date value into a Carbon object.
     */
    private function transformDate($value)
    {
        return $value ? Carbon::parse($value)->format('Y-m-d') : null;
    }

    /**
     * Transform 'featured' value to boolean (1 or 0).
     */
    private function transformBoolean($value)
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
    }

    /**
     * Transform 'on_sale' to a valid boolean-like field ('yes' => 1, 'no' => 0).
     */
    private function transformOnSale($value)
    {
        return strtolower(trim($value)) === 'yes' ? 1 : 0;
    }

    /**
     * Prepare data for validation, converting specific fields to appropriate formats.
     */
    public function prepareForValidation($data)
    {
        $data['brand'] = trim((string)$data['brand']);
        $data['description'] = trim((string)$data['description']);
        $data['category'] = trim((string)$data['category']);

        if (isset($data['featured'])) {
            $data['featured'] = strtolower(trim($data['featured']));
        }

        if (isset($data['on_sale'])) {
            $data['on_sale'] = strtolower(trim($data['on_sale']));
        }

        return $data;
    }

    /**
     * Define validation rules.
     */
    public function rules(): array
    {
        return [
            '*.id'                     => 'nullable|integer',
            '*.name'                   => 'nullable|string',
            '*.description'            => 'nullable|string',
            '*.brand'                  => 'nullable|string',
            '*.price'                  => 'nullable|numeric',
            '*.category'               => 'nullable|string',
            '*.total_stock'            => 'nullable|integer',
            '*.stock'                  => 'nullable|integer',
            '*.sold'                   => 'nullable|integer',
            '*.status'                 => 'nullable|string',
            '*.expiration_date'        => 'nullable|numeric',
            '*.image'                  => 'nullable|string',
            '*.featured'               => 'nullable|string',
            '*.on_sale'                => 'nullable|string',
            '*.on_sale_price'          => 'nullable|numeric',
            '*.business_id'            => 'nullable|integer',
        ];
    }

    /**
     * Provide custom messages for validation errors.
     */
    public function customValidationMessages()
    {
        return [
            '*.featured.in'               => 'The featured field must be either "true", "false", "1", or "0".',
            '*.on_sale.in'                => 'The on sale field must be either "yes", "no", "1", or "0".',
        ];
    }
}
