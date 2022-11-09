<?php
include 'includes/globals.php';

require_once('vendor/autoload.php');

use GlobalPayments\Api\PaymentMethods\CreditCardData;
use GlobalPayments\Api\ServicesContainer;
use GlobalPayments\Api\Entities\Address;
use GlobalPayments\Api\Entities\Customer;
use GlobalPayments\Api\ServiceConfigs\Gateways\PorticoConfig;

$formData = [];
foreach ($_POST as $key => $value) {
    $formData[$key] = htmlspecialchars($value);
}
// Provide default date values
$default_array = array();
$default_array['schedFrequency'] = 'n/a';
$default_array['schedStartDate'] = '0000-00-00';
$default_array['schedEndDate'] = '0000-00-00';
$default_array['schedNumPayments'] = 0;
$default_array['society'] = 'default';

foreach( $default_array as $key => $val ) {
    if( empty($formData[$key]) ) {
        $formData[$key] = $val;
    }
}

$config = new PorticoConfig();
$config->secretApiKey = "$gHeartlandSecretKey";
ServicesContainer::configureService($config);

$card = new CreditCardData();
$card->token = $formData['token_value'];
$invoiceNumber = htmlspecialchars($formData['invoice_number']);

$customer = new Customer();
$customer->firstName = htmlspecialchars($formData['FirstName']);
$customer->lastName = htmlspecialchars($formData['LastName']);
$customer->homePhone = htmlspecialchars($formData['PhoneNumber']);
$customer->email = htmlspecialchars($formData['Email']);

$address = new Address();
$address->streetAddress1 = htmlspecialchars($formData['Address']);
$address->city = htmlspecialchars($formData['City']);
$address->state = htmlspecialchars($formData['State']);
$address->postalCode = preg_replace('/[^0-9]/', '', htmlspecialchars($formData['Zip']));
$address->country = "United States";

$amount = htmlspecialchars($formData['payment_amount'] );
//->withAllowDuplicates(true)

try {
    $response = $card->charge($amount)
        ->withCurrency('USD')
        ->withAddress($address)
        ->withCustomerData($customer)
        ->withAllowDuplicates(true)
        ->execute();

    $body = '<h1>Success!</h1>';
    $body .= '<p>Thank you, ' . 
            htmlspecialchars( $formData['cardholder_name']) .
            ', for your order of $' . filter_input( INPUT_POST, "payment_amount", FILTER_SANITIZE_NUMBER_FLOAT) . '.</p>';

    echo "<b>Transaction Success! </b><br/> Transaction Id: " . $response->transactionId;
    echo "<br />Invoice Number: " . isset($invoiceNumber) ? $invoiceNumber : "";

} catch (Exception $e) {
    echo 'Failure: ' . $e->getMessage();
    exit;
}

function sendEmail($to, $from, $subject, $body, $isHtml)
{
    $message = '<html><body>';
    $message .= $body;
    $message .= '</body></html>';

    $headers = "From: $from\r\n";
    $headers .= "Reply-To: $from\r\n";

    if ($isHtml) {
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=ISO-8859-1\r\n";
    }

    mail($to, $subject, $message, $headers);
}