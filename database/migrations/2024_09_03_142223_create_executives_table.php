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
        Schema::create('executives', function (Blueprint $table) {
            $table->id();
            $table->date('implementation_date')->comment('تاريخ التنفيذ');
            $table->string('month')->comment('شهر التنفيذ');
            $table->string('broker_name')->comment('الاسم المختصر');
            $table->string('account')->comment('الحساب');
            $table->string('affiliate_name')->comment('الاسم التابع');
            $table->string('project_name')->comment('المشروع');
            $table->string('detail')->nullable()->comment('تفصيل');
            $table->string('item_name')->comment('الصنف');
            $table->integer('quantity')->nullable()->comment('الكمية');
            $table->decimal('price', 10, 2)->nullable()->comment('السعر ₪');
            $table->decimal('total_ils', 10, 2)->nullable()->comment('الإجمالي ₪');
            $table->string('received')->nullable()->comment('المستلم');
            $table->text('notes')->nullable();
            $table->decimal('amount_payments', 10, 2)->nullable()->comment('المبلغ المدفوع');
            $table->text('payment_mechanism')->nullable()->comment('آلية الدفع');
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
        Schema::dropIfExists('executives');
    }
};
