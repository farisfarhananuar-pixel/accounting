<?php
// database/migrations/2024_01_01_000010_create_subscription_payments_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('subscription_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('set null');
            $table->string('company_name');
            $table->string('contact_name');
            $table->string('contact_email');
            $table->string('receipt_path')->nullable(); // uploaded receipt image
            $table->decimal('amount', 10, 2)->default(50.00);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // Add payment_verified column to companies table if not exists
        if (!Schema::hasColumn('companies', 'payment_verified')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->boolean('payment_verified')->default(false)->after('subscription_status');
                $table->string('developer_qr_image')->nullable()->after('payment_verified');
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('subscription_payments');
    }
};
