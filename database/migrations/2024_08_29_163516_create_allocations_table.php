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
        Schema::create('allocations', function (Blueprint $table) {
            $table->id();
            $table->date('date_allocation')->comment('تاريخ التخصيص');
            $table->integer('budget_number')->comment('رقم الموازنة');
            $table->string('broker_name')->comment('الاسم المختصر');
            $table->string('organization_name')->comment('المؤسسة');
            $table->string('project_name')->comment('المشروع');
            $table->string('item_name')->nullable()->comment('الصنف');
            $table->integer('quantity')->nullable()->comment('الكمية');
            $table->decimal('price', 10, 2)->nullable()->comment('السعر');
            $table->decimal('total_dollar', 10, 2)->nullable()->comment('الإجمالي بالدولار');
            $table->decimal('allocation', 10, 2)->comment('التخصيص');
            $table->string('currency_allocation')->comment('عملة التخصيص');
            $table->decimal('currency_allocation_value', 6, 2)->comment('قيمة عملة التخصيص');
            $table->decimal('amount', 10, 2)->comment('المبلغ بالدولار');
            $table->text('implementation_items')->nullable()->comment('بنود التنفيذ');
            $table->date('date_implementation')->nullable()->comment('تاريخ التنفيذ');
            $table->text('implementation_statement')->nullable()->comment('بيان');
            $table->decimal('amount_received', 10, 2)->nullable()->comment('المبلغ المستلم');            
            $table->text('notes')->nullable();
            $table->integer('number_beneficiaries')->nullable()->comment('عدد المستفيدين');
            $table->integer('arrest_receipt_number')->nullable()->comment('رقم ايصال القبض');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_name')->nullable()->comment('المستخدم');
            $table->string('manager_name')->nullable()->comment('المدير المستلم');
            $table->json('files')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allocations');
    }
};
