<?php
include __DIR__ . '/config/database.php';
include __DIR__ . '/config/helpers.php';

$tahun_aktif = ambil_tahun();

$total_fisik     = hitung_total($conn, 'usulan_fisik', $tahun_aktif);
$total_ekonomi   = hitung_total($conn, 'data_ekonomi', $tahun_aktif);
$total_sosial    = hitung_total($conn, 'data_sosial_budaya', $tahun_aktif);
$total_dau       = hitung_total($conn, 'data_dau', $tahun_aktif);
$total_kerjasama = hitung_total($conn, 'data_kerjasama_dinas', $tahun_aktif);
$total_pemetaan  = hitung_total($conn, 'data_pemetaan_wilayah', $tahun_aktif);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - E-MUSANG Kelurahan Antasan Kecil Timur</title>
    <!-- Google Fonts & Bootstrap 5 -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- CSS bersama E-MUSANG -->
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>

    <?php include __DIR__ . '/navbar.php'; ?>

    <div class="container my-5">

        <!-- HERO SECTION BERANDA -->
        <div class="hero-section mb-5 text-center text-lg-start">
            <div class="row align-items-center g-4">
                <div class="col-12">
                    <span class="badge bg-white text-primary fw-bold px-3 py-2 rounded-pill mb-3 shadow-sm">
                        <i class="bi bi-building-fill me-1"></i> Portal Kelurahan Antasan Kecil Timur
                    </span>
                    <span class="badge bg-warning text-dark fw-bold px-3 py-2 rounded-pill mb-3 ms-1 shadow-sm">
                        <i class="bi bi-calendar-check me-1"></i> Periode <?= e($tahun_aktif); ?>
                    </span>

                    <!-- JUDUL DENGAN LOGO DI SISI KIRI -->
                    <div class="d-flex flex-column flex-lg-row align-items-center gap-3 justify-content-center justify-content-lg-start mb-3">
                        <img src="assets/logo.png" alt="Logo" class="hero-logo" onerror="this.onerror=null;this.src='assets/no-image.svg'">
                        <h1 class="fw-bold display-6 mb-0">Selamat Datang di E-MUSANG Kelurahan Antasan Kecil Timur</h1>
                    </div>

                    <p class="fs-5 opacity-90 mb-0">
                        Sistem Informasi Ekonomi Musyawarah Pembangunan, Pemetaan Potensi Ekonomi, Sarana Sosial Budaya, DAU, Kerjasama Dinas, dan Pemetaan Wilayah Berbasis RT.
                    </p>
                </div>
            </div>
        </div>

        <!-- EDUKASI MUSRENBANG -->
        <div class="card card-sektor p-4 mb-5 border-0 shadow-sm">
            <div class="row align-items-center g-4">
                <div class="col-lg-5">
                    <span class="badge bg-primary bg-opacity-10 text-primary fw-bold px-3 py-2 rounded-pill mb-2">
                        <i class="bi bi-info-circle-fill me-1"></i> Edukasi Pembangunan
                    </span>
                    <h3 class="fw-bold text-dark mb-3">Apa itu Musrenbang Kelurahan?</h3>
                    <p class="text-secondary small mb-3" style="line-height: 1.7;">
                        <strong>Musrenbang (Musyawarah Perencanaan Pembangunan)</strong> adalah forum musyawarah tahunan antar pemangku kepentingan di tingkat kelurahan untuk menyepakati Rencana Kerja Pembangunan Daerah (RKPD) tahun anggaran berikutnya.
                    </p>
                    <div class="p-3 bg-light rounded-3 border-start border-4 border-primary">
                        <small class="text-dark fw-semibold d-block">
                            <i class="bi bi-quote text-primary me-1 fs-5"></i>
                            Melalui E-MUSANG, proses Musrenbang Kelurahan Antasan Kecil Timur menjadi lebih transparan, akuntabel, dan dapat dipantau oleh seluruh warga.
                        </small>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 bg-light h-100 border">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="bi bi-sliders text-primary fs-4"></i>
                                    <h6 class="fw-bold mb-0 text-dark">Fungsi Musrenbang</h6>
                                </div>
                                <p class="text-muted small mb-0">
                                    Sebagai penampung dan penyelaras usulan kegiatan dari tingkat RT dengan skala prioritas pembangunan kelurahan.
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 rounded-3 bg-light h-100 border">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="bi bi-bullseye text-danger fs-4"></i>
                                    <h6 class="fw-bold mb-0 text-dark">Tujuan Pembangunan</h6>
                                </div>
                                <p class="text-muted small mb-0">
                                    Mewujudkan pemerataan infrastruktur, pemetaan potensi ekonomi, serta peningkatan kesejahteraan warga.
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 rounded-3 bg-light h-100 border">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="bi bi-eye-fill text-success fs-4"></i>
                                    <h6 class="fw-bold mb-0 text-dark">Manfaat Transparansi</h6>
                                </div>
                                <p class="text-muted small mb-0">
                                    Masyarakat dapat memantau status usulan pembangunan (ACC / Proses) serta realisasi lapangan secara langsung.
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 rounded-3 bg-light h-100 border">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="bi bi-people-fill text-warning fs-4"></i>
                                    <h6 class="fw-bold mb-0 text-dark">Partisipasi Warga</h6>
                                </div>
                                <p class="text-muted small mb-0">
                                    Mendorong keterlibatan aktif masyarakat dalam merencanakan kebutuhan prioritas wilayah RT masing-masing.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SEKSI PILIH SEKTOR / MENU UTAMA -->
        <div class="text-center mb-4">
            <h3 class="fw-bold text-dark mb-1">Jelajahi Sektor Pembangunan & Program</h3>
            <p class="text-muted">Pilih kategori di bawah ini untuk melihat data lengkap per RT atau program kelurahan</p>
        </div>

        <div class="row g-4 justify-content-center">

            <!-- CARD 1: FISIK & INFRASTRUKTUR -->
            <div class="col-md-6 col-lg-4">
                <div class="card card-sektor h-100 p-4 d-flex flex-column">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary mb-3">
                        <i class="bi bi-bricks"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-2">Fisik & Infrastruktur</h4>
                    <p class="text-muted small mb-4">
                        Usulan pembangunan jalan, jembatan, drainase, status persetujuan (ACC/Belum ACC) beserta dokumentasi foto realisasi per RT.
                    </p>
                    <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                        <span class="badge bg-light text-dark border"><i class="bi bi-files me-1"></i><?= $total_fisik; ?> Usulan</span>
                        <a href="rakorbang.php?tahun=<?= e($tahun_aktif); ?>&kategori=fisik" class="btn btn-blue rounded-pill px-3 btn-sm fw-semibold">
                            Lihat Data <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- CARD 2: EKONOMI -->
            <div class="col-md-6 col-lg-4">
                <div class="card card-sektor h-100 p-4 d-flex flex-column">
                    <div class="icon-box bg-info bg-opacity-10 text-info mb-3">
                        <i class="bi bi-shop"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-2">Ekonomi (UMKM & LPG)</h4>
                    <p class="text-muted small mb-4">
                        Data UMKM masyarakat, pangkalan/agen LPG 3kg, alamat lengkap, dan integrasi titik koordinat Google Maps per RT.
                    </p>
                    <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                        <span class="badge bg-light text-dark border"><i class="bi bi-geo-alt me-1"></i><?= $total_ekonomi; ?> Usaha</span>
                        <a href="rakorbang.php?tahun=<?= e($tahun_aktif); ?>&kategori=ekonomi" class="btn btn-info text-white rounded-pill px-3 btn-sm fw-semibold">
                            Lihat Data <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- CARD 3: SOSIAL BUDAYA -->
            <div class="col-md-6 col-lg-4">
                <div class="card card-sektor h-100 p-4 d-flex flex-column">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary mb-3">
                        <i class="bi bi-moon-stars"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-2">Sosial Budaya</h4>
                    <p class="text-muted small mb-4">
                        Pendataan sarana ibadah (Masjid, Musholla) serta cagar budaya / situs religi (Kubah Abah Haji) per wilayah RT.
                    </p>
                    <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                        <span class="badge bg-light text-dark border"><i class="bi bi-building me-1"></i><?= $total_sosial; ?> Tempat</span>
                        <a href="rakorbang.php?tahun=<?= e($tahun_aktif); ?>&kategori=sosial" class="btn btn-blue rounded-pill px-3 btn-sm fw-semibold">
                            Lihat Data <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- CARD 4: DAU -->
            <div class="col-md-6 col-lg-4">
                <div class="card card-sektor h-100 p-4 d-flex flex-column">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary mb-3">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-2">Dana Alokasi Umum (DAU)</h4>
                    <p class="text-muted small mb-4">
                        Transparansi pengalokasian dan pengerjaan kegiatan pembangunan/pemberdayaan masyarakat dari Dana Alokasi Umum.
                    </p>
                    <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                        <span class="badge bg-light text-dark border"><i class="bi bi-journal-check me-1"></i><?= $total_dau; ?> Program</span>
                        <a href="dau.php?tahun=<?= e($tahun_aktif); ?>" class="btn btn-blue rounded-pill px-3 btn-sm fw-semibold">
                            Lihat Data <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- CARD 5: KERJASAMA DINAS -->
            <div class="col-md-6 col-lg-4">
                <div class="card card-sektor h-100 p-4 d-flex flex-column">
                    <div class="icon-box bg-info bg-opacity-10 text-info mb-3">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-2">Kerjasama Dinas Lain</h4>
                    <p class="text-muted small mb-4">
                        Pendataan program, bantuan, dan proyek kolaborasi antara Kelurahan Antasan Kecil Timur dengan Dinas/Instansi terkait.
                    </p>
                    <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                        <span class="badge bg-light text-dark border"><i class="bi bi-diagram-3 me-1"></i><?= $total_kerjasama; ?> Kerjasama</span>
                        <a href="kerjasama.php?tahun=<?= e($tahun_aktif); ?>" class="btn btn-info text-white rounded-pill px-3 btn-sm fw-semibold">
                            Lihat Data <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- CARD 6: PEMETAAN WILAYAH -->
            <div class="col-md-6 col-lg-4">
                <div class="card card-sektor h-100 p-4 d-flex flex-column">
                    <div class="icon-box bg-info bg-opacity-10 text-info mb-3">
                        <i class="bi bi-map-fill"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-2">Pemetaan Wilayah</h4>
                    <p class="text-muted small mb-4">
                        Dokumen peta dan pemetaan wilayah Kelurahan Antasan Kecil Timur dalam bentuk PDF yang dapat dilihat dan diunduh langsung.
                    </p>
                    <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                        <span class="badge bg-light text-dark border"><i class="bi bi-file-earmark-pdf me-1"></i><?= $total_pemetaan; ?> Dokumen</span>
                        <a href="pemetaan.php?tahun=<?= e($tahun_aktif); ?>" class="btn btn-info text-white rounded-pill px-3 btn-sm fw-semibold">
                            Lihat Data <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <?php include __DIR__ . '/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>