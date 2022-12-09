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
        Schema::create('sales', function (Blueprint $table) {
            $table->bigIncrements('idSales');
            $table->unsignedBigInteger('idClients', false);
            $table->unsignedBigInteger('idSalePoints', false);
            $table->unsignedBigInteger('idUsersCreation', false)->nullable();
            $table->unsignedBigInteger('idUsersLastUpdate', false)->nullable();
            $table->dateTime('deliverDateTime')->nullable();
            $table->set('status', ['ic', 'cl', 'fs'])
                ->default('ic')
                ->comment('ic: in course / cl: canceled / fs: finished');

            $table->timestamps();

            $table->foreign('idClients')
                ->references('idClients')
                ->on('clients')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('idSalePoints')
                ->references('idSalePoints')
                ->on('sale_points')
                ->cascadeOnDelete()
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
        Schema::dropIfExists('sales');
    }
};
