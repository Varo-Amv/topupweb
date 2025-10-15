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

/* ====== VAZATECH Alert ====== */
.vz-alert {
  position: fixed;
  left: 0; right: 0; bottom: 0;
  z-index: 1100;
  display: flex; align-items: center; justify-content: center; gap: 14px;
  padding: 14px 18px;
  color: #fff;
  background: linear-gradient(90deg, #3c82ff 0%, #2e6bff 40%, #1a2cff 100%);
  box-shadow: 0 -8px 30px rgba(0,0,0,.28);
  border-top: 1px solid rgba(255,255,255,.15);
  font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
}
.vz-alert__icon { width: 20px; height: 20px; display:inline-flex; align-items:center; justify-content:center }
.vz-alert__text { font-size: 16px; font-weight: 700; letter-spacing: .2px }
.vz-alert__spacer { flex: 0 0 8px }
.vz-btn {
  display:inline-flex; align-items:center; justify-content:center;
  height:36px; padding:0 14px; border-radius:999px; border:1px solid transparent;
  font-weight:700; cursor:pointer; user-select:none; transition:background .15s, color .15s, border-color .15s;
}
.vz-btn--light { background:#fff; color:#0a0019; border-color:#fff }
.vz-btn--light:hover { background:#e9eefb; border-color:#e9eefb }
.vz-alert__close { margin-left:6px; background:transparent; border:0; color:#fff; opacity:.85; cursor:pointer; font-size:22px; line-height:1; padding:0 6px }
.vz-alert__close:hover { opacity:1 }
/* ====== end alert ====== */

/* ====== Center variant ====== */
.vz-alert--center{
  left:50%; top:50%; bottom:auto; transform:translate(-50%,-50%);
  width:auto; max-width:640px; min-width:320px;
  border-radius:14px; padding:14px 18px;
  box-shadow:0 18px 60px rgba(0,0,0,.45);
}
@media (max-width:420px){ .vz-alert--center{ min-width:min(92vw,360px) } }
/* ====== end center ====== */
</style>

<?php
$err        = "";
$sukses     = "";

if(!isset($_GET['email']) or !isset($_GET['kode'])){
    $err    = "Data yang diperlukan untuk verifikasi tidak tersedia.";
} else {
    $email  = $_GET['email'];
    $kode   = $_GET['kode'];

    $sql1   = "select * from users where email = '$email'";
    $q1     = mysqli_query($koneksi,$sql1);
    $r1     = mysqli_fetch_array($q1);
    if($r1['status'] == $kode){
        $sql2   = "update users set status = 'active' where email = '$email'";
        mysqli_query($koneksi,$sql2);
        $sukses = "Verifikasi Berhasil! Silahkan kembali ke halaman sebelumnya";
    } else {
        $err = "Kode tidak valid.";
    }
}
?>

<?php if($err): ?>
  <div class="vz-alert vz-alert--center" role="alert" aria-live="polite">
    <span class="vz-alert__icon" aria-hidden="true">
      <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
           stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 9v4m0 4h.01M12 5a7 7 0 1 0 0 14a7 7 0 0 0 0-14z"></path>
      </svg>
    </span>
    <span class="vz-alert__text"><?php echo htmlspecialchars($err, ENT_QUOTES, 'UTF-8'); ?></span>
    <button class="vz-alert__close" onclick="this.parentElement.remove()" aria-label="Tutup">&times;</button>
  </div>
<?php endif; ?>


<?php if($sukses): ?>
  <div class="vz-alert vz-alert--center" role="alert" aria-live="polite">
    <span class="vz-alert__icon" aria-hidden="true">
      <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
           stroke-linecap="round" stroke-linejoin="round">
        <path d="M20 6L9 17l-5-5"></path>
      </svg>
    </span>
    <span class="vz-alert__text"><?php echo htmlspecialchars($sukses, ENT_QUOTES, 'UTF-8'); ?></span>
    <span class="vz-alert__spacer"></span>
    <a href="<?= url_dasar(). "/profile.php" ?>" class="vz-btn vz-btn--light">Kembali</a>
    <button class="vz-alert__close" onclick="this.parentElement.remove()" aria-label="Tutup">&times;</button>
  </div>
  <script>
    setTimeout(function(){ var el=document.querySelector('.vz-alert'); if(el) el.remove(); }, 8000);
        function goBack(){
      if (document.referrer && document.referrer !== location.href) history.back();
      else window.location.href = 'profile.php';
    }
  </script>
<?php endif; ?>
