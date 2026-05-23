<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->decimal('interest_rate', 5, 2);
            $table->enum('interest_type', ['simple', 'compound'])->default('simple');
            $table->enum('payment_modality', ['libre', 'semanal', 'quincenal', 'mensual']);
            $table->date('start_date');
            $table->date('estimated_end_date');
            $table->enum('status', ['active', 'paid', 'late', 'cancelled'])->default('active');
            $table->decimal('balance', 15, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
