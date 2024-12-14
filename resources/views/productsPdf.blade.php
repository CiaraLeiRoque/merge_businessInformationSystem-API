<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Products Export</title>
    <style>
        .invoice-box {
            max-width: 1300px;
            margin: auto;
            padding: 10px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 16px;
            line-height: 24px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }
        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }
        .invoice-box table td {
            padding: 5px;
            vertical-align: middle;
        }
        .invoice-box table tr.heading td {
            background: #1F2937;
            color: #fff;
            font-weight: bold;
            border: 1px solid #ddd;
        }
        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }
        .invoice-box table tr.item:nth-child(even) {
            background: #f9f9f9;
        }
        .invoice-box table tr.item:nth-child(odd) {
            background: #fff;
        }
    </style>
</head>
<body>
<div class="invoice-box">
    <table>
        <thead>
            <tr class="heading">
                @foreach ($visibleColumns as $column)
                    <th>{{ ucwords(str_replace(['_', 'product'], [' ', ''], $column)) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
                <tr class="item">
                    @foreach ($visibleColumns as $column)
                        <td>
                            {{ $product[$columnMapping[$column] ?? $column] ?? 'N/A' }}
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
</body>
</html>
