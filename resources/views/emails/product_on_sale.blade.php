<!DOCTYPE html>
<html>
<head>
    <title>{{ $product->name }} is on Sale!</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
            line-height: 1.6;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background: #4caf50;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .email-body {
            padding: 20px;
        }
        .email-body img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin: 10px 0;
        }
        .email-body h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        .email-body p {
            margin: 10px 0;
            font-size: 16px;
        }
        .cta-button {
            display: inline-block;
            margin: 20px 0;
            padding: 10px 20px;
            font-size: 16px;
            color: white;
            background-color: #4caf50;
            text-decoration: none;
            border-radius: 4px;
        }
        .cta-button:hover {
            background-color: #45a049;
        }
        .email-footer {
            background: #f4f4f4;
            text-align: center;
            padding: 10px;
            font-size: 12px;
            color: #666;
        }
        .unsubscribe-link {
            color: #4caf50;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>{{ $product->name }} is Now on Sale!</h1>
        </div>
        <div class="email-body">
            <p>Great news! Our featured product, <strong>{{ $product->name }}</strong>, is now available at a discounted price of <strong>₱{{ number_format($product->on_sale_price, 2) }}</strong>.</p>
            
            @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
            @endif
            
            <p>Don't miss out on this limited-time offer! Click the button below to view the product at our page!</p>
            
            <a href="{{ url('/products_page') }}" class="cta-button">Check out our site!</a>
        </div>
        <div class="email-footer">
            <p>If you no longer wish to receive these emails, you can <a href="{{ url('/unsubscribe?email=' . $email) }}" class="unsubscribe-link">unsubscribe here</a>.</p>
            <p>Thank you for choosing our store!</p>
        </div>
    </div>
</body>
</html>
