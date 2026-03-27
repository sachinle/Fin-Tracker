<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the monthly_records table in MySQL
     */
    public function up(): void
    {
        Schema::create('monthly_records', function (Blueprint $table) {
            $table->id();                                          // Auto-increment primary key
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();  // Link to users table
            $table->string('month');                               // e.g., "January 2024"
            $table->decimal('income', 12, 2);                     // Total monthly income
            $table->decimal('loan', 12, 2)->default(0);           // Monthly EMI / Loan amount
            $table->decimal('expenses', 12, 2)->default(0);       // General expenses
            $table->decimal('savings', 12, 2)->default(0);        // Savings
            $table->decimal('miscellaneous', 12, 2)->default(0);  // Misc expenses
            $table->decimal('remaining_balance', 12, 2);          // Calculated: income - all deductions
            $table->timestamps();                                  // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_records');
    }
};