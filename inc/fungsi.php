<?php
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
function url_dasar(){
    //$_SERVER['SERVER_NAME'] : alamat website, misalkan websitemu.com
    //$_SERVER['SCRIPT_NAME'] : directory website, websitemu.com/blog/$_SERVER['SCRIPT_NAME']
    $url_dasar  = "http://".$_SERVER['SERVER_NAME'].dirname($_SERVER['SCRIPT_NAME']);
    return $url_dasar;
}