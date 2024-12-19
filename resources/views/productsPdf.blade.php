<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
	<title>Products Export - {{ now()->format('Y-m-d H:i:s') }} </title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .invoice-box {
            width: calc(100% - 40px);
            min-height: 100vh;
            margin: 0 auto;
            padding: 20px;
            border: none;
            box-shadow: none;
            background: #fff;
            overflow-x: auto;
            position: relative;
        }
        .table-container {
            width: 100%;
            overflow-x: auto;
        }
        .header {
            position: relative;
            width: 100%;
            margin-bottom: 40px;
            min-height: 120px;
        }
        .header .logo {
            position: absolute;
            left: 0;
            top: 0;
        }
        .header .logo img {
            width: 120px;
            height: 120px;
            object-fit: contain;
        }
        .header .info {
            position: absolute;
            right: 0;
            top: 0;
            text-align: right;
        }
        .header .info p {
            margin: 4px 0;
            font-size: 16px;
        }
        .header .info .name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            clear: both;
        }
        table thead tr {
            background: #1F2937;
            color: #fff;
            text-align: left;
        }
        table thead th {
            padding: 8px;
            border: 1px solid #ddd;
            font-size: 12px;
            text-transform: uppercase;
        }
        table tbody tr {
            border-bottom: 1px solid #ddd;
        }
        table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }
        table tbody tr:nth-child(odd) {
            background: #fff;
        }
        table tbody td {
            padding: 8px;
            vertical-align: middle;
            font-size: 12px;
            text-align: left;
        }
        .image-cell img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .status-listed {
            color: #fff;
            background: #4CAF50;
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 10px;
            text-align: center;
        }
        .footer {
        position: absolute;
        bottom: 10px;
        left: 20px;
        right: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center; /* Ensures vertical alignment */
        font-size: 12px;
        color: #666;
        }
        .footer p {
            margin: 0; /* Removes default margin */
        }
        .footer .left, .footer .right {
            flex: 1;
        }
        .footer .left {
            text-align: left;
        }
        .footer .right {
            text-align: right;
        }
        .page-break {
            page-break-after: always;
            justify-items: center;
        }
        .export-title {
            width: 100%;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin-top: 20px 0;
            margin-bottom: 2px 0;
        }
    </style>
</head>
<body>
@php
    $itemsPerPage = 10; // Adjust this value based on how many items fit comfortably on one page
    $totalPages = ceil(count($products) / $itemsPerPage);
    $currentPage = 1;
@endphp

@for ($page = 1; $page <= $totalPages; $page++)
    <div class="invoice-box">
        <!-- Header -->
        <div class="header">
            <div class="logo">
                <img src="storage/business_logos/{{ $businessImage }}" alt="Business Logo">
            </div>
            <div class="info">
                <p class="name">{{ $businessName }}</p>
                <p>Address: {{ $businessAddress }}</p>
                <p>TIN: {{ $businessTIN ?? 'N/A' }}</p>
            </div>
        </div>
        
        <!-- Export Title -->
        <div class="export-title">{{$exportTitle}}</div>
        
        <!-- Products Table -->
        <div class="table-container">
            <table>
            <thead>
                    <tr>
                        @foreach ($visibleColumns as $column)
                            <th>
                                @if ($column === 'productTotalStock')
                                    Total Quantity
                                @elseif ($column === 'productStock')
                                    Products Unsold
                                @elseif ($column === 'productSold')
                                    Products Sold
                                @elseif ($column === 'image')
                                    <div class>
                                        <img src="{{ $product[$column] }}" alt="Product Image">
                                    </div>
                                @else
                                    {{ ucwords(str_replace(['_', 'product'], [' ', ''], $column)) }}
                                @endif
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products->forPage($page, $itemsPerPage) as $product)
                        <tr>
                            @foreach ($visibleColumns as $column)
                                <td>
                                    @if ($column === 'image')
                                        <div class="image-cell">
                                            <img src="{{ $product[$column] }}" alt="Product Image">
                                        </div>
                                    @elseif ($column === 'status')
                                        <span class="status-{{ strtolower($product[$column]) }}">{{ ucfirst($product[$column]) }}</span>
                                    @else
                                        {{ $product[$columnMapping[$column] ?? $column] ?? 'N/A' }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="left">
                <p>Generated at: {{ now()->format('Y-m-d H:i:s') }} | Created by: {{ $businessName }}</p>
            </div>
            <div style="position:absolute; right:10px; top:1px" class="right">
                <p>Page {{ $page }} of {{ $totalPages }}</p>
            </div>
        </div>

    @if ($page < $totalPages)
        <div class="page-break"></div>
    @endif

    </div>

@endfor
</body>
</html>