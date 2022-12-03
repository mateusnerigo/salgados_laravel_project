<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('sale_items', function (Blueprint $table) {
            $table->integer('idSaleItems')->default(0);
            $table->unsignedBigInteger('idSales', false);
            $table->unsignedBigInteger('idProducts', false);
            $table->decimal('quantity', 19, 2, true)->default(0.00);
            $table->decimal('soldPrice', 19, 2, true)->default(0.00);
            $table->decimal('discountApplied', 19, 2, true)->default(0.00);
            $table->timestamps();

            $table->primary(['idSaleItems', 'idSales']);

            $table->foreign('idSales')
                ->references('idSales')
                ->on('sales')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('idProducts')
                ->references('idProducts')
                ->on('products')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('sale_items');
    }
};
