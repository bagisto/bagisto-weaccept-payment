<?php

namespace Webkul\WeAccept\Models;


use Illuminate\Database\Eloquent\Model;
use Webkul\WeAccept\Contracts\WeAccept as WeAcceptContract;

class WeAccept extends Model implements WeAcceptContract
{
    protected $table = 'weaccept_transaction_history';
    protected $fillable = [
        'transaction_id',
        'order_id',
        'amount',
        'currency_code' 
    ];
   
         
}