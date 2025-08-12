<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'cliente_id',
        'order_number',
        'total_amount',
        'status',
        'items',
        'customer_data',
        'epayco_ref',
        'epayco_transaction_id',
        'payment_method',
        'notes'
    ];

    protected $casts = [
        'items' => 'array',
        'customer_data' => 'array',
        'total_amount' => 'decimal:2'
    ];

    // Estados de pedido
    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Relación con cliente
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Generar número de pedido único
     */
    public static function generateOrderNumber()
    {
        $prefix = 'ORD-';
        $timestamp = now()->format('YmdHis');
        $random = str_pad(random_int(0, 999), 3, '0', STR_PAD_LEFT);
        
        return $prefix . $timestamp . '-' . $random;
    }

    /**
     * Obtener el estado formateado
     */
    public function getFormattedStatusAttribute()
    {
        $statuses = [
            self::STATUS_PENDING => 'Pendiente',
            self::STATUS_PAID => 'Pagado',
            self::STATUS_FAILED => 'Fallido',
            self::STATUS_CANCELLED => 'Cancelado'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Verificar si el pedido está pagado
     */
    public function isPaid()
    {
        return $this->status === self::STATUS_PAID;
    }

    /**
     * Verificar si el pedido está pendiente
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }
}