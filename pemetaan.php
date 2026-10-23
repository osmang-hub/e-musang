<?php
include __DIR__ . '/config/database.php';
include __DIR__ . '/config/helpers.php';

$tahun_aktif = ambil_tahun();

$result = ambil_data(
    $conn,
    "SELECT * FROM data_pemetaan_wilayah WHERE tahun_rakorbang = ? ORDER BY id DESC",
    'i',
    (int)$tahun_aktif
);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemetaan Wilayah <?= e($tahun_aktif); ?> - E-MUSANG</title>
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
                <h3 class="fw-bold mb-1"><i class="bi bi-map-fill me-2"></i>Pemetaan Wilayah</h3>
                <p class="mb-0 opacity-90 fs-6">Dokumen peta dan pemetaan wilayah Kelurahan Antasan Kecil Timur</p>
            </div>
            <div class="bg-white px-3 py-2 rounded-pill shadow-sm">
                <span class="fs-6 text-primary fw-bold">
                    <i class="bi bi-calendar-check me-1"></i> Periode Tahun <?= e($tahun_aktif); ?>
                </span>
            </div>
        </div>

        <!-- DAFTAR DOKUMEN PEMETAAN -->
        <div class="row g-4">
            <?php if ($result && mysqli_num_rows($result) > 0) : ?>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <?php
                    $ada_pdf = pdf_ada($row['file_pdf']);
                    $url_pdf = pdf_url($row['file_pdf']);
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card card-modern h-100 p-3 d-flex flex-column">

                            <div class="d-flex align-items-start gap-3 mb-3">
                                <div class="icon-box bg-danger bg-opacity-10 text-danger flex-shrink-0">
                                    <i class="bi bi-file-earmark-pdf-fill"></i>
                                </div>
                                <div>
                                    <?php if (!empty($row['wilayah'])) : ?>
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-2 py-1 fw-bold mb-1">
                                            <i class="bi bi-geo-alt-fill me-1"></i><?= e($row['wilayah']); ?>
                                        </span>
                                    <?php endif; ?>
                                    <h5 class="fw-bold text-dark mb-0"><?= e($row['judul']); ?></h5>
                                </div>
                            </div>

                            <?php if (!empty($row['keterangan'])) : ?>
                                <div class="mb-3 p-2 bg-light rounded text-dark small border-start border-3 border-primary">
                                    <i class="bi bi-info-circle me-1 text-primary"></i> <?= e($row['keterangan']); ?>
                                </div>
                            <?php endif; ?>

                            <div class="mt-auto pt-3 border-top">
                                <?php if ($ada_pdf) : ?>
                                    <small class="text-muted d-block mb-2">
                                        <i class="bi bi-file-earmark me-1"></i>PDF &middot; <?= e(ukuran_file($row['file_pdf'])); ?>
                                    </small>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-blue rounded-pill btn-sm fw-semibold flex-fill btn-lihat-pdf"
                                            data-judul="<?= e($row['judul']); ?>" data-pdf="<?= e($url_pdf); ?>">
                                            <i class="bi bi-eye me-1"></i> Lihat PDF
                                        </button>
                                        <a href="<?= e($url_pdf); ?>" download class="btn btn-outline-primary rounded-pill btn-sm fw-semibold">
                                            <i class="bi bi-download me-1"></i> Unduh
                                        </a>
                                    </div>
                                <?php else : ?>
                                    <span class="badge bg-light text-muted border px-3 py-2">
                                        <i class="bi bi-exclamation-circle me-1"></i> File PDF belum tersedia
                                    </span>
                                <?php endif; ?>
                            </div>

                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else : ?>
                <div class="col-12 text-center py-5">
                    <div class="card card-modern p-5">
                        <i class="bi bi-map text-muted display-4 mb-2"></i>
                        <p class="text-muted mb-0">Belum ada dokumen pemetaan wilayah untuk <strong>Tahun <?= e($tahun_aktif); ?></strong>.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- MODAL PRATINJAU PDF -->
    <div class="modal fade" id="modalPdf" tabindex="-1" aria-labelledby="modalPdfJudul" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 overflow-hidden">
                <div class="modal-header bg-dark text-white border-0">
                    <h5 class="modal-title fw-bold fs-6" id="modalPdfJudul">Pratinjau PDF</h5>
                    <a id="modalPdfBuka" href="#" target="_blank" rel="noopener" class="btn btn-sm btn-outline-light rounded-pill ms-auto me-3">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Buka di tab baru
                    </a>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body p-0">
                    <iframe id="modalPdfFrame" class="pdf-frame" title="Pratinjau dokumen PDF"></iframe>
                </div>
                <div class="modal-footer border-0 bg-light py-2">
                    <small class="text-muted">Jika PDF tidak tampil di perangkat Anda, gunakan tombol <strong>Buka di tab baru</strong> atau <strong>Unduh</strong>.</small>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // PDF dimuat hanya saat tombol "Lihat PDF" diklik (hemat kuota)
        (function() {
            var modalEl = document.getElementById('modalPdf');
            var frame = document.getElementById('modalPdfFrame');
            var modal = new bootstrap.Modal(modalEl);

            document.querySelectorAll('.btn-lihat-pdf').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    document.getElementById('modalPdfJudul').textContent = btn.dataset.judul;
                    document.getElementById('modalPdfBuka').href = btn.dataset.pdf;
                    frame.src = btn.dataset.pdf + '#view=FitH';
                    modal.show();
                });
            });

            modalEl.addEventListener('hidden.bs.modal', function() {
                frame.src = 'about:blank';
            });
        })();
    </script>
</body>

</html>
