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
        Schema::create('accreditation_projects', function (Blueprint $table) {
            $table->id();

            // بيانات التخصيص
            $table->date('date_allocation')->nullable()->comment('تاريخ التخصيص');
            $table->integer('budget_number')->nullable()->comment('رقم الموازنة');
            $table->string('organization_name')->nullable()->comment('المؤسسة');
            $table->decimal('total_dollar', 10, 2)->nullable()->comment('الإجمالي بالدولار');
            $table->decimal('allocation', 10, 2)->nullable()->comment('التخصيص');
            $table->string('currency_allocation')->nullable()->comment('عملة التخصيص');
            $table->decimal('currency_allocation_value', 6, 2)->nullable()->comment('قيمة عملة التخصيص');
            $table->decimal('amount', 10, 2)->nullable()->comment('المبلغ بالدولار');
            $table->text('implementation_items')->nullable()->comment('بنود التنفيذ');
            $table->date('date_implementation')->nullable()->comment('تاريخ التنفيذ');
            $table->text('implementation_statement')->nullable()->comment('بيان');
            $table->decimal('amount_received', 10, 2)->nullable()->comment('المبلغ المستلم');
            $table->integer('number_beneficiaries')->nullable()->comment('عدد المستفيدين');

            // بيانات التنفيذ
            $table->date('implementation_date')->nullable()->comment('تاريخ التنفيذ');
            $table->string('month')->nullable()->comment('شهر التنفيذ');
            $table->string('account')->nullable()->comment('الحساب');
            $table->string('affiliate_name')->nullable()->comment('الاسم التابع');
            $table->string('detail')->nullable()->comment('تفصيل');
            $table->decimal('total_ils', 10, 2)->nullable()->comment('الإجمالي ₪');
            $table->string('received')->nullable()->comment('المستلم');
            $table->decimal('amount_payments', 10, 2)->nullable()->comment('المبلغ المدفوع');
            $table->text('payment_mechanism')->nullable()->comment('آلية الدفع');

            // حقول مشتركة
            $table->string('broker_name')->comment('الاسم المختصر');
            $table->string('project_name')->comment('المشروع');
            $table->string('item_name')->nullable()->comment('الصنف');
            $table->integer('quantity')->nullable()->comment('الكمية');
            $table->decimal('price', 10, 2)->nullable()->comment('السعر');
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_name')->nullable()->comment('المستخدم');
            $table->string('manager_name')->nullable()->comment('المدير المستلم');
            $table->json('files')->nullable();

            // حقول خاصة بجدول الإعتماد
            $table->enum('type',['allocation','executive'])->comment('النوع');
            $table->boolean('status')->default(0)->comment('الحالة');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accreditation_projects');
    }
};
