<?php

namespace Webkul\WeAccept\Http\Controllers;

use Illuminate\Http\Request;
use Webkul\Checkout\Facades\Cart;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Repositories\InvoiceRepository;
use Webkul\Sales\Repositories\OrderItemRepository;
use Webkul\Sales\Repositories\RefundRepository;
use Webkul\WeAccept\Repositories\WeAcceptRepository;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Redirect;

/**
 * WeAccept controller
 *
 * @author    Rahul Shukla <rahulshukla,symfony517@webkul.com>
 * @copyright 2018 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class WeAcceptController extends Controller
{

     /**
     * Display a listing of the resource.
     *
     * @var array
     */
    protected $_config;


    /**
     * OrderRepository object
     *
     * @var array
     */
    protected $orderRepository;

    /**
     * InvoiceRepository object
     *
     * @var object
     */
    protected $invoiceRepository;

    /**
     * OrderItemRepository object
     *
     * @var \Webkul\Sales\Repositories\OrderItemRepository
     */
    protected $orderItemRepository;

    /**
     * RefundRepository object
     *
     * @var \Webkul\Sales\Repositories\RefundRepository
     */
    protected $refundRepository;

      /**
     * WeAcceptRepository object
     *
     * @var \Webkul\WeAccept\Repositories\WeAcceptRepository
     */
    protected $weAcceptRepository;

    /**
     * GuzzleHttp object
     *
     * @var object
     */
    protected $client;

    /**
     * userName for WeAccept
     *
     */
    protected $userName = null;

    /**
     * password for WeAccept
     *
     */
    protected $password = null;

    /**
     * merchentId for WeAccept
     *
     */
    protected $merchentId = null;

    /**
     * iFrameId for WeAccept
     *
     */
    protected $iFrameId = null;

    /**
     * integrationId for WeAccept
     *
     */
    protected $integrationId = null;

    /**
     * hmac for WeAccept
     *
     */
    protected $hmac = null;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Attribute\Repositories\OrderRepository  $orderRepository
     * @param  \Webkul\Sales\Repositories\InvoiceRepository $invoiceRepository
     * @param  Webkul\Sales\Repositories\OrderItemRepository  $orderItemRepository
     * @param  Webkul\Sales\Repositories\RefundRepository  $refundRepository
     * 
    */

    public function __construct(
        OrderRepository $orderRepository,
        InvoiceRepository $invoiceRepository,
        OrderItemRepository $orderItemRepository,
        RefundRepository $refundRepository,
        WeAcceptRepository $weAcceptRepository
    ) {
        $this->orderRepository   = $orderRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->refundRepository = $refundRepository;
        $this->weAcceptRepository = $weAcceptRepository;
        $this->merchentId        = core()->getConfigData('sales.paymentmethods.weaccept.merchant_id');
        $this->apiKey            = core()->getConfigData('sales.paymentmethods.weaccept.api_key');
        $this->iFrameId          = core()->getConfigData('sales.paymentmethods.weaccept.iframe_id');
        $this->integrationId     = core()->getConfigData('sales.paymentmethods.weaccept.integration_id');
        $this->hmac              = core()->getConfigData('sales.paymentmethods.weaccept.hmac_secret');

        $this->client = new Client([
            'headers' => ['Content-Type' => 'application/json']
        ]);


        $this->_config = request('_config');
    }

    /**
     * Redirects to the paypal.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirect()
    {
        try {
            $token = $this->authentication();
            
            if ($token) {
                $order = $this->orderRegister($token);
                
                $paymentKey = $this->paymentkey($token, $order);
                
                $iFrameId = $this->iFrameId;

                $url = sprintf('https://accept.paymobsolutions.com/api/acceptance/iframes/'.''.$iFrameId.'?payment_token='.''.$paymentKey['token']);

                return \Redirect::to($url);
            }
        } catch (\Exception $e) {
            
            $error = explode(PHP_EOL, preg_replace("/[^a-zA-Z0-9\s]/", "", $e->getMessage()));
            $errorMsg = current($error).'.'.next($error);

            session()->flash('error', trans($errorMsg));

            return redirect()->back();
        }
    }

    /**
     * Callback payment
     *
     * @return \Illuminate\Http\Response
     */
    public function callback() {
        $data = request()->all();
        ksort($data);

        $key = ['amount_cents', 'created_at', 'currency', 'error_occured', 'has_parent_transaction', 'id', 'integration_id', 'is_3d_secure', 'is_auth', 'is_capture', 'is_refunded', 'is_standalone_payment', 'is_voided', 'order', 'owner', 'pending', 'source_data_pan', 'source_data_sub_type', 'source_data_type', 'success'];

        foreach ($key as $value) {
            $hmacData[] = $data[$value];
        }

        $generatedHmac = implode("", $hmacData);
        $hmac = hash_hmac('sha512', $generatedHmac, $this->hmac);

        if ($data['success'] == 'true') {
            if ($hmac == $data['hmac']) {
               
                $order = $this->orderRepository->create(Cart::prepareDataForOrder());

                $params = [
                    'transaction_id' => $data['id'],
                    'order_id' => $order->id,
                    'amount' => $data['amount_cents'],
                    'currency' => $data['currency']
                ];

                $this->weAcceptRepository->create($params);

                Cart::deActivateCart();

                $this->orderRepository->update(['status' => 'processing'], $order->id);

                if ($order->canInvoice()) {
                    $this->invoiceRepository->create($this->prepareInvoiceData($order));
                }

                session()->flash('order', $order);

                return redirect()->route('shop.checkout.success');
            } else {
                session()->flash('error', trans('weaccept::app.error.failure-message'));
            }
        } else {
            $errorCode = [1,2,3,4,5,6];

            if (in_array($data['txn_response_code'], $errorCode)) {
                $errorString = 'weaccept::app.error'.'.'.$data['txn_response_code'];
            } else {
                $errorString = 'weaccept::app.error.general';
            }

            session()->flash('error', trans($errorString));

            return redirect()->route('shop.checkout.onepage.index');
        }
    }

    /**
     * Request to Accept's authentication API to obtain authentication token.
     *
     * @return \Illuminate\Http\Response
     */
    protected function authentication()
    {
        $apiKey = json_encode(array(
            "api_key" => $this->apiKey
        ));

        $response = $this->client->post('https://accept.paymobsolutions.com/api/auth/tokens', ['body' => $apiKey]);

        $response = json_decode($response->getBody(), true);

        return $response;
    }


    /**
     * Register an order on Accept.
     *
     * array token
     * @return \Illuminate\Http\Response
     */
    protected function orderRegister($token)
    {
        $address = $this->getCartAddresses();
        $cart = Cart::getCart();

        $orderData = json_encode(array(
            "auth_token" => $token['token'],
            "delivery_needed" => "false",
            "merchant_id" => $this->merchentId,
            "amount_cents" => $cart->base_grand_total * 100,
            "currency"=>  $cart->cart_currency_code,
            "items" => [],
            "shipping_data" => $address["shipping"]
        ));

        $response = $this->client->post('https://accept.paymobsolutions.com/api/ecommerce/orders', ['body' => $orderData]);
        
        $response = json_decode($response->getBody(), true);
        
        return $response;
    }

     /**
     * get Payment Key
     *
     * array token, order
     * @return \Illuminate\Http\Response
     */
    protected function paymentkey($token, $order)
    {
        $address = $this->getCartAddresses();
        $cart = Cart::getCart();

        $keyData = json_encode(array(
            "auth_token" => $token['token'],
            "amount_cents" => $cart->base_grand_total * 100,
            "expiration" => 3600,
            "order_id" => $order['id'] ,
            "billing_data" => $address["billing"],
            "currency" => $cart->cart_currency_code,
            "integration_id" =>  $this->integrationId,
            "lock_order_when_paid" => "false"
        ));

        $response = $this->client->post
        ('https://accept.paymobsolutions.com/api/acceptance/payment_keys', ['body' => $keyData]);

        $response = json_decode($response->getBody(), true);

        return $response;
    }


    /**
     * Shipping Data of order.
     *
     * @return \Illuminate\Http\Response
     */
    protected function getCartAddresses() {
        $cart = Cart::getCart();

        if($cart->shipping_address) {
                $data['shipping'] = [
                    "first_name" => $cart->shipping_address->first_name,
                    "phone_number" => $cart->shipping_address->phone,
                    "last_name" => $cart->shipping_address->last_name,
                    "email" => $cart->shipping_address->email,
                    "street" => $cart->shipping_address->address1,
                    "postal_code" => $cart->shipping_address->postcode,
                    "country" => $cart->shipping_address->country,
                    "city" => $cart->shipping_address->city,
                    "state" => $cart->shipping_address->state
                ];
        } else {
                $data['shipping'] = [
                    "first_name" => $cart->billing_address->first_name,
                    "phone_number" => $cart->billing_address->phone,
                    "last_name" => $cart->billing_address->last_name,
                    "email" => $cart->billing_address->email,
                    "street" => $cart->billing_address->address1,
                    "postal_code" => $cart->billing_address->postcode,
                    "country" => $cart->billing_address->country,
                    "city" => $cart->billing_address->city,
                    "state" => $cart->billing_address->state
                ];
        }

        $data['billing'] = [
            "first_name" => $cart->billing_address->first_name,
            "phone_number" => $cart->billing_address->phone,
            "last_name" => $cart->billing_address->last_name,
            "email" => $cart->billing_address->email,
            "street" => $cart->billing_address->address1,
            "postal_code" => $cart->billing_address->postcode,
            "country" => $cart->billing_address->country,
            "city" => $cart->billing_address->city,
            "state" => $cart->billing_address->state,
            "apartment" => "803",
            "floor" => "42",
            "building" => "8028",
        ];

        return $data;
    }

    /**
     * Prepares order's invoice data for creation
     *
     *
     * @return array
     */
    protected function prepareInvoiceData($order)
    {
        $invoiceData = [
            "order_id" => $order->id
        ];

        foreach ($order->items as $item) {
            $invoiceData['invoice']['items'][$item->id] = $item->qty_to_invoice;
        }

        return $invoiceData;
    }



     /**
     * Refund Create Form
     *
     *
     * @return array
     */
    public function createRefund($orderId)
    {
        $order = $this->orderRepository->findOrFail($orderId);

        return view($this->_config['view'], compact('order'));
    }

     /**
     * Refund store
     *
     *
     * @return array
     */
    public function storeRefund($orderId)
    {
        
        $order = $this->orderRepository->findOrFail($orderId);    

        if (! $order->canRefund()) {
            session()->flash('error', trans('admin::app.sales.refunds.creation-error'));

            return redirect()->back();
        }

        $this->validate(request(), [
            'refund.items.*' => 'required|numeric|min:0',
        ]);

        $data = request()->all();

        $totals = $this->refundRepository->getOrderItemsRefundSummary($data['refund']['items'], $orderId);

        $maxRefundAmount = $totals['grand_total']['price'] - $order->refunds()->sum('base_adjustment_refund');

        $refundAmount = $totals['grand_total']['price'] - $totals['shipping']['price'] + $data['refund']['shipping'] + $data['refund']['adjustment_refund'] - $data['refund']['adjustment_fee'];
        
        $transaction_history = $this->weAcceptRepository->where('order_id',$orderId)->get();
        
        foreach($transaction_history as $value) {
            $transaction = $value;
        }

        if (! $refundAmount) {
            session()->flash('error', trans('admin::app.sales.refunds.invalid-refund-amount-error'));
    
            return redirect()->back();
        }
    
        if ($refundAmount > $maxRefundAmount) {
            session()->flash('error', trans('admin::app.sales.refunds.refund-limit-error', ['amount' => core()->formatBasePrice($maxRefundAmount)]));
    
            return redirect()->back();
        }

        try {

            $token = $this->authentication();

            $keyData = json_encode(array(
                "auth_token" => $token['token'],
                "transaction_id" => $transaction->transaction_id,
                "amount_cents" => $refundAmount * 100
            ));

            $result = $this->client->post
            ('https://accept.paymob.com/api/acceptance/void_refund/refund', ['body' => $keyData]);
    
            $result = json_decode($result->getBody(), true);

        } catch (Exception $e) {
            
            session()->flash('error', $e);
            return redirect()->back();
        }
        
        if (!$result) {
            session()->flash('error', trans('admin::app.sales.refunds.creation-error'));
            return redirect()->back();
        }
    
        $this->refundRepository->create(array_merge($data, ['order_id' => $orderId]));
    
        session()->flash('success', trans('admin::app.response.create-success', ['name' => 'Refund']));
    
        return redirect()->route($this->_config['redirect'], $orderId);
     
         
    }

   

}