<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Str;

class Payment extends Model
{
    use HasFactory;

    public const STATUS_PENDING  = 'pending';
    public const STATUS_PAID     = 'paid';
    public const STATUS_REFUNDED = 'refunded';

    protected $fillable = [
        'appointment_id',
        'patient_id',
        'amount',
        'service_fee',
        'vat_amount',
        'total_paid',
        'method',
        'transaction_id',
        'gateway_response',
        'status',
        'refund_amount',
        'refund_reason',
        'paid_at',
        'refunded_at',
    ];

    protected function casts(): array
    {
        return [
            'amount'           => 'decimal:2',
            'service_fee'      => 'decimal:2',
            'vat_amount'       => 'decimal:2',
            'total_paid'       => 'decimal:2',
            'gateway_response' => 'array',
            'refund_amount'    => 'decimal:2',
            'paid_at'          => 'datetime',
            'refunded_at'      => 'datetime',
        ];
    }

    /* -------------------------------------------------------------------------- */
    /*                                Relationships                               */
    /* -------------------------------------------------------------------------- */

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /* -------------------------------------------------------------------------- */
    /*                               Helper Methods                               */
    /* -------------------------------------------------------------------------- */

    /**
     * Your existing dynamic receipt number (e.g. RCPT-000042)
     */
    public function getReceiptNumberAttribute(): string
    {
        if (isset($this->attributes['receipt_number']) && !empty($this->attributes['receipt_number'])) {
            return $this->attributes['receipt_number'];
        }

        return 'RCPT-' . str_pad((string) $this->id, 6, '0', STR_PAD_LEFT);
    }

    public static function generateTransactionId(): string
    {
        return 'TXN-' . date('Ymd') . '-' . strtoupper(Str::random(6));
    }

    public function paymentMethodLabel(): string
    {
        $method = strtolower($this->method ?? '');
        return match ($method) {
            'bkash'           => 'bKash Mobile Banking',
            'nagad'           => 'Nagad Mobile Banking',
            'rocket'          => 'Rocket Mobile Banking',
            'card'            => 'Credit / Debit Card',
            'wallet'          => 'Digital Health Wallet',
            'sslcommerz'      => 'SSLCommerz Gateway',
            'internetbanking' => 'Internet Banking',
            default           => ucfirst($this->method ?? 'Digital Payment'),
        };
    }
}