<?php
use PayPal\Api\Payer;
use PayPal\Api\Details;
use PayPal\Api\Amount;
use PayPal\Api\Transaction;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\PaymentExecution;
use PayPal\Exception\PayPalConnectionException;

class PayPalPayment
{
    public function __construct($paypalApi)
    {
        $this->api = $paypalApi;

        $this->payer = new Payer();
        $this->details = new Details();
        $this->amount = new Amount();
        $this->itemList = new ItemList();
        $this->transaction = new Transaction();
        $this->payment = new Payment();
        $this->redirectUrls = new RedirectUrls();

        $this->transactionDescription = 'Default Transaction Description';
        $this->totalPrice = 0;
    }

    public function setPaymentMethod($method)
    {
        $this->payer->setPaymentMethod($method);
    }

    public function setTransactionDescription($description)
    {
        $this->transactionDescription = $description;
    }

    public function addItem($name, $currency, $quantity, $price)
    {
        $item = new Item();
        $item->setName($name)
            ->setCurrency($currency)
            ->setQuantity($quantity)
            ->setPrice($price);
        $this->itemList->addItem($item);
        $this->totalPrice = $this->totalPrice += $price;
    }

    public function setDetails($shippingPrice, $fee = 0)
    {
		$this->totalPrice = $this->totalPrice += $shippingPrice += $fee;
        $this->details->setShipping($shippingPrice)
			->setHandlingFee($fee)
            ->setSubtotal($this->totalPrice);
    }

    public function setAmount($currency)
    {
        $this->amount->setCurrency($currency)
            ->setTotal($this->totalPrice)
            ->setDetails($this->details);
    }

    public function setRedirectURLs($returnUrl, $cancelUrl)
    {
        $this->redirectUrls->setReturnUrl($returnUrl)
            ->setCancelUrl($cancelUrl);
    }

    public function makeTransaction()
    {
        $this->transaction->setAmount($this->amount)
            ->setItemList($this->itemList)
            ->setDescription($this->transactionDescription);
    }

    public function makePayment()
    {
        $this->payment->setIntent('sale')
            ->setPayer($this->payer)
            ->setTransactions([$this->transaction])
            ->setRedirectUrls($this->redirectUrls);
    }

    public function startPayment()
    {
        try {
            $this->payment->create($this->api);
        } catch (PayPalConnectionException $e) {
            return json_encode(array(
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => $e->getData()
            ));
        }

        foreach($this->payment->getLinks() as $link)
        {
            if($link->getRel() == "approval_url")
            {
                $redirectUrl = $link->getHref();
            }
        }

        return json_encode(array(
            'status' => 'success',
            'paymentId' => $this->payment->getId(),
            'redirectUrl' => $redirectUrl
        ));
    }
}

class PayPalExecution
{
    public function __construct($paypalApi)
    {
        $this->api = $paypalApi;

        $this->execution = new PaymentExecution();
    }

    public function getPayment($paymentId)
    {
        try {
            $this->payment = Payment::get($paymentId, $this->api);
        } catch (PayPalConnectionException $e) {
            return json_encode(array(
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => $e->getData()
            ));
        }

        return json_encode(array(
            'status' => 'success'
        ));
    }

    public function setPayerID($payerID)
    {
        $this->execution->setPayerId($payerID);
    }

    public function execute()
    {
        try {
            $this->payment->execute($this->execution, $this->api);
        } catch (PayPalConnectionException $e) {
            return json_encode(array(
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => $e->getData()
            ));
        }

        return json_encode(array(
            'status' => 'success',
            'message' => 'Successfully payment'
        ));
    }
}
?>