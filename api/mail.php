<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function envoyerMail($destinataire, $sujet, $message) {
    $mail = new PHPMailer(true);

    try {
        $mail->SMTPDebug = 3;
        $mail->Debugoutput = 'html';
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'bluestackspotify@gmail.com';
        $mail->Password = 'jsiw gzcr hueg yoky';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('bluestackspotify@gmail.com', 'GreenBooking');
        $mail->addAddress($destinataire);

        $mail->isHTML(true);
        $mail->Subject = $sujet;
        $mail->Body = $message;

        $mail->send();
        return true;
    } catch (Exception $e) {
        echo "Erreur PHPMailer : {$mail->ErrorInfo}";
        return false;
    }
}
?>
