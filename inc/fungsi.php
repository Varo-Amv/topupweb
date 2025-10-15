<?php
function goBack_url(){
    $goBack_url = htmlspecialchars($_SERVER['HTTP_REFERER']);
    return $goBack_url;
}
function url_dasar(){
    //$_SERVER['SERVER_NAME'] : alamat website, misalkan websitemu.com
    //$_SERVER['SCRIPT_NAME'] : directory website, websitemu.com/blog/$_SERVER['SCRIPT_NAME']
    $url_dasar  = "http://".$_SERVER['SERVER_NAME'].dirname($_SERVER['SCRIPT_NAME']);
    return $url_dasar;
}
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function kirim_email($email, $nama, $judul_email, $isi_email) {

    $email_pengirim     = "vazatech.id@gmail.com";
    $nama_pengirim      = "VAZATECH";

//Load Composer's autoloader (created by composer, not included with PHPMailer)
require getcwd().'../vendor/autoload.php';

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = 0;
    $mail->Debugoutput = 'error_log';                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = $email_pengirim;                     //SMTP username
    $mail->Password   = 'brqyskzybwdiykrg';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom($email_pengirim, $nama_pengirim);
    $mail->addAddress($email, $nama);     //Add a recipient
    
    //Attachments
    //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = $judul_email;
    $mail->Body    = $isi_email;

    $mail->send();
    return "sukses";
} catch (Exception $e) {
    return "Gagal. Mailer Error: {$mail->ErrorInfo}";
}
}