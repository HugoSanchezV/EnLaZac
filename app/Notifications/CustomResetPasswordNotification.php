<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Models\EmailConfig;
use App\Models\MailSetting;

class CustomResetPasswordNotification extends Notification
{
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Restablecimiento de contraseña enviado',
        ];
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // Obtén la configuración de correo de la base de datos
        $emailConfig = MailSetting::find(1); // Supongamos que siempre usas el registro con id 1

        if (!$emailConfig) {
            // Manejar el error si la configuración no se encuentra
            throw new Exception('No se pudo encontrar la configuración de correo.');
        }

        $mail = new PHPMailer(true);
        $resetUrl = route('password.reset', $this->token);

        try {
            // Configura PHP Mailer con los datos de la base de datos
            $mail->isSMTP();
            $mail->Host = $emailConfig->host;
            $mail->SMTPAuth = true;
            $mail->Username = $emailConfig->username;
            $mail->Password = $emailConfig->password;
            $mail->SMTPSecure = $emailConfig->encryption; // ssl o tls
            $mail->Port = $emailConfig->port;

            // Configura el correo
            $mail->setFrom($emailConfig->address, $emailConfig->name);
            $mail->addAddress($notifiable->email);
            $mail->isHTML(true);
            $mail->Subject = 'Restablece tu contraseña';
            $mail->Body = '
    <!DOCTYPE html>
    <html>
    <body style="background-color: #f7fafc; padding: 32px;">
        <div style="max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); padding: 24px;">
            <h1 style="font-size: 24px; font-weight: 700; color: #3b82f6;">¡Hola, Restablece tu password!</h1>
            <p style="color: #4a5568; margin-top: 16px;">
                Para restablecer tu contraseña ingresa en el link y llena el formulario.
            </p>
            <a href="' . $resetUrl . '" style="display: inline-block; margin-top: 24px; background-color: #3b82f6; color: #ffffff; padding: 8px 16px; border-radius: 8px; text-decoration: none;">
                Restablecer
            </a>
        </div>
    </body>
    </html>';

            $mail->send();
        } catch (Exception $e) {
            // Manejar errores en caso de que el correo falle
            error_log('Error al enviar el correo de restablecimiento de contraseña: ' . $e->getMessage());
        }

        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Restablecimiento de Contraseña')
            ->line('Por favor, revisa tu correo electrónico para continuar con el restablecimiento de contraseña.');
    }
}
