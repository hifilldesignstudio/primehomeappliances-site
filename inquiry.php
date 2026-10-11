<?php
// Prime Home Appliances — contact form handler
// Sends form submissions to info@primehomeappliances.ca

$TO = 'info@primehomeappliances.ca';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

function clean($v) {
    return trim(strip_tags((string)$v));
}

$name    = clean($_POST['name']    ?? '');
$phone   = clean($_POST['phone']   ?? '');
$email   = clean($_POST['email']   ?? '');
$message = clean($_POST['message'] ?? '');

// basic validation
if ($name === '' || $message === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    exit('Please fill in your name, a valid email and a message.');
}

// block header injection
foreach ([$name, $phone, $email] as $f) {
    if (preg_match('/[\r\n]/', $f)) {
        http_response_code(400);
        exit('Invalid input.');
    }
}

$subject = 'Website enquiry from ' . $name;
$body  = "Name: {$name}\n";
$body .= "Phone: {$phone}\n";
$body .= "Email: {$email}\n\n";
$body .= "Message:\n{$message}\n";

$headers  = "From: Prime Home Appliances Website <{$TO}>\r\n";
$headers .= "Reply-To: {$name} <{$email}>\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

if (mail($TO, $subject, $body, $headers)) {
    http_response_code(200);
    echo 'OK';
} else {
    http_response_code(500);
    echo 'Mail failed.';
}
