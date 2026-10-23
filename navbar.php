<?php
// Variabel dari halaman pemanggil dipakai jika ada, jika tidak ambil aman dari URL
$tahun_aktif   = $tahun_aktif   ?? ambil_tahun();
$rt_nav        = $rt_aktif       ?? ambil_rt();
$kategori_nav  = $kategori_aktif ?? ambil_kategori();
$page_sekarang = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top shadow-sm">
    <div class="container-fluid px-lg-4 px-xl-5">

        <!-- Logo Kelurahan & Instansi -->
        <a class="navbar-brand d-flex align-items-center gap-2" href="index.php?tahun=<?= e($tahun_aktif); ?>">
            <img src="assets/banjaarmasin.png" alt="Logo" style="height: 42px;" onerror="this.onerror=null;this.src='assets/no-image.svg'">
            <div>
                <small class="d-block text-uppercase fw-semibold text-white-50" style="font-size: 0.6rem; letter-spacing: 0.5px;">Pemerintah Kota Banjarmasin</small>
                <span class="fw-bold fs-6 text-white text-uppercase d-block" style="line-height: 1.1; font-size: 0.95rem !important;">Kelurahan Antasan Kecil Timur</span>
            </div>
        </a>
        <!-- Tombol Toggler khusus tampilan HP -->
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center gap-2 mt-3 mt-lg-0">

                <!-- 1. BERANDA -->
                <li class="nav-item">
                    <a class="nav-link text-white fw-semibold px-2 me-1 <?= ($page_sekarang == 'index.php') ? 'active fw-bold' : ''; ?>" href="index.php?tahun=<?= e($tahun_aktif); ?>">
                        <i class="bi bi-house-door me-1"></i> Beranda
                    </a>
                </li>


                <!-- 3. DAU -->
                <li class="nav-item">
                    <a class="nav-link text-white fw-semibold px-2 <?= ($page_sekarang == 'dau.php') ? 'active fw-bold' : ''; ?>" href="dau.php?tahun=<?= e($tahun_aktif); ?>">
                        <i class="bi bi-cash-stack me-1"></i> DAU
                    </a>
                </li>

                <!-- 4. KERJASAMA DINAS -->
                <li class="nav-item">
                    <a class="nav-link text-white fw-semibold px-2 <?= ($page_sekarang == 'kerjasama.php') ? 'active fw-bold' : ''; ?>" href="kerjasama.php?tahun=<?= e($tahun_aktif); ?>">
                        <i class="bi bi-people-fill me-1"></i> Kerjasama Dinas
                    </a>
                </li>

                <!-- 5. PEMETAAN WILAYAH -->
                <li class="nav-item">
                    <a class="nav-link text-white fw-semibold px-2 me-2 <?= ($page_sekarang == 'pemetaan.php') ? 'active fw-bold' : ''; ?>" href="pemetaan.php?tahun=<?= e($tahun_aktif); ?>">
                        <i class="bi bi-map-fill me-1"></i> Pemetaan Wilayah
                    </a>
                </li>

                <li class="nav-item dropdown me-2">
                    <a class="btn btn-warning dropdown-toggle fw-bold rounded-pill px-3 py-2 btn-sm shadow-sm" href="#" id="dropdownTahun" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-calendar3 me-1"></i> Ekonomi Pembangunan <?= e($tahun_aktif); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3" aria-labelledby="dropdownTahun">
                        <?php foreach (DAFTAR_TAHUN as $th) : ?>
                            <li>
                                <a class="dropdown-item py-2 fw-semibold <?= ($page_sekarang == 'rakorbang.php' && $tahun_aktif == $th) ? 'active bg-primary text-white' : ''; ?>"
                                    href="rakorbang.php?tahun=<?= $th; ?>&rt=<?= urlencode($rt_nav); ?>&kategori=<?= urlencode($kategori_nav); ?>">
                                    <i class="bi bi-calendar-event me-2"></i> Tahun <?= $th; ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>


                <!-- 6. LOGIN ADMIN -->
                <li class="nav-item">
                    <a class="btn btn-outline-light rounded-pill px-3 py-2 btn-sm fw-semibold" href="admin/login.php">
                        <i class="bi bi-person-lock me-1"></i> Admin Login
                    </a>
                </li>

                <!-- 2. DROPDOWN TAHUN -->


            </ul>
        </div>
    </div>
</nav>