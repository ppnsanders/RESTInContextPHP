<?php
require 'config.php';
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\ExecutePayment;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Transaction;

if(isset($_POST['success']) && $_POST['success'] == 'true') {

	$paymentId = $_POST['paymentId'];
	$token = $_POST['token'];
	$payerId = $_POST['PayerID'];

	try {
		$payment = Payment::get($paymentId, $apiContext);
	} catch (Exception $ex) {
		die($ex);
	}

    $execution = new PaymentExecution();
    $execution->setPayerId($payment->payer->payer_info->payer_id);
    $execution->addTransaction($payment->transactions[0]);

    try {

        $result = $payment->execute($execution, $apiContext);

        try {
            $payment = Payment::get($paymentId, $apiContext);
        } catch (Exception $ex) {
            exit($ex);
        }

    } catch (Exception $ex) {
        exit($ex);
    }
}

include 'views/header.php';

if(isset($payment)) {
	echo '<div id="paymentDetails" style="margin-bottom: 30px;" class="col-md-12">
        <div class="row">
            <div class="col-md-12">
            <h3>Your Receipt</h3>
                <hr />
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-striped">
                            <tr><td>Order Number:</td><td>' . $payment->transactions[0]->related_resources[0]->sale->id . '</td></tr>
                            <tr><td>Payment Method:</td><td><img height="50px" width="49px" src="public/images/pp_mark_icon.png" /></td></tr>
                            <tr><td>Total Charged:</td><td><strong>' . $payment->transactions[0]->amount->total . ' ' . $payment->transactions[0]->amount->currency . '</strong></td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <h4>Ship To:</h4>
                                <address>
                                    <strong>' 
                                    . $payment->transactions[0]->item_list->shipping_address->recipient_name . '</strong><br />'
                                    . $payment->transactions[0]->item_list->shipping_address->line1 . '<br />'
                                    . $payment->transactions[0]->item_list->shipping_address->city . ',' 
                                    . $payment->transactions[0]->item_list->shipping_address->state . ' '
                                    . $payment->transactions[0]->item_list->shipping_address->postal_code . '<br /><abbr title="Phone">P:</abbr> '
                                    . $payment->payer->payer_info->phone .
                                '</address>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h3>Order Details</h3>
                        <hr />
                        <div class="panel panel-default">
                          <div class="panel-body">
                            <table class="table table-striped">
                                <tr><th>Item Name</th><th>Item Desc.</th><th>Quantity</th><th>Price Each</th></tr>';
                                    foreach ($payment->transactions[0]->item_list->items as $value) {
                                        echo "<tr><td>" . $value->name . "</td><td>" . $value->description . "</td><td>" . $value->quantity . "</td><td>" . $value->price . "</td></tr>";
                                    }
                                echo '<tr><td colspan="2"></td><td><strong>Subtotal:</strong></td><td>' 
                                . $payment->transactions[0]->amount->details->subtotal . '</td></tr><tr><td colspan="2"></td><td><strong>Shipping:</strong></td><td>'
                                . $payment->transactions[0]->amount->details->shipping . '</td></tr><tr><td colspan="2"></td><td><strong>Total:</strong></td><td><strong>'
                                . $payment->transactions[0]->amount->total . ' ' . $payment->transactions[0]->amount->currency . '</strong></td></tr>
                            </table>
                          </div>
                        </div>
                    </div>
                </div>
            </div>
		</div>
	</div>';
} else {
	echo "<h3>An Error occured..</h3>";
}

include 'views/footer.php';
?>