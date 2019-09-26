<?php
require_once __DIR__ . '/settings.php';
require_once __DIR__ . '/classes/PayPal.php';
require_once __DIR__ . '/classes/DBC.php';

if (isset($_GET['approved']))
{
    $approved = $_GET['approved'] === 'true';

    if ($approved)
    {
        if (isset($_GET['paymentId']))
        {
            $payment = new PayPalExecution($paypal);

            $data1 = json_decode($payment->getPayment($_GET['paymentId']));
            if ($data1->status == 'success')
            {
                $payment->setPayerID($_GET['PayerID']);

                $data2 = json_decode($payment->execute());
                if ($data2->status == 'success')
					// Database set completed to 1
                    $dbc->set("UPDATE transactions SET completed = 1 WHERE payment_id = ?", [$_GET['paymentId']]);
					
					// Custom Methods
					
                    echo $data2->message;
                else
                    echo $data2->message;
            }
            else
                echo 'Invalid paymentid';
            }
        else
            echo 'Paymentid does not exist';
    }
    else
        echo 'Payment cancelled.';
}
else
    echo 'Approved does not exist';
?>