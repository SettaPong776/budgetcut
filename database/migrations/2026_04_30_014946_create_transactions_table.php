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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('doc_ref')->comment('เลขที่เอกสาร/บันทึกข้อความ');
            $table->text('description')->comment('รายการจัดซื้อจัดจ้าง');
            $table->decimal('amount', 15, 2)->comment('จำนวนเงินที่เบิก');
            $table->date('transaction_date')->comment('วันที่ทำรายการ');
            $table->enum('status', ['pending', 'approved', 'cancelled'])->default('approved')->comment('สถานะ');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
