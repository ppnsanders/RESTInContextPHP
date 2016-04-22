<?php
require 'config.php';
use PayPal\Api\Payment;

if(isset($_GET['success']) && $_GET['success'] == 'true') {

	$paymentId = $_GET['paymentId'];
	$token = $_GET['token'];
	$payerId = $_GET['PayerID'];

	try {
		$payment = Payment::get($paymentId, $apiContext);
	} catch (Exception $ex) {
		die($ex);
	}
}

include 'views/header.php';

if(isset($payment)) {
	echo '<div id="paymentDetails" style="margin-bottom: 30px;" class="col-md-12">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6">
                        <h3>Your Shipping Address</h3>
                        <hr />
                        <div class="panel panel-default">
                          <div class="panel-body">
                            <address>
                              <strong>' 
                              . $payment->transactions[0]->item_list->shipping_address->recipient_name . '</strong><br />'
                              . $payment->transactions[0]->item_list->shipping_address->line1 . '<br />'
                              . $payment->transactions[0]->item_list->shipping_address->city . ',' 
                              . $payment->transactions[0]->item_list->shipping_address->state . ' '
                              . $payment->transactions[0]->item_list->shipping_address->postal_code . '<br /><abbr title="Phone">P:</abbr>'
                              . $payment->payer->payer_info->phone .
                            '</address>
                          </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h3>Your Payment Details</h3>
                        <hr />
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <table class="table">
                                    <tr><td><strong>Email:</strong></td><td>' . $payment->payer->payer_info->email . '</td></tr>
                                    <tr><td><strong>Payment Method:</strong></td><td><img height="50px" width="49px" src="public/images/pp_mark_icon.png" /></td></tr>
                                </table>
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
                <div class="row">
                    <div class="col-md-12">
                    	<form action="' . $baseUrl . '/confirm.php" method="POST">
                    		<input type="hidden" name="success" value="true" />
                    		<input type="hidden" name="paymentId" value="' . $paymentId . '" />
                    		<input type="hidden" name="token" value="' . $token . '" />
                    		<input type="hidden" name="PayerID" value="' . $payerId . '" />
                    		<button type="submit" class="btn btn-primary btn-lg btn-block pull-right">Confirm Order</button>
                    	</form>
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