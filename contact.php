<?php

require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';
require 'vendor/phpmailer/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;

define("ADMINMAIL", "nikhilthomas789@gmail.com");
define("SITE", "Give Way General Trading LLC");
define("FROMMAIL", "givewayparts@gmail.com");
define("SENDER_MAIL", "givewayparts@gmail.com");

function sendEmail($toEmail, $emailSubject = '', $messageBody = '', $attachments = null, $ccEmail = null, $bccEmail = null)
{
    $mail = new PHPMailer();

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = ADMINMAIL;
    $mail->Password = 'gsdoazrywmssldty';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom(FROMMAIL);
    $mail->addAddress($toEmail);
    $mail->Subject = $emailSubject;
    $mail->isHTML(true);
    $mail->Body = $messageBody;

    if ($attachments !== null) {
        foreach ($attachments as $attachment) {
            $mail->addAttachment($attachment);
        }
    }

    if ($ccEmail !== null) {
        foreach ($ccEmail as $cc) {
            $mail->addCC($cc['email'], $cc['name']);
        }
    }

    if ($bccEmail !== null) {
        foreach ($bccEmail as $bcc) {
            $mail->addBCC($bcc['email'], $bcc['name']);
        }
    }

    // Error handling
    if (!$mail->send()) {
        return 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        return true;
    }
}


function defaultTemplate($html)
{
    $template = '
        <!doctype html>
        <html>
        <head>
        <meta charset="utf-8">
        <title></title>
        </head>
        <body style="
        margin:0px;
        font-family:verdana,Helvetica, sans-serif;
        color:#505050;
        font-size:12px;
        height: 100%;
        background-color:#f3f3f3;">
        <div style="
        background-color:#FFF;
        padding:25px 40px 60px 40px;
        color:#000; font-size:13px;
        line-height:20px;height:100%;">' . $html . '</div>
        </div>
        </body>
        </html>';
    return $template;
}

function verifyRecaptcha($recaptchaSecretKey, $recaptchaResponse)
{
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = [
        'secret'   => $recaptchaSecretKey,
        'response' => $recaptchaResponse,
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ],
    ];

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $result = json_decode($result, true);

    return $result['success'];
}

// Check if the request method is POST
// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify reCAPTCHA
    $recaptchaSecretKey = '6LeIz2UqAAAAAE_QVPxc6ad7G-b1lLW7HtiP0OPO'; 
    $recaptchaResponse = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';

    $recaptchaResult = verifyRecaptcha($recaptchaSecretKey, $recaptchaResponse);

    if (!$recaptchaResult) {
        echo json_encode(['status' => 0, 'error' => 'reCAPTCHA verification failed']);
        exit;
    }

    // Proceed with form processing if reCAPTCHA is successful
    $name = isset($_POST["name"]) ? $_POST['name'] : "";
    $email = isset($_POST["email"]) ? $_POST['email'] : "";
    $phone = isset($_POST["phone"]) ? $_POST['phone'] : "";
    $message = isset($_POST["message"]) ? $_POST['message'] : "";
    $subject = isset($_POST["subject"]) ? $_POST['subject'] : "";

    $html = '
                <b> Give Way General Trading LLC </b>.<br><br>
                <table cellspacing="0" cellpadding="5" width="100%" border="0" bgcolor="#f7f7f7"
                    style="font-family:Verdana, Geneva, sans-serif; color: #6d6d6d; font-size: 12px; line-height: 22px; padding:10px;">
                <tr><td><b>Name:</b></td><td>' . $name . '</td></tr>
                <tr><td><b>Email:</b></td><td>' . $email . '</td></tr>
                <tr><td><b>Phone:</b></td><td>' . $phone . '</td></tr>
                <tr><td><b>Subject:</b></td><td>' . $subject . '</td></tr>
                <tr><td><b>Message:</b></td><td>' . $message . '</td></tr>
                </table>';

    $toEmail = SENDER_MAIL;
    $emailSubject = "Give Way General Trading LLC";
    $messageBody = defaultTemplate($html);

    $return = sendEmail($toEmail, $emailSubject, $messageBody);

    if ($return === true) {
        echo json_encode(['status' => 1, 'message' => 'Your message has been sent successfully!']);
    } else {
        // Echo error message for debugging
        echo json_encode(['status' => 0, 'error' => $return]);
    }
} else {
    echo json_encode(['status' => 0, 'error' => 'Invalid request method']);
    exit;
}
