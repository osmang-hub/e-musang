<?php
include __DIR__ . '/config/database.php';
include __DIR__ . '/config/helpers.php';

$tahun_aktif = ambil_tahun();

$result = ambil_data(
    $conn,
    "SELECT * FROM data_dau WHERE tahun_rakorbang = ? ORDER BY id DESC",
    'i',
    (int)$tahun_aktif
);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dana Alokasi Umum (DAU) <?= e($tahun_aktif); ?> - E-MUSANG</title>
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

        <!-- BANNER HEADER DAU -->
        <div class="hero-banner mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h3 class="fw-bold mb-1"><i class="bi bi-cash-stack me-2"></i>Dana Alokasi Umum (DAU)</h3>
                <p class="mb-0 opacity-90 fs-6">Transparansi Pengadaan, Pembangunan, dan Sosialisasi Tingkat Kelurahan</p>
            </div>
            <div class="bg-white px-3 py-2 rounded-pill shadow-sm">
                <span class="fs-6 text-primary fw-bold">
                    <i class="bi bi-calendar-check me-1"></i> Periode Tahun <?= e($tahun_aktif); ?>
                </span>
            </div>
        </div>

        <!-- DAFTAR KEGIATAN DAU (klik kartu untuk melihat semua foto) -->
        <div class="row g-4">
            <?php if ($result && mysqli_num_rows($result) > 0) : ?>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <?php
                    $bg_status = 'bg-secondary'; // Terencana
                    if ($row['status'] == 'Selesai') $bg_status = 'bg-success';
                    elseif ($row['status'] == 'Proses Pengerjaan') $bg_status = 'bg-warning text-dark';

                    // Foto: kolom "foto" boleh berisi banyak nama file dipisah koma.
                    // Foto pertama menjadi sampul kartu.
                    $fotos = daftar_foto($row['foto'], 'assets/data_dau');
                    $cover = $fotos[0] ?? FOTO_KOSONG;

                    // Judul kartu = nama kegiatan. Jika kosong, pakai jenis kegiatan.
                    $judul = !empty($row['nama_kegiatan']) ? $row['nama_kegiatan'] : ($row['jenis_kegiatan'] ?? 'Kegiatan DAU');
                    $jenis = (!empty($row['nama_kegiatan']) && !empty($row['jenis_kegiatan'])) ? $row['jenis_kegiatan'] : '';
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card card-modern card-klik h-100 p-3 d-flex flex-column" role="button" tabindex="0"
                            data-judul="<?= e($judul); ?>"
                            data-jenis="<?= e($jenis); ?>"
                            data-status="<?= e($row['status']); ?>"
                            data-foto="<?= e(json_encode($fotos)); ?>">

                            <!-- BADGE JENIS KEGIATAN & STATUS -->
                            <div class="d-flex justify-content-between align-items-center mb-2 gap-2">
                                <?php if ($jenis !== '') : ?>
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-2 py-1 fw-bold">
                                        <i class="bi bi-tag-fill me-1"></i><?= e($jenis); ?>
                                    </span>
                                <?php else : ?>
                                    <span></span>
                                <?php endif; ?>
                                <?php if (!empty($row['status'])) : ?>
                                    <span class="badge <?= $bg_status; ?> rounded-pill px-3 py-1"><?= e($row['status']); ?></span>
                                <?php endif; ?>
                            </div>

                            <!-- FOTO SAMPUL + JUMLAH FOTO -->
                            <div class="dau-cover mb-3">
                                <img src="<?= e($cover); ?>" class="img-dau border" alt="<?= e($judul); ?>" onerror="this.onerror=null;this.src='assets/no-image.svg'">
                                <?php if (count($fotos) > 0) : ?>
                                    <span class="badge-jumlah"><i class="bi bi-images me-1"></i><?= count($fotos); ?> foto</span>
                                <?php endif; ?>
                            </div>

                            <!-- NAMA KEGIATAN -->
                            <h5 class="fw-bold text-dark mb-2"><?= e($judul); ?></h5>

                            <div class="mt-auto small text-primary fw-semibold">
                                <i class="bi bi-images me-1"></i> Klik untuk melihat foto kegiatan
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else : ?>
                <div class="col-12 text-center py-5">
                    <div class="card card-modern p-5">
                        <i class="bi bi-cash-stack text-muted display-4 mb-2"></i>
                        <p class="text-muted mb-0">Belum ada data pengadaan/sosialisasi DAU untuk <strong>Tahun <?= e($tahun_aktif); ?></strong>.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- MODAL GALERI FOTO -->
    <div class="modal fade" id="modalGaleri" tabindex="-1" aria-labelledby="galeriJudul" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 overflow-hidden">
                <div class="modal-header bg-dark text-white border-0">
                    <div>
                        <h5 class="modal-title fw-bold fs-6 mb-0" id="galeriJudul">Foto Kegiatan</h5>
                        <small class="text-white-50" id="galeriMeta"></small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body p-0" id="galeriIsi"></div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function() {
            var modalEl = document.getElementById('modalGaleri');
            var modal = new bootstrap.Modal(modalEl);
            var judulEl = document.getElementById('galeriJudul');
            var metaEl = document.getElementById('galeriMeta');
            var isiEl = document.getElementById('galeriIsi');
            var carousel = null;

            function buat(tag, kelas, teks) {
                var el = document.createElement(tag);
                if (kelas) el.className = kelas;
                if (teks) el.textContent = teks;
                return el;
            }

            function bukaGaleri(kartu) {
                var fotos = [];
                try {
                    fotos = JSON.parse(kartu.dataset.foto || '[]');
                } catch (err) {
                    fotos = [];
                }

                judulEl.textContent = kartu.dataset.judul;
                metaEl.textContent = [kartu.dataset.jenis, kartu.dataset.status].filter(Boolean).join(' · ');
                isiEl.textContent = '';

                if (fotos.length === 0) {
                    isiEl.appendChild(buat('div', 'text-center text-muted py-5', 'Belum ada foto dokumentasi untuk kegiatan ini.'));
                    modal.show();
                    return;
                }

                // Carousel foto
                var car = buat('div', 'carousel slide');
                car.id = 'carouselDau';
                var inner = buat('div', 'carousel-inner');
                fotos.forEach(function(src, i) {
                    var item = buat('div', 'carousel-item' + (i === 0 ? ' active' : ''));
                    var img = buat('img', 'd-block galeri-img');
                    img.src = src;
                    img.alt = 'Foto ' + (i + 1);
                    item.appendChild(img);
                    inner.appendChild(item);
                });
                car.appendChild(inner);

                if (fotos.length > 1) {
                    ['prev', 'next'].forEach(function(arah) {
                        var tombol = buat('button', 'carousel-control-' + arah);
                        tombol.type = 'button';
                        tombol.setAttribute('data-bs-target', '#carouselDau');
                        tombol.setAttribute('data-bs-slide', arah);
                        var ikon = buat('span', 'carousel-control-' + arah + '-icon');
                        ikon.setAttribute('aria-hidden', 'true');
                        tombol.appendChild(ikon);
                        tombol.appendChild(buat('span', 'visually-hidden', arah === 'prev' ? 'Sebelumnya' : 'Berikutnya'));
                        car.appendChild(tombol);
                    });
                }
                isiEl.appendChild(car);
                carousel = new bootstrap.Carousel(car, {
                    interval: false,
                    ride: false,
                    wrap: true
                });

                // Keterangan nomor + gambar kecil
                var bawah = buat('div', 'bg-light p-2 text-center');
                var nomor = buat('div', 'small text-muted mb-2', 'Foto 1 dari ' + fotos.length);
                bawah.appendChild(nomor);

                var thumbs = [];
                if (fotos.length > 1) {
                    var baris = buat('div', 'd-flex flex-wrap justify-content-center gap-2');
                    fotos.forEach(function(src, i) {
                        var t = buat('img', 'galeri-thumb' + (i === 0 ? ' aktif' : ''));
                        t.src = src;
                        t.alt = 'Foto ' + (i + 1);
                        t.addEventListener('click', function() {
                            carousel.to(i);
                        });
                        thumbs.push(t);
                        baris.appendChild(t);
                    });
                    bawah.appendChild(baris);
                }
                isiEl.appendChild(bawah);

                car.addEventListener('slid.bs.carousel', function(ev) {
                    nomor.textContent = 'Foto ' + (ev.to + 1) + ' dari ' + fotos.length;
                    thumbs.forEach(function(t, i) {
                        t.classList.toggle('aktif', i === ev.to);
                    });
                });

                modal.show();
            }

            document.querySelectorAll('.card-klik').forEach(function(kartu) {
                kartu.addEventListener('click', function() {
                    bukaGaleri(kartu);
                });
                kartu.addEventListener('keydown', function(ev) {
                    if (ev.key === 'Enter' || ev.key === ' ') {
                        ev.preventDefault();
                        bukaGaleri(kartu);
                    }
                });
            });

            modalEl.addEventListener('hidden.bs.modal', function() {
                if (carousel) {
                    carousel.dispose();
                    carousel = null;
                }
                isiEl.textContent = '';
            });
        })();
    </script>
</body>

</html>