<?php

declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Crée et retourne une instance PHPMailer préconfigurée.
 * Tous les envois de mail passent par cette fonction.
 */
function creer_mailer(): PHPMailer
{
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host       = getenv('MAIL_HOST')     ?: 'sandbox.smtp.mailtrap.io';
    $mail->SMTPAuth   = true;
    $mail->Username   = getenv('MAIL_USERNAME')  ?: '';
    $mail->Password   = getenv('MAIL_PASSWORD')  ?: '';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = (int)(getenv('MAIL_PORT') ?: 2525);
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom(
        getenv('MAIL_FROM')      ?: 'noreply@viteetgourmand.fr',
        getenv('MAIL_FROM_NAME') ?: 'Vite & Gourmand'
    );

    return $mail;
}
