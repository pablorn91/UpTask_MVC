<?php

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email {
    protected $email;
    protected $nombre;
    protected $token;

    public function __construct($email, $nombre, $token) 
    {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
    }
    public function enviarConfirmacion() {
        
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = 'bbb894252431fa';
        $mail->Password = '2acd6537e4a8e7';

        $mail->setFrom('cuentas@uptask.com');
        $mail->addAddress('cuentas@uptask.com', 'upstask.com');
        $mail->Subject = 'Confirma tu Cuenta';

        $mail->isHTML(true);
        $mail->CharSet = 'UFT-8';

        $contenido = '<html>';
        $contenido .='<p><strong>Hola: '. $this->nombre .'</strong> Has Creado tu cuenta en UpTask, solo debes confirmarla en el siguiente enlace</p>';
        $contenido .= '<P>Presiona aquí: <a href="http://localhost:3000/confirmar?token='. $this->token .'" >Confirmar Cuenta</a></p>';
        $contenido .= '<p>Si tu no creaste esta cuenta puedes ignorar el mensaje</p>';
        $contenido .= '</html>';

        $mail->Body = $contenido;

        //Enviar el email
        $mail->send();
    }

    public function enviarReestablecer() {
        
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = 'bbb894252431fa';
        $mail->Password = '2acd6537e4a8e7';

        $mail->setFrom('cuentas@uptask.com');
        $mail->addAddress('cuentas@uptask.com', 'upstask.com');
        $mail->Subject = 'Reestablece tu Password';

        $mail->isHTML(true);
        $mail->CharSet = 'UFT-8';

        $contenido = '<html>';
        $contenido .='<p><strong>Hola: '. $this->nombre .'</strong> Parece que has solicitado reestablecer tu password, solo debes ir al siguiente enlace</p>';
        $contenido .= '<P>Presiona aquí: <a href="http://localhost:3000/reestablecer?token='. $this->token .'" >Reestablecer Password</a></p>';
        $contenido .= '<p>Si no solicitaste este cambio puedes ignorar el mensaje</p>';
        $contenido .= '</html>';

        $mail->Body = $contenido;

        //Enviar el email
        $mail->send();
    }
}