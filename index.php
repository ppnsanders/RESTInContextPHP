<?php
require 'config.php';
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;

if(isset($_POST['checkout']) && $_POST['checkout'] == 'now') {

    $payer = new Payer();
    $payer->setPaymentMethod("paypal");
    $item1 = new Item();
    $item1->setName('Ground Coffee 40 oz')
        ->setCurrency('USD')
        ->setQuantity(1)
        ->setSku("123123") // Similar to `item_number` in Classic API
        ->setPrice(7.5);
    $item2 = new Item();
    $item2->setName('Granola bars')
        ->setCurrency('USD')
        ->setQuantity(5)
        ->setSku("321321") // Similar to `item_number` in Classic API
        ->setPrice(2);
    $itemList = new ItemList();
    $itemList->setItems(array($item1, $item2));
    $details = new Details();
    $details->setShipping(1.2)
        ->setTax(1.3)
        ->setSubtotal(17.50);
    $amount = new Amount();
    $amount->setCurrency("USD")
        ->setTotal(20)
        ->setDetails($details);
    $transaction = new Transaction();
    $transaction->setAmount($amount)
        ->setItemList($itemList)
        ->setDescription("Payment description")
        ->setInvoiceNumber(uniqid());
    $redirectUrls = new RedirectUrls();
    $redirectUrls->setReturnUrl("$baseUrl/return.php?success=true")
        ->setCancelUrl("$baseUrl/return.php?success=false");
    $payment = new Payment();
    $payment->setIntent("sale")
        ->setPayer($payer)
        ->setRedirectUrls($redirectUrls)
        ->setTransactions(array($transaction));
    $request = clone $payment;


    try {
        $payment->create($apiContext);
    } catch (Exception $ex) {
        exit($ex);
    }

    $approvalUrl = $payment->getApprovalLink();
    $urlParts = explode('&', $approvalUrl);
    $token = $urlParts[1];
    $paypalUrl = 'https://www.sandbox.paypal.com/checkoutnow?' . $token;
    header("Location: $paypalUrl");
} else {

include 'views/header.php';

echo '
    <form id="myContainer" method="POST" action="' . $baseUrl . '/index.php">
        <input type="hidden" name="checkout" value="now" />
    </form>

    <script>
        window.paypalCheckoutReady = function () {
            paypal.checkout.setup("' . $merchantId . '", {
                environment: "sandbox",
                container: "myContainer"
            });
        };
    </script>

    <script src="//www.paypalobjects.com/api/checkout.js" async></script>';

include 'views/footer.php';
}
?>