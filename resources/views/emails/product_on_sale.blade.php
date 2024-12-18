<!DOCTYPE html>
<html>
<head>
    <title>Product on Sale</title>
</head>
<body>
    <h1>{{ $product->name }} is now on Sale!</h1>
    <p>Get it for just ${{ number_format($product->on_sale_price, 2) }}!</p>

    @if($product->image)
        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" style="max-width: 300px;">
    @endif

    <p>Don't miss out!</p>
</body>
</html>
