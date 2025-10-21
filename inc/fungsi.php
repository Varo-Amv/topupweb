<?php
 include("koneksi.php");
 require __DIR__.'/env.php';
/**
 * Upload file gambar ke ImgBB dan mengembalikan URL publiknya.
 * @param string $tmpPath   path file sementara (mis. $_FILES['avatar']['tmp_name'])
 * @param string $fileName  nama file asli (optional, untuk penamaan)
 * @param string $apiKey    API key imgbb
 * @return array            ['ok'=>bool, 'url'=>string|null, 'err'=>string|null]
 */
function upload_to_imgbb(string $tmpPath, string $fileName, string $apiKey): array {
    if (!is_file($tmpPath)) {
        return ['ok'=>false, 'url'=>null, 'err'=>'File tidak ditemukan'];
    }
    // Validasi mime dasar
    $mime = mime_content_type($tmpPath) ?: '';
    if (!in_array($mime, ['image/jpeg','image/png','image/webp'])) {
        return ['ok'=>false, 'url'=>null, 'err'=>'Tipe file tidak didukung'];
    }

    $imageData = base64_encode(file_get_contents($tmpPath));
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => 'https://api.imgbb.com/1/upload?key=' . urlencode($apiKey),
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_POSTFIELDS     => [
            'image' => $imageData,
            'name'  => pathinfo($fileName, PATHINFO_FILENAME) ?: 'avatar',
            // 'expiration' => 0 // jika ingin auto-expire, isi detik (opsional)
        ],
    ]);
    $res = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if ($res === false) {
        return ['ok'=>false, 'url'=>null, 'err'=>"cURL error: $err"];
    }
    $json = json_decode($res, true);
    if (!is_array($json) || empty($json['success'])) {
        $msg = $json['error']['message'] ?? 'Upload gagal';
        return ['ok'=>false, 'url'=>null, 'err'=>$msg];
    }
    $url = $json['data']['url'] ?? null;          // URL halaman
    $display = $json['data']['display_url'] ?? null; // URL langsung
    return ['ok'=>true, 'url'=> $display ?: $url, 'err'=>null];
}

function goBack_url(){
    $goBack_url = htmlspecialchars($_SERVER['HTTP_REFERER']);
    return $goBack_url;
}
function base_url(): string {
  // pastikan TANPA trailing slash
  $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
  return $scheme . '://' . $_SERVER['HTTP_HOST']; // <-- tidak pakai '/' di akhir
}

/** Gabung base URL + path dengan benar (hilangkan double slash) */
function url(string $path = ''): string {
  return rtrim(base_url(), '/') . '/' . ltrim($path, '/');
}

#<!--                 Kirim email menggunakan PHPMailer                  -->

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader (created by composer, not included with PHPMailer)
$VENDOR = __DIR__ . '/../vendor/autoload.php';
if (is_file($VENDOR)) {
  require_once $VENDOR;
} else {
  error_log('Composer autoload tidak ditemukan: ' . $VENDOR);
}

/**
 * Kirim email HTML via SMTP (PHPMailer)
 * @param string $email
 * @param string $name
 * @param string $isi_email
 * @return array ['ok'=>bool, 'err'=>string|null]
 */
function kirim_email(string $email, string $nama, string $isi_email): array {
try {
    //Server settings
$mailer = new PHPMailer(true);

        // ======= KONFIGURASI SMTP =======
        $mailer->isSMTP();
        $mailer->Host       = 'mail.vazatech.store';
        $mailer->SMTPAuth   = true;
        $mailer->Username   = 'no-reply@vazatech.store';                  //SMTP username
    $mailer->Password   = $PWMail;                               //SMTP password
    $mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mailer->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mailer->setFrom('no-reply@vazatech.store', 'Verifikasi');
    $mailer->addAddress($email, $nama);     //Add a recipient
    // $mailer->addAddress('ellen@example.com');               //Name is optional
    // $mail->addReplyTo('info@example.com', 'Information');
    // $mail->addCC('cc@example.com');
    // $mail->addBCC('bcc@example.com');

    // //Attachments
    // $mailer->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
    // $mailer->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

    //Content
    $mailer->isHTML(true);                                  //Set email format to HTML
    $mailer->Subject = 'Verifikasi Akun';
    $mailer->Body    = $isi_email;
    $mailer->AltBody = $isi_email; // versi text biasa

    $mailer->send();
    return ['ok' => true, 'err' => null];
  } catch (Exception $e) {
    return ['ok' => false, 'err' => $e->getMessage()];
}
}

/**
 * Redirect (otomatis) setelah jeda detik tertentu.
 * Mengirim header Refresh (kalau belum ada output), plus fallback JS & <noscript>.
 */
function redirect_after(string $url, int $seconds = 3): void {
  $url = rtrim(base_url(), '/') . '/' . ltrim($url, '/'); // rapikan slash

  // Header Refresh (jika header belum terkirim)
  if (!headers_sent()) {
    header("Refresh: {$seconds}; url={$url}");
  }

  // Fallback JS + <noscript> (tetap tampil walau header sudah terkirim)
  echo <<<HTML
<script>
  setTimeout(function(){ location.href = ${json_encode($url)}; }, {$seconds} * 1000);
</script>
<noscript>
  <meta http-equiv="refresh" content="{$seconds};url={$url}">
</noscript>
HTML;
}