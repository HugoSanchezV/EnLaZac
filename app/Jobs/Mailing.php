<?php

namespace App\Jobs;

use App\Models\MailSetting;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailing implements ShouldQueue
{
    use Queueable;

    public $settings;
    public $user;
    public $subject;
    public $body;
    /**
     * Create a new job instance.
     */
    public function __construct(MailSetting $settings, User $user, $subject, $body)
    {
        $this->settings = $settings;
        $this->user = $user;
        $this->subject = $subject;
        $this->body = $body;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $mail = new PHPMailer();
        try {
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = $this->settings->host;
            $mail->SMTPAuth = true;
            $mail->Username = $this->settings->username;
            $mail->Password = $this->settings->password;
            $mail->SMTPSecure = $this->settings->encryption;
            $mail->Port = $this->settings->port;
        
            $mail->setFrom($this->settings->address, $this->settings->name);
            $mail->addAddress($this->user->email);
        
            $mail->isHTML(true);
        
            $mail->Subject = $this->subject;
            $mail->Body = $this->body;
        
            if (!$mail->send()) {
                back()->with('error', 'Email not sent.')->withErrors($mail->ErrorInfo);
            } else {
                back()->with('success', 'Email has been sent.');
            }
        } catch (Exception $e) {
            back()->with('error', 'Message could not be sent');
        }
    }
}
