<?php
global $gHeartlandVersionNumber;
global $gHeartlandDeveloperId;
global $gHeartlandMerchantId;
global $gHeartlandPublicKey;
global $gHeartlandSecretKey;
global $gHeartlandServiceUrl;

global $config;
global $formData;

$gHeartlandVersionNumber = '0000';
$gHeartlandDeveloperId = '000000';
$gHeartlandMerchantId = 'merchant-id';
$gHeartlandPublicKey = 'public key';
$gHeartlandSecretKey = 'secret key';
$gHeartlandServiceUrl = 'https://cert.api2.heartlandportico.com/'; // Certification Testing
$gHeartlandServiceUrl = 'https://api2.heartlandportico.com/'; // Production

$file = 'includes/private.php';
if( file_exists($file) ) {
    include "$file";
    echo "loading $file";
} else {
    echo "file $file doesn't exist";
}