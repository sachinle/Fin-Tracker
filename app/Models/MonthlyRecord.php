<?php
// File: app/Models/MonthlyRecord.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyRecord extends Model
{
    use HasFactory;

    protected $table = 'monthly_records';

    /**
     * Mass-assignable fields.
     *
     * IMPORTANT — 'savings' is NEVER entered by the user.
     * It is auto-calculated as:
     *   Savings = Income − Loan/EMI − Expenses − Miscellaneous
     *
     * 'remaining_balance' is always kept equal to 'savings'.
     * It exists for backward compatibility only.
     */
    protected $fillable = [
        'user_id',
        'month',
        'income',
        'loan',
        'expenses',
        'miscellaneous',
        'savings',           // auto-calculated, never user-entered
        'remaining_balance', // always equals savings
    ];

    protected $casts = [
        'income'            => 'decimal:2',
        'loan'              => 'decimal:2',
        'expenses'          => 'decimal:2',
        'miscellaneous'     => 'decimal:2',
        'savings'           => 'decimal:2',
        'remaining_balance' => 'decimal:2',
    ];

    /**
     * Relationship: Monthly record belongs to a User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate net savings — the only correct definition.
     *
     *   Net Savings = Income − Loan/EMI − Expenses − Miscellaneous
     *
     * Can be negative if the user has spent more than they earned.
     */
    public static function calculateSavings(array $data): float
    {
        return (float)$data['income']
             - (float)($data['loan']          ?? 0)
             - (float)($data['expenses']      ?? 0)
             - (float)($data['miscellaneous'] ?? 0);
    }
}