<?php

namespace App\Modules\Finance\PaymentGateway\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentGatewayConfig extends Model
{
    protected $table = 'payment_gateway_configs';

    protected $fillable = ['provider', 'merchant_id', 'server_key', 'client_key', 'is_production', 'is_active'];

    protected $casts = [
        'is_production' => 'boolean',
        'is_active' => 'boolean',
    ];
}
