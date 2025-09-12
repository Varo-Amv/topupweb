<?php
include_once("inc/koneksi.php");
include_once("inc/fungsi.php");
?>
<?php
// Production-safe error handling
error_reporting(E_ALL);                 // tetap laporkan semua error
ini_set('display_errors', '0');         // JANGAN tampilkan ke browser
ini_set('log_errors', '1');             // LOG saja
ini_set('error_log', __DIR__ . '/php-error.log'); // lokasinya bebas
// mysqli: jangan lempar warning ke output
mysqli_report(MYSQLI_REPORT_OFF);
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
$password   = "";
$err        = "";

if(isset($_POST['masuk'])){
  $email      = $_POST['email'];
  $password   = $_POST['password'];

  if($email == '' or $password == ''){
    $err .= "<li>Email atau Password belum di isi.</li>";
  } else {
    $sql1    = "select * from users where email = '$email'";
    $q1      = mysqli_query($koneksi,$sql1);
    $r1      = mysqli_fetch_array($q1);
    $n1      = mysqli_num_rows($q1);

    if($r1['status'] =='suspended' && $n1 > 0){
      $err  .= "<li>Akun kamu kena suspend.</li>";
    }

    if($r1['status'] !='active' && $n1 > 0 && $r1['status'] !='suspended'){
      $err  .= "<li>Akun yang kamu miliki belum aktif</li>";
    }

    if($r1['password'] != md5($password) && $r1['status'] == 'active'){
      $err  .= "<li>Password tidak sesuai.</li>";
    }

    if($n1 < 1){
      $err  .= "<li>Akun tidak ditemukan.</li>";
    }

    if(empty($err) && $r1['role'] == 'admin'){
      header('location:admin/index.php');
      exit();
    }

    if(empty($err)){
      header("location:index.php");
      exit();
    }
  }
}
?>
<?php if($err){ echo "<div class='error'><ul class='pesan'>$err</ul></div>";}?>
<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Masuk · VAZATECH</title>
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

        <h1 class="title">MASUK</h1>

        <form class="auth-form" action="#" method="post" novalidate>
          <label class="field">
            <input
              type="email"
              name="email"
              class="input"
              placeholder="Email"
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
            />
            <button
              type="button"
              class="toggle"
              aria-label="Tampilkan password"
              onclick="togglePwd(this)"
            >
              <svg
                viewBox="0 0 24 24"
                width="20"
                height="20"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z" />
                <circle cx="12" cy="12" r="3" />
              </svg>
            </button>
          </label>

          <div class="row between">
            <a href="#" class="link small">Lupa Password?</a>
          </div>

          <input class="btn primary" type="submit" value="Masuk" name="masuk">

          <div class="divider"><span>Atau masuk dengan</span></div>

          <div class="oauth">
            <button class="btn ghost">
              <img src="./image/google.png" height="30px" width="30px" />
            </button>
            <button class="btn ghost">
              <img src="./image/facebook.png" height="35px" width="35px" />
            </button>
          </div>

          <p class="foot">
            Belum punya akun?
            <a href="./daftar.php" class="link strong">Buat Disini</a>
          </p>
        </form>
      </div>
    </div>

    <script>
      function togglePwd(btn) {
        const input = btn.parentElement.querySelector("input");
        const is = input.type === "password";
        input.type = is ? "text" : "password";
        btn.setAttribute(
          "aria-label",
          is ? "Sembunyikan password" : "Tampilkan password"
        );
      }
    </script>
  </body>
</html>
