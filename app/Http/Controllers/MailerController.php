<?php

namespace App\Http\Controllers;

use App\Models\MailSetting;
use App\Models\User;
use DragonCode\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


class MailerController extends Controller implements ShouldQueue
{
    public function store(MailSetting $settings, User $user, $subject, $body)
    {
        $mail = new PHPMailer();
        try {
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = $settings->host;
            $mail->SMTPAuth = true;
            $mail->Username = $settings->username;
            $mail->Password = $settings->password;
            $mail->SMTPSecure = $settings->encryption;
            $mail->Port = $settings->port;
        
            $mail->setFrom($settings->address, $settings->name);
            $mail->addAddress($user->email);
        
            $mail->isHTML(true);
        
            $mail->Subject = $subject;
            $mail->Body = $body;
        
            if (!$mail->send()) {
                return back()->with('error', 'Email not sent.')->withErrors($mail->ErrorInfo);
            } else {
                return back()->with('success', 'Email has been sent.');
            }
        } catch (Exception $e) {
            return back()->with('error', 'Message could not be sent');
        }
    }
}
