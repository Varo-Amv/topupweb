<?php
// topupweb/assets/api/profile_upload.php

// ------ Setup path ke root proyek ------
$ROOT = dirname(__DIR__, 2);              // .../topupweb
require $ROOT . '/inc/session.php';
require $ROOT . '/inc/koneksi.php';
require $ROOT . '/inc/auth.php';
require_login();

function back_with($q) {
  // kembali ke halaman profil (relative dari assets/api)
  header("Location: ../../profile.php?$q");
  exit;
}

// Pastikan ada file
if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] === UPLOAD_ERR_NO_FILE) {
  back_with("upload=none");
}
if ($_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
  back_with("upload=err");
}

// Validasi ukuran (maks 3 MB)
$maxBytes = 3 * 1024 * 1024;
if ($_FILES['avatar']['size'] > $maxBytes) {
  back_with("upload=toolarge");
}

// Deteksi MIME + ekstensi yang diizinkan
$allowed = [
  'image/jpeg' => 'jpg',
  'image/png'  => 'png',
  'image/webp' => 'webp',
];

$mime = '';
if (function_exists('finfo_open')) {
  $finfo = finfo_open(FILEINFO_MIME_TYPE);
  $mime  = finfo_file($finfo, $_FILES['avatar']['tmp_name']);
  finfo_close($finfo);
}
if (!$mime) {
  $imgInfo = @getimagesize($_FILES['avatar']['tmp_name']);
  $mime = $imgInfo['mime'] ?? '';
}
if (!isset($allowed[$mime])) {
  back_with("upload=badtype");
}

$ext = $allowed[$mime];

// Ambil user (id & avatar lama)
$stmt = $koneksi->prepare("SELECT id, avatar_path FROM users WHERE email = ?");
$stmt->bind_param("s", $_SESSION['user']['email']);
$stmt->execute();
$me = $stmt->get_result()->fetch_assoc();
if (!$me) { header("Location: ../../logout.php"); exit; }

// Folder upload di root proyek: topupweb/uploads/avatars
$uploadDirFs  = $ROOT . '/uploads/avatars';
$uploadDirWeb = 'uploads/avatars'; // disimpan ke DB untuk <img src="">
if (!is_dir($uploadDirFs)) { @mkdir($uploadDirFs, 0775, true); }

// Nama file unik
$baseName  = 'ava_' . (int)$me['id'] . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
$targetFs  = $uploadDirFs . '/' . $baseName;
$targetWeb = $uploadDirWeb . '/' . $baseName;

// (Opsional) Resize square 256x256 bila GD tersedia
$didResize = false;
if (function_exists('imagecreatetruecolor')) {
  try {
    switch ($mime) {
      case 'image/jpeg': $src = imagecreatefromjpeg($_FILES['avatar']['tmp_name']); break;
      case 'image/png' : $src = imagecreatefrompng($_FILES['avatar']['tmp_name']);  break;
      case 'image/webp': $src = imagecreatefromwebp($_FILES['avatar']['tmp_name']); break;
      default: $src = null;
    }
    if ($src) {
      $w = imagesx($src); $h = imagesy($src);
      $size = min($w, $h);
      $x = (int)(($w - $size) / 2);
      $y = (int)(($h - $size) / 2);
      $crop = imagecrop($src, ['x'=>$x, 'y'=>$y, 'width'=>$size, 'height'=>$size]);
      if ($crop) {
        $dst = imagecreatetruecolor(256, 256);
        imagecopyresampled($dst, $crop, 0,0,0,0, 256,256, $size,$size);
        if ($mime === 'image/jpeg')      imagejpeg($dst, $targetFs, 88);
        elseif ($mime === 'image/png')   imagepng($dst, $targetFs, 6);
        else                             imagewebp($dst, $targetFs, 85);
        imagedestroy($dst); imagedestroy($crop); imagedestroy($src);
        $didResize = true;
      }
    }
  } catch (\Throwable $e) {
    // fallback ke move_uploaded_file di bawah
  }
}

// Jika tidak di-resize, simpan file asli
if (!$didResize) {
  if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $targetFs)) {
    back_with("upload=movefail");
  }
}

// Hapus avatar lama jika ada (pastikan hanya file di uploads/avatars)
if (!empty($me['avatar_path'])) {
  $oldFs = $ROOT . '/' . ltrim($me['avatar_path'], '/');
  if (is_file($oldFs) && strpos(realpath($oldFs), realpath($uploadDirFs)) === 0) {
    @unlink($oldFs);
  }
}

// Update DB
$upd = $koneksi->prepare("UPDATE users SET avatar_path=? WHERE id=?");
$upd->bind_param("si", $targetWeb, $me['id']);
$ok = $upd->execute();

// Update session (jika dipakai untuk render avatar)
$_SESSION['user']['avatar_path'] = $targetWeb;

back_with($ok ? "upload=ok" : "upload=dberr");
