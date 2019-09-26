<?php
require_once __DIR__ . '/settings.php';
require_once __DIR__ . '/classes/PayPal.php';
require_once __DIR__ . '/classes/DBC.php';

if (isset($_POST['startPayment']))
{
    $payment = new PayPalPayment($paypal);
    $payment->setPaymentMethod('paypal'); // Payment methods: https://www.paypal.com/us/webapps/mpp/popup/about-payment-methods
    $payment->setTransactionDescription('Modified Description'); // Payment Description
    $payment->addItem('test', 'HUF', '1', '200'); // Name, Currency, Quantity, Price
    $payment->addItem('test2', 'HUF', '1', '450'); // Name, Currency, Quantity, Price
    $payment->setDetails('1000', '500'); // First shipping, second handling price.
    $payment->setAmount('HUF'); // Currency codes: https://developer.paypal.com/docs/classic/api/currency_codes/
    $payment->setRedirectURLs('http://localhost/PayPal/redirect.php?approved=true', 'http://localhost/PayPal/redirect.php?approved=false'); // Approved parameter is mandatory
    $payment->makeTransaction();
    $payment->makePayment();

    $data = json_decode($payment->startPayment());
    if ($data->status == 'success')
    {
        // Save paymentid to database
		$dbc->set("INSERT INTO transactions (payment_id, complete) VALUES (?, 0)", [$data->paymentId]);
		
		// Navigate to payment page
        echo $data->redirectUrl;
        header("Location: " . $data->redirectUrl);
    }
    else
    {
        echo $data->message;
    }
}
?><!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>PayPal Payment</title>
    </head>

    <body>
        <form method="post">
            <input type="submit" value="Start Payment" name="startPayment">
        </form>
    </body>
</html>