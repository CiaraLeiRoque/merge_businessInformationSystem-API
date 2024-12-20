<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $exportTitle }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .business-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .business-logo {
            max-height: 50px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="business-header">
        @if($business->business_image)
            <img src="{{ public_path('storage/' . $business->business_image) }}" alt="Business Logo" class="business-logo">
        @endif
        <h1>{{ $business->business_Name }}</h1>
        <p>{{ $business->business_Address }}</p>
        <p>TIN: {{ $business->business_TIN }}</p>
    </div>

    <h2>{{ $exportTitle }}</h2>

    <table>
        <thead>
            <tr>
                @foreach($visibleColumns as $column)
                    <th>{{ ucfirst(str_replace('product', '', $column)) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                <tr>
                    @foreach($visibleColumns as $column)
                        @php
                            $field = $columnMapping[$column] ?? null;
                        @endphp
                        @if($field)
                            <td>
                                @if($field === 'image' && $product->image)
                                    <img src="{{ public_path('storage/' . $product->image) }}" alt="Product Image" style="max-height: 50px;">
                                @else
                                    {{ $product->$field }}
                                @endif
                            </td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
