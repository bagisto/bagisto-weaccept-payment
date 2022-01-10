<?php

namespace Webkul\WeAccept\Payment;

use Illuminate\Support\Facades\Config;
use Webkul\Payment\Payment\Payment;

/**
 * CashU class
 *
 * @author    Rahul Shukla <rahulshukla,symfony517@webkul.com>
 * @copyright 2018 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
abstract class WeAccept extends Payment
{
    /**
     * CashU web URL generic getter
     *
     * @return string
     */
    public function getWeAcceptUrl()
    {
    }
}