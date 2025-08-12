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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->string('order_number')->unique();
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['pending', 'paid', 'failed', 'cancelled'])->default('pending');
            $table->json('items'); // Productos del pedido
            $table->json('customer_data'); // Datos del cliente al momento de la compra
            $table->string('epayco_ref')->nullable(); // Referencia de ePayco
            $table->string('epayco_transaction_id')->nullable(); // ID de transacción de ePayco
            $table->string('payment_method')->nullable(); // Método de pago usado
            $table->text('notes')->nullable(); // Notas adicionales
            $table->timestamps();

            // Índices
            $table->index(['cliente_id', 'status']);
            $table->index('order_number');
            $table->index('epayco_ref');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};