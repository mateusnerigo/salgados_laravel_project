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
        Schema::create('clients', function (Blueprint $table) {
            $table->bigIncrements('idClients');
            $table->string('clientName', 150);
            $table->unsignedBigInteger('idSalePoints', false)->nullable();
            $table->unsignedBigInteger('idUsersCreation', false)->nullable();
            $table->unsignedBigInteger('idUsersLastUpdate', false)->nullable();
            $table->boolean('isActive')->default(1);
            $table->timestamps();

            $table->foreign('idSalePoints')
                ->references('idSalePoints')
                ->on('sale_points')
                ->nullOnDelete()
                ->cascadeOnUpdate();

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
        Schema::dropIfExists('clients');
    }
};
