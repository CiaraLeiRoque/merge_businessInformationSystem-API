<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->foreign('business_id')->references('business_id')->on('businesses')->onDelete('cascade');
            $table->unsignedBigInteger('business_id')->default(1);
            $table->enum('on_sale', ['yes','no'])->default('no');
            $table->decimal('on_sale_price', 8, 2)->default(0);
            $table->enum('featured', ['true','false'])->default('false');
            $table->id();
            $table->string('name');
            $table->string('brand');
            $table->decimal('price', 8, 2);
            $table->string('category');
            $table->integer('stock')->default(0);
            $table->integer('sold')->default(0);
            $table->integer('total_stock')->default(0);
            $table->string('status');
            $table->text('description');
            $table->date('expDate');
            $table->string('image')->nullable();
            $table->timestamps();
        });


        Schema::create('product_notification_settings', function (Blueprint $table) {
            $table->id();
            $table->string('stock_expDate')->unique(); // Ensure 'category' is unique
            $table->integer('count')->unique(); // Ensure 'category' is unique
            $table->timestamps();
        });
        DB::table('product_notification_settings')->insert([
            ['stock_expDate' => 'stock', 'count' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['stock_expDate' => 'expDate', 'count' => 7, 'created_at' => now(), 'updated_at' => now()],
        ]);


        Schema::create('product_column_table_visibilities', function (Blueprint $table) {
            $table->id();
            $table->string('column_Table')->unique(); // Ensure 'category' is unique
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
        });
        DB::table('product_column_table_visibilities')->insert([
            ['column_table' => 'productId', 'is_visible' => true, 'created_at' => now(), 'updated_at' => now()],
            ['column_table' => 'productImage', 'is_visible' => true, 'created_at' => now(), 'updated_at' => now()],
            ['column_table' => 'productName', 'is_visible' => true, 'created_at' => now(), 'updated_at' => now()],
            ['column_table' => 'productBrand', 'is_visible' => true, 'created_at' => now(), 'updated_at' => now()],
            ['column_table' => 'productPrice', 'is_visible' => true, 'created_at' => now(), 'updated_at' => now()],
            ['column_table' => 'productCategory', 'is_visible' => true, 'created_at' => now(), 'updated_at' => now()],
            ['column_table' => 'productTotalStock', 'is_visible' => true, 'created_at' => now(), 'updated_at' => now()],
            ['column_table' => 'productStock', 'is_visible' => true, 'created_at' => now(), 'updated_at' => now()],
            ['column_table' => 'productSold', 'is_visible' => true, 'created_at' => now(), 'updated_at' => now()],
            ['column_table' => 'productStatus', 'is_visible' => true, 'created_at' => now(), 'updated_at' => now()],
            ['column_table' => 'productExpiry', 'is_visible' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);


        Schema::create('product_package_names', function (Blueprint $table) {
            //TO STORE MULTIPLE ITEMS IN ONE TABLE 
            $table->id();
            $table->string('image')->nullable();
            $table->string('product_package_name')->nullable(); // name
            $table->string('product_package_description')->nullable(); // names
            $table->timestamps();
        });
        Schema::create('product_packages', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('product_package_id'); // Define the column first

            $table->unsignedBigInteger('product_id'); // Define the product_id column    
            $table->string('product_name')->nullable();
            $table->bigInteger('product_quantity')->nullable();
            $table->timestamps();

            // Define foreign keys
            $table->foreign('product_package_id')->references('id')->on('product_package_names')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });







    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_notification_settings');
        Schema::dropIfExists('product_column_table_visibility');
        Schema::dropIfExists('product_packages');
        Schema::dropIfExists('product_package_names');
    }
};
