<?php
include __DIR__ . '/config/database.php';
include __DIR__ . '/config/helpers.php';

const FOLDER_KERJASAMA = 'assets/data_kerjasama';

$tahun_aktif = ambil_tahun();

$result = ambil_data(
    $conn,
    "SELECT * FROM data_kerjasama_dinas WHERE tahun_rakorbang = ? ORDER BY id DESC",
    'i',
    (int)$tahun_aktif
);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kerjasama Dinas <?= e($tahun_aktif); ?> - E-MUSANG</title>
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

        <!-- BANNER HEADER -->
        <div class="hero-banner mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h3 class="fw-bold mb-1"><i class="bi bi-people-fill me-2"></i>Kerjasama Dinas Lain</h3>
                <p class="mb-0 opacity-90 fs-6">Program Kolaborasi & Bantuan SKPD / Instansi Kota Banjarmasin</p>
            </div>
            <div class="bg-white px-3 py-2 rounded-pill shadow-sm">
                <span class="fs-6 text-primary fw-bold">
                    <i class="bi bi-calendar-check me-1"></i> Periode Tahun <?= e($tahun_aktif); ?>
                </span>
            </div>
        </div>

        <!-- DAFTAR PROGRAM (klik kartu untuk melihat foto & dokumen) -->
        <div class="row g-4">
            <?php if ($result && mysqli_num_rows($result) > 0) : ?>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <?php
                    $bg_status = 'bg-info text-white'; // Rencana
                    if ($row['status'] == 'Selesai') $bg_status = 'bg-success text-white';
                    elseif ($row['status'] == 'Berjalan') $bg_status = 'bg-warning text-dark';

                    // Kolom foto & file_pdf boleh berisi banyak nama file dipisah koma.
                    $fotos = daftar_foto($row['foto'] ?? '', FOLDER_KERJASAMA);
                    $pdfs  = daftar_pdf($row['file_pdf'] ?? '', FOLDER_KERJASAMA);
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card card-modern card-klik h-100 p-3 d-flex flex-column" role="button" tabindex="0"
                            data-judul="<?= e($row['nama_program']); ?>"
                            data-dinas="<?= e($row['nama_dinas']); ?>"
                            data-status="<?= e($row['status']); ?>"
                            data-foto="<?= e(json_encode($fotos)); ?>"
                            data-pdf="<?= e(json_encode($pdfs, JSON_UNESCAPED_UNICODE)); ?>">

                            <!-- DINAS & STATUS -->
                            <div class="d-flex justify-content-between align-items-start mb-2 gap-2">
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-2 py-1 fw-bold text-start text-wrap">
                                    <i class="bi bi-building me-1"></i><?= e($row['nama_dinas']); ?>
                                </span>
                                <?php if (!empty($row['status'])) : ?>
                                    <span class="badge <?= $bg_status; ?> rounded-pill px-3 py-1 flex-shrink-0"><?= e($row['status']); ?></span>
                                <?php endif; ?>
                            </div>

                            <!-- SAMPUL + JUMLAH FOTO & PDF -->
                            <div class="dau-cover mb-3">
                                <?php if ($fotos) : ?>
                                    <img src="<?= e($fotos[0]); ?>" class="img-dau border" alt="<?= e($row['nama_program']); ?>" onerror="this.onerror=null;this.src='assets/no-image.svg'">
                                <?php else : ?>
                                    <div class="cover-kosong">
                                        <i class="bi <?= $pdfs ? 'bi-file-earmark-pdf' : 'bi-image'; ?>"></i>
                                        <span><?= $pdfs ? 'Dokumen tersedia' : 'Belum ada foto'; ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if (count($pdfs) > 0) : ?>
                                    <span class="badge-pdf"><i class="bi bi-file-earmark-pdf me-1"></i><?= count($pdfs); ?> PDF</span>
                                <?php endif; ?>
                                <?php if (count($fotos) > 0) : ?>
                                    <span class="badge-jumlah"><i class="bi bi-images me-1"></i><?= count($fotos); ?> foto</span>
                                <?php endif; ?>
                            </div>

                            <!-- NAMA PROGRAM -->
                            <h5 class="fw-bold text-dark mb-2"><?= e($row['nama_program']); ?></h5>

                            <div class="mt-auto small text-primary fw-semibold">
                                <i class="bi bi-eye me-1"></i> Klik untuk melihat foto &amp; dokumen
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else : ?>
                <div class="col-12 text-center py-5">
                    <div class="card card-modern p-5">
                        <i class="bi bi-diagram-3 text-muted display-4 mb-2"></i>
                        <p class="text-muted mb-0">Belum ada data program Kerjasama Dinas untuk <strong>Tahun <?= e($tahun_aktif); ?></strong>.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </div>

    <!-- MODAL DETAIL: galeri foto + dokumen PDF -->
    <div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="detailJudul" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 overflow-hidden">
                <div class="modal-header bg-dark text-white border-0">
                    <div>
                        <h5 class="modal-title fw-bold fs-6 mb-0" id="detailJudul">Detail Program</h5>
                        <small class="text-white-50" id="detailMeta"></small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body p-0">
                    <!-- Panel foto + daftar dokumen -->
                    <div id="panelFoto"></div>
                    <!-- Panel pratinjau PDF -->
                    <div id="panelPdf" class="d-none">
                        <div class="d-flex flex-wrap align-items-center gap-2 p-2 bg-light border-bottom">
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" id="pdfKembali"><i class="bi bi-arrow-left me-1"></i> Kembali</button>
                            <strong class="small flex-grow-1" id="pdfJudul"></strong>
                            <a id="pdfBuka" href="#" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary rounded-pill"><i class="bi bi-box-arrow-up-right me-1"></i> Buka di tab baru</a>
                        </div>
                        <iframe id="pdfFrame" class="pdf-frame" title="Pratinjau dokumen PDF"></iframe>
                        <div class="p-2 bg-light small text-muted">Jika PDF tidak tampil di perangkat Anda, gunakan <strong>Buka di tab baru</strong>.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function() {
            var modalEl = document.getElementById('modalDetail');
            var modal = new bootstrap.Modal(modalEl);
            var judulEl = document.getElementById('detailJudul');
            var metaEl = document.getElementById('detailMeta');
            var panelFoto = document.getElementById('panelFoto');
            var panelPdf = document.getElementById('panelPdf');
            var frame = document.getElementById('pdfFrame');
            var carousel = null;

            function buat(tag, kelas, teks) {
                var el = document.createElement(tag);
                if (kelas) el.className = kelas;
                if (teks) el.textContent = teks;
                return el;
            }

            function tampilFoto() {
                panelPdf.classList.add('d-none');
                panelFoto.classList.remove('d-none');
                frame.src = 'about:blank';
            }

            function tampilPdf(item) {
                panelFoto.classList.add('d-none');
                panelPdf.classList.remove('d-none');
                document.getElementById('pdfJudul').textContent = item.label;
                document.getElementById('pdfBuka').href = item.url;
                frame.src = item.url + '#view=FitH';
            }

            function bukaDetail(kartu) {
                var fotos = [],
                    pdfs = [];
                try {
                    fotos = JSON.parse(kartu.dataset.foto || '[]');
                } catch (err) {}
                try {
                    pdfs = JSON.parse(kartu.dataset.pdf || '[]');
                } catch (err) {}

                judulEl.textContent = kartu.dataset.judul;
                metaEl.textContent = [kartu.dataset.dinas, kartu.dataset.status].filter(Boolean).join(' · ');
                panelFoto.textContent = '';
                tampilFoto();

                if (fotos.length === 0 && pdfs.length === 0) {
                    panelFoto.appendChild(buat('div', 'text-center text-muted py-5', 'Belum ada foto atau dokumen untuk program ini.'));
                    modal.show();
                    return;
                }

                /* ---- Galeri foto ---- */
                if (fotos.length > 0) {
                    var car = buat('div', 'carousel slide');
                    car.id = 'carouselKerjasama';
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
                            tombol.setAttribute('data-bs-target', '#carouselKerjasama');
                            tombol.setAttribute('data-bs-slide', arah);
                            var ikon = buat('span', 'carousel-control-' + arah + '-icon');
                            ikon.setAttribute('aria-hidden', 'true');
                            tombol.appendChild(ikon);
                            tombol.appendChild(buat('span', 'visually-hidden', arah === 'prev' ? 'Sebelumnya' : 'Berikutnya'));
                            car.appendChild(tombol);
                        });
                    }
                    panelFoto.appendChild(car);
                    carousel = new bootstrap.Carousel(car, {
                        interval: false,
                        ride: false,
                        wrap: true
                    });

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
                    panelFoto.appendChild(bawah);

                    car.addEventListener('slid.bs.carousel', function(ev) {
                        nomor.textContent = 'Foto ' + (ev.to + 1) + ' dari ' + fotos.length;
                        thumbs.forEach(function(t, i) {
                            t.classList.toggle('aktif', i === ev.to);
                        });
                    });
                }

                /* ---- Daftar dokumen PDF ---- */
                if (pdfs.length > 0) {
                    var kotak = buat('div', 'p-3');
                    kotak.appendChild(buat('h6', 'fw-bold mb-2', 'Dokumen PDF'));
                    var daftar = buat('div', 'list-group');
                    pdfs.forEach(function(item) {
                        var baris = buat('div', 'list-group-item d-flex justify-content-between align-items-center flex-wrap gap-2');
                        var kiri = buat('div', 'd-flex align-items-center gap-2');
                        kiri.appendChild(buat('i', 'bi bi-file-earmark-pdf-fill text-danger fs-4'));
                        var teks = buat('div');
                        teks.appendChild(buat('div', 'fw-semibold', item.label));
                        teks.appendChild(buat('small', 'text-muted', 'PDF · ' + item.ukuran));
                        kiri.appendChild(teks);
                        baris.appendChild(kiri);

                        var kanan = buat('div', 'd-flex gap-2');
                        var lihat = buat('button', 'btn btn-blue btn-sm rounded-pill fw-semibold', 'Lihat');
                        lihat.type = 'button';
                        lihat.addEventListener('click', function() {
                            tampilPdf(item);
                        });
                        var unduh = buat('a', 'btn btn-outline-primary btn-sm rounded-pill fw-semibold', 'Unduh');
                        unduh.href = item.url;
                        unduh.setAttribute('download', '');
                        kanan.appendChild(lihat);
                        kanan.appendChild(unduh);
                        baris.appendChild(kanan);
                        daftar.appendChild(baris);
                    });
                    kotak.appendChild(daftar);
                    panelFoto.appendChild(kotak);
                }

                modal.show();
            }

            document.querySelectorAll('.card-klik').forEach(function(kartu) {
                kartu.addEventListener('click', function() {
                    bukaDetail(kartu);
                });
                kartu.addEventListener('keydown', function(ev) {
                    if (ev.key === 'Enter' || ev.key === ' ') {
                        ev.preventDefault();
                        bukaDetail(kartu);
                    }
                });
            });

            document.getElementById('pdfKembali').addEventListener('click', tampilFoto);

            modalEl.addEventListener('hidden.bs.modal', function() {
                if (carousel) {
                    carousel.dispose();
                    carousel = null;
                }
                panelFoto.textContent = '';
                tampilFoto();
            });
        })();
    </script>
</body>

</html>