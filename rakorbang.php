<?php
include __DIR__ . '/config/database.php';
include __DIR__ . '/config/helpers.php';

// Semua parameter URL divalidasi lewat whitelist (lihat config/helpers.php)
$tahun_aktif    = ambil_tahun();
$rt_aktif       = ambil_rt();
$kategori_aktif = ambil_kategori();

// Nama tabel hanya dari daftar tetap, tidak pernah dari input pengguna
$tabel_map = [
    'fisik'   => 'usulan_fisik',
    'ekonomi' => 'data_ekonomi',
    'sosial'  => 'data_sosial_budaya',
];
$tabel = $tabel_map[$kategori_aktif];

$result = ambil_data(
    $conn,
    "SELECT * FROM $tabel WHERE tahun_rakorbang = ? AND TRIM(UPPER(rt)) = TRIM(UPPER(?)) ORDER BY id DESC",
    'is',
    (int)$tahun_aktif,
    $rt_aktif
);
$ada_data = ($result && mysqli_num_rows($result) > 0);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Musang - <?= e(ucfirst($kategori_aktif)); ?> <?= e($tahun_aktif); ?></title>
    <!-- Google Fonts & Bootstrap 5 -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- CSS bersama E-MUSANG -->
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>

    <?php include __DIR__ . '/navbar.php'; ?>

    <div class="container my-4">

        <!-- HERO BANNER -->
        <div class="hero-banner mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h3 class="fw-bold mb-1">E-Musyawarah Pembangunan</h3>
                <p class="mb-0 opacity-90 fs-6">Kelurahan Antasan Kecil Timur, Kota Banjarmasin</p>
            </div>
            <div class="bg-white px-3 py-2 rounded-pill shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-geo-alt-fill text-danger fs-5"></i>
                <span class="fs-6 text-dark fw-bold">
                    <?= e($rt_aktif); ?> <span class="text-muted fw-normal">(Periode <?= e($tahun_aktif); ?>)</span>
                </span>
            </div>
        </div>

        <!-- PILIH RT WILAYAH -->
        <div class="card card-modern mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold text-uppercase text-muted mb-3">
                    <i class="bi bi-map me-1 text-primary"></i> PILIH WILAYAH RT (PERIODE <?= e($tahun_aktif); ?>)
                </h6>
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach (daftar_rt() as $rt_item) : ?>
                        <a href="rakorbang.php?tahun=<?= e($tahun_aktif); ?>&rt=<?= urlencode($rt_item); ?>&kategori=<?= e($kategori_aktif); ?>"
                            class="btn btn-rt <?= ($rt_aktif == $rt_item) ? 'active' : ''; ?>">
                            <?= e($rt_item); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- TAB 3 KATEGORI RT -->
        <ul class="nav nav-category mb-4">
            <li class="nav-item">
                <a class="nav-link <?= ($kategori_aktif == 'fisik') ? 'active-kat' : ''; ?>"
                    href="rakorbang.php?tahun=<?= e($tahun_aktif); ?>&rt=<?= urlencode($rt_aktif); ?>&kategori=fisik">
                    <i class="bi bi-bricks me-2"></i>Fisik & Infrastruktur
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($kategori_aktif == 'ekonomi') ? 'active-kat' : ''; ?>"
                    href="rakorbang.php?tahun=<?= e($tahun_aktif); ?>&rt=<?= urlencode($rt_aktif); ?>&kategori=ekonomi">
                    <i class="bi bi-shop me-2"></i>Ekonomi (UMKM & LPG)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($kategori_aktif == 'sosial') ? 'active-kat' : ''; ?>"
                    href="rakorbang.php?tahun=<?= e($tahun_aktif); ?>&rt=<?= urlencode($rt_aktif); ?>&kategori=sosial">
                    <i class="bi bi-moon-stars me-2"></i>Sosial Budaya
                </a>
            </li>
        </ul>

        <!-- TAMPILAN DATA KATEGORI TERPILIH -->
        <div class="row g-4">

            <!-- 1. KATEGORI FISIK / INFRASTRUKTUR -->
            <?php if ($kategori_aktif == 'fisik') : ?>
                <?php if ($ada_data) : ?>
                    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card card-modern h-100 p-3 shadow-sm d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-light text-dark border px-2 py-1">
                                        <i class="bi bi-tag-fill me-1 text-primary"></i>Infrastruktur
                                    </span>
                                    <span class="<?= ($row['status'] == 'ACC') ? 'badge-status-acc' : 'badge-status-pending'; ?>">
                                        <?= e($row['status']); ?>
                                    </span>
                                </div>

                                <h5 class="fw-bold mb-2 text-dark"><?= e($row['nama_usulan']); ?></h5>

                                <!-- DETAIL ALAMAT & KETERANGAN KERUSAKAN -->
                                <div class="small text-muted mb-3">
                                    <?php if (!empty($row['alamat'])) : ?>
                                        <div class="mb-1">
                                            <i class="bi bi-geo-alt-fill text-danger me-1"></i> <strong>Alamat:</strong> <?= e($row['alamat']); ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($row['keterangan_kerusakan'])) : ?>
                                        <div class="mt-2 p-2 bg-warning bg-opacity-10 border-start border-3 border-warning text-dark rounded small">
                                            <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i>
                                            <strong>Kerusakan:</strong> <?= e($row['keterangan_kerusakan']); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- FOTO SEBELUM & SESUDAH -->
                                <div class="row g-2 text-center mb-3 mt-auto">
                                    <div class="col-6">
                                        <small class="d-block text-muted fw-semibold mb-1">Kondisi Awal</small>
                                        <img src="<?= e(foto_url('assets/upload/usulan', $row['foto_usulan_1'])); ?>" class="img-preview border rounded w-100" alt="Foto Usulan" onerror="this.onerror=null;this.src='assets/no-image.svg'">
                                    </div>
                                    <div class="col-6">
                                        <small class="d-block text-muted fw-semibold mb-1">Realisasi</small>
                                        <img src="<?= e(foto_url('assets/upload/realisasi', $row['foto_hasil_1'])); ?>" class="img-preview border rounded w-100" alt="Foto Hasil" onerror="this.onerror=null;this.src='assets/no-image.svg'">
                                    </div>
                                </div>

                                <!-- TOMBOL MAPS KOORDINAT -->
                                <?php if (!empty($row['titik_koordinat'])) : ?>
                                    <a href="https://www.google.com/maps?q=<?= urlencode($row['titik_koordinat']); ?>" target="_blank" rel="noopener" class="btn btn-outline-primary btn-sm rounded-pill w-100 fw-semibold">
                                        <i class="bi bi-geo-alt me-1"></i> Lihat Titik Lokasi Maps
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else : ?>
                    <div class="col-12 text-center py-5">
                        <div class="card card-modern p-5">
                            <i class="bi bi-inbox text-muted display-4 mb-2"></i>
                            <p class="text-muted mb-0">Belum ada usulan fisik di <strong><?= e($rt_aktif); ?></strong> (Tahun <?= e($tahun_aktif); ?>).</p>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- 2. KATEGORI EKONOMI (UMKM & LPG) -->
            <?php elseif ($kategori_aktif == 'ekonomi') : ?>
                <?php if ($ada_data) : ?>
                    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card card-modern h-100 p-3 shadow-sm d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-warning text-dark border border-warning px-2 py-1 fw-bold">
                                        <i class="bi bi-shop me-1"></i><?= !empty($row['kategori']) ? e($row['kategori']) : 'UMKM'; ?>
                                    </span>
                                    <?php if (!empty($row['jenis_umkm'])) : ?>
                                        <span class="badge bg-light text-secondary border px-2 py-1"><?= e($row['jenis_umkm']); ?></span>
                                    <?php endif; ?>
                                </div>

                                <?php if (!empty($row['foto'])) : ?>
                                    <img src="<?= e(foto_url('assets/data_ekonomi', $row['foto'])); ?>" class="img-preview border mb-3 rounded" style="height: 160px;" alt="Foto Usaha" onerror="this.onerror=null;this.src='assets/no-image.svg'">
                                <?php endif; ?>

                                <h5 class="fw-bold text-dark mb-2"><?= e($row['nama_tempat']); ?></h5>

                                <div class="small text-muted mb-3">
                                    <div class="mb-1">
                                        <i class="bi bi-person-circle me-1 text-primary"></i>
                                        Pemilik: <strong><?= !empty($row['nama_pemilik']) ? e($row['nama_pemilik']) : '-'; ?></strong>
                                        <?php if (!empty($row['gender_pemilik'])) : ?>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary ms-1"><?= e($row['gender_pemilik']); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <?php if (!empty($row['no_hp'])) : ?>
                                        <div class="mb-1">
                                            <i class="bi bi-whatsapp me-1 text-success"></i>
                                            <a href="https://wa.me/<?= e(nomor_wa($row['no_hp'])); ?>" target="_blank" rel="noopener" class="text-decoration-none text-muted">
                                                <?= e($row['no_hp']); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($row['alamat'])) : ?>
                                        <div class="mb-1">
                                            <i class="bi bi-geo-alt-fill me-1 text-danger"></i> <?= e($row['alamat']); ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($row['keterangan'])) : ?>
                                        <div class="mt-2 p-2 bg-light rounded text-dark small border-start border-3 border-warning">
                                            <i class="bi bi-info-circle me-1 text-warning"></i> <?= e($row['keterangan']); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <?php if (!empty($row['titik_koordinat'])) : ?>
                                    <a href="https://www.google.com/maps?q=<?= urlencode($row['titik_koordinat']); ?>" target="_blank" rel="noopener" class="btn btn-outline-primary btn-sm rounded-pill mt-auto w-100 fw-semibold">
                                        <i class="bi bi-geo-alt me-1"></i> Lihat Titik Lokasi Maps
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else : ?>
                    <div class="col-12 text-center py-5">
                        <div class="card card-modern p-5">
                            <i class="bi bi-shop text-muted display-4 mb-2"></i>
                            <p class="text-muted mb-0">Belum ada data ekonomi/UMKM di <strong><?= e($rt_aktif); ?></strong> (Tahun <?= e($tahun_aktif); ?>).</p>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- 3. KATEGORI SOSIAL BUDAYA -->
            <?php elseif ($kategori_aktif == 'sosial') : ?>
                <?php if ($ada_data) : ?>
                    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                        <?php
                        // Ambil foto utama
                        $fotos = daftar_foto($row['foto'] ?? '', 'assets/data_sosial_budaya');
                        $cover = $fotos[0] ?? FOTO_KOSONG;
                        $modal_id = "modalSosial" . $row['id'];
                        ?>

                        <!-- CARD BISA DIKLIK -->
                        <div class="col-md-6 col-lg-4">
                            <div class="card card-modern h-100 p-3 shadow-sm d-flex flex-column style-card-hover"
                                style="cursor: pointer;"
                                data-bs-toggle="modal"
                                data-bs-target="#<?= $modal_id; ?>">

                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-2 py-1 fw-bold">
                                        <i class="bi bi-moon-stars me-1"></i><?= !empty($row['kategori']) ? e($row['kategori']) : 'Sosial Budaya'; ?>
                                    </span>
                                    <span class="badge bg-light text-primary border">
                                        <i class="bi bi-eye-fill me-1"></i>Lihat Profil
                                    </span>
                                </div>

                                <img src="<?= e($cover); ?>" class="img-preview border mb-3 rounded" style="height: 160px; object-fit: cover;" alt="<?= e($row['nama_tempat']); ?>" onerror="this.onerror=null;this.src='assets/no-image.svg'">

                                <h5 class="fw-bold text-dark mb-2"><?= e($row['nama_tempat']); ?></h5>
                                <p class="small text-secondary mb-0"><i class="bi bi-geo-alt-fill text-danger me-1"></i> <?= e($row['alamat']); ?></p>
                            </div>
                        </div>

                        <!-- MODAL POPUP PROFIL & SEJARAH -->
                        <div class="modal fade" id="<?= $modal_id; ?>" tabindex="-1" aria-labelledby="<?= $modal_id; ?>Label" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content border-0 shadow rounded-4">
                                    <div class="modal-header bg-primary text-white rounded-top-4">
                                        <h5 class="modal-title fw-bold" id="<?= $modal_id; ?>Label">
                                            <i class="bi bi-bank me-2"></i><?= e($row['nama_tempat']); ?>
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="row g-4">
                                            <div class="col-md-5">
                                                <img src="<?= e($cover); ?>" class="img-fluid rounded-3 border shadow-sm w-100" style="max-height: 250px; object-fit: cover;" alt="<?= e($row['nama_tempat']); ?>" onerror="this.onerror=null;this.src='assets/no-image.svg'">
                                                <div class="mt-3 small text-muted">
                                                    <p class="mb-1"><i class="bi bi-geo-alt-fill text-danger me-1"></i><strong>Lokasi:</strong> <?= e($row['alamat']); ?></p>
                                                    <p class="mb-0"><i class="bi bi-building me-1 text-primary"></i><strong>Wilayah:</strong> <?= e($row['rt']); ?></p>
                                                </div>
                                            </div>
                                            <div class="col-md-7">
                                                <h6 class="fw-bold text-primary mb-2"><i class="bi bi-journal-text me-1"></i> Profil & Sejarah Singkat</h6>
                                                <div class="p-3 bg-light rounded-3 border border-start-0 style-scroll" style="max-height: 200px; overflow-y: auto;">
                                                    <p class="text-dark small mb-0" style="line-height: 1.7; text-align: justify;">
                                                        <?= !empty($row['profil']) ? nl2br(e($row['profil'])) : 'Belum ada data profil atau sejarah singkat untuk tempat ini.'; ?>
                                                    </p>
                                                </div>

                                                <?php if (!empty($row['latitude']) && !empty($row['longitude'])) : ?>
                                                    <a href="https://www.google.com/maps?q=<?= e($row['latitude']); ?>,<?= e($row['longitude']); ?>" target="_blank" rel="noopener" class="btn btn-primary rounded-pill w-100 fw-semibold mt-3">
                                                        <i class="bi bi-map-fill me-1"></i> Buka Petunjuk Lokasi Google Maps
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php endwhile; ?>
                <?php else : ?>
                    <div class="col-12 text-center py-5">
                        <div class="card card-modern p-5">
                            <i class="bi bi-moon-stars text-muted display-4 mb-2"></i>
                            <p class="text-muted mb-0">Belum ada data sosial budaya di <strong><?= e($rt_aktif); ?></strong> (Tahun <?= e($tahun_aktif); ?>).</p>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php include __DIR__ . '/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>