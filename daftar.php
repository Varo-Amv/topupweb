<?php
include_once("inc/koneksi.php");
include_once("inc/fungsi.php");
?>
<style>
  .error {
    padding: 20px;
    background-color: #f44336;
    color: #FFFFFF;
    margin-bottom: 15px;
  }

    .sukses {
    padding: 20px;
    background-color: #2196F3;
    color: #FFFFFF;
    margin-bottom: 15px;
  }
</style>

<?php
$email      = "";
$nama       = "";
$no_telp    = "";
$err        = "";
$sukses     = "";

if(isset($_POST['simpan'])){
  $email                 = $_POST['email'];
  $nama                  = $_POST['nama'];
  $no_telp               = $_POST['no_telp'];
  $password              = $_POST['password'];
  $password_confirmation = $_POST['password_confirmation'];

  if($email == '' or $nama == '' or $no_telp == '' or $password == '' or $password_confirmation == ''){
    $err .= "<li>Silahkan masukkan semua isian.</li>";
  }

  //cek di bagian db, apakah email sudah ada atau belum
  if($email !=''){
    $sql1   = "select email from users where email = '$email'";
    $q1     = mysqli_query($koneksi,$sql1);
    $n1     = mysqli_num_rows($q1);
    if($n1 > 0){
      $err .= "<li>Email yang kamu masukkan sudah terdaftar.</li>";
    }
  }
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $err .= 'Email tidak valid.';
}
  if($password != $password_confirmation){
    $err .= "<li>Password dan Konfirmasi Password tidak sesuai!</li>";
  }
  if(strlen($password) < 8) {
    $err .= "Password harus lebih dari 8 karakter.";
  }
if ($no_telp === '' || !ctype_digit($no_telp)) {
    $err .= 'Nomor telepon harus berisi angka saja.';
}

  if(empty($err)){
    $sql1       = "insert into users(nama,email,no_telp,password,role,status) values ('$nama','$email','$no_telp',md5($password),'user','$status')";
    $q1         = mysqli_query($koneksi,$sql1);
    if($q1){
      $sukses = "Daftar Berhasil. Silahkan ke halaman login.";
    }
  }
}
?>
<?php if($err){echo "<div class='error'><ul>$err</ul></div>";} ?>
<?php if($sukses) {echo "<div class='sukses'>$sukses</div>";} ?>
<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Daftar · VAZATECH</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="./assets/css/login.css" />
    <style>
      .auth-card.outlined {
        border: 3px solid #2e6bff;
      }
      .btn.block {
        width: 100%;
      }
    </style>
  </head>
  <body>
    <div class="auth-wrap">
      <div class="auth-card outlined">
        <div class="brand">
          <img
            src="./image/logo_nocapt.png"
            alt="VAZATECH"
            class="brand-logo"
          />
          <span class="logo">V A Z A T E C H</span>
        </div>

        <h1 class="title">DAFTAR</h1>

        <form class="auth-form" action="#" method="post" novalidate>
          <label class="field">
            <input
              type="name"
              name="nama"
              class="input"
              placeholder="Nama"
              value="<?php echo $nama?>"
              required
            />
          </label>
          <label class="field">
            <input
              type="email"
              name="email"
              class="input"
              placeholder="Email"
              value="<?php echo $email?>"
              required
            />
          </label>

          <label class="field">
            <input
              type="tel"
              name="no_telp"
              class="input"
              placeholder="Nomor telepon"
              pattern="[0-9]{3}-[0-9]{2}-[0-9]{3}"
              value="<?php echo $no_telp?>"
              required
            />
          </label>

          <label class="field">
            <input
              type="password"
              name="password"
              class="input"
              placeholder="Password"
              required
              minlength="8"
            />
          </label>

          <label class="field">
            <input
              type="password"
              name="password_confirmation"
              class="input"
              placeholder="Confirm password"
              required
              minlength="8"
            />
          </label>

          <input class="btn primary block" type="submit" value="Daftar" name="simpan">
          <p class="foot">
            Sudah punya akun?
            <a href="./login.php" class="link strong">Masuk Disini</a>
          </p>
        </form>
      </div>
    </div>
  </body>
</html>
