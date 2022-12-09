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
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('idProducts');
            $table->string('productName', 100);
            $table->decimal('standardValue', 19, 2, true)->default(0.00);
            $table->unsignedBigInteger('idUsersCreation', false)->nullable();
            $table->unsignedBigInteger('idUsersLastUpdate', false)->nullable();
            $table->boolean('isActive')->default(1);
            $table->timestamps();

            $table->foreign('idUsersCreation')
                ->references('idUsers')
                ->on('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('idUsersLastUpdate')
                ->references('idUsers')
                ->on('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('products');
    }
};
