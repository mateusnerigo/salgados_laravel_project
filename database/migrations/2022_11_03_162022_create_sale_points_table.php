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
        Schema::create('sale_points', function (Blueprint $table) {
            $table->bigIncrements('idSalePoints');
            $table->string('salePointName', 150);
            $table->text('description')->nullable();
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
        Schema::dropIfExists('sale_points');
    }
};
