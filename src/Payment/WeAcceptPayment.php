<?php

namespace Webkul\WeAccept\Payment;

/**
 * CashUPayment payment method class
 *
 * @author    Rahul Shukla <rahulshukla,symfony517@webkul.com>
 * @copyright 2018 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class WeAcceptPayment extends WeAccept
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $code  = 'weaccept';

     /**
     * Return WeAccept redirect url
     *
     * @var string
     */
    public function getRedirectUrl()
    {
        return route('weaccept.payement.redirect');
    }
}