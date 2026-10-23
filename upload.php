<?php
/* ==========================================================
   E-MUSANG - Unggah foto yang aman
   Foto divalidasi, diputar sesuai EXIF, diperkecil, lalu
   disimpan ulang sebagai JPEG dengan nama acak.
   ========================================================== */

const FOTO_MAKS_UKURAN = 10485760; // 10 MB per foto (sebelum diperkecil)
const FOTO_MAKS_SISI   = 1600;     // sisi terpanjang setelah diperkecil (px)
const FOTO_MAKS_JUMLAH = 20;       // maksimal foto per kegiatan

// Merapikan $_FILES['kunci'] (input multiple) menjadi daftar file tunggal
function file_terunggah($kunci)
{
    $hasil = [];
    if (empty($_FILES[$kunci]) || !is_array($_FILES[$kunci]['name'])) return $hasil;

    foreach ($_FILES[$kunci]['name'] as $i => $nama) {
        if ($_FILES[$kunci]['error'][$i] === UPLOAD_ERR_NO_FILE) continue;
        $hasil[] = [
            'name'     => $nama,
            'tmp_name' => $_FILES[$kunci]['tmp_name'][$i],
            'error'    => $_FILES[$kunci]['error'][$i],
            'size'     => $_FILES[$kunci]['size'][$i],
        ];
    }
    return $hasil;
}

function pesan_error_unggah($kode)
{
    switch ($kode) {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            return 'melebihi batas ukuran file di server (upload_max_filesize = ' . ini_get('upload_max_filesize') . ')';
        case UPLOAD_ERR_PARTIAL:
            return 'terunggah sebagian, coba lagi';
        default:
            return 'gagal diunggah (kode ' . $kode . ')';
    }
}

// Mengembalikan ['ok' => true, 'nama' => 'file.jpg'] atau ['ok' => false, 'pesan' => '...']
function simpan_foto($file, $folder, $awalan)
{
    $label = basename((string)$file['name']);
    $gagal = function ($alasan) use ($label) {
        return ['ok' => false, 'pesan' => '"' . $label . '": ' . $alasan];
    };

    if ($file['error'] !== UPLOAD_ERR_OK) return $gagal(pesan_error_unggah($file['error']));
    if ($file['size'] > FOTO_MAKS_UKURAN)  return $gagal('lebih dari 10 MB');
    if (!is_uploaded_file($file['tmp_name'])) return $gagal('bukan file unggahan yang sah');

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']);
    if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
        return $gagal('format tidak didukung (gunakan JPG, PNG, atau WebP)');
    }

    $info = @getimagesize($file['tmp_name']);
    if (!$info) return $gagal('bukan gambar yang valid');
    if ($info[0] * $info[1] > 50000000) return $gagal('resolusi terlalu besar');

    @ini_set('memory_limit', '384M');

    if ($mime === 'image/jpeg') {
        $src = @imagecreatefromjpeg($file['tmp_name']);
    } elseif ($mime === 'image/png') {
        $src = @imagecreatefrompng($file['tmp_name']);
    } else {
        $src = function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($file['tmp_name']) : false;
    }
    if (!$src) return $gagal('gambar tidak dapat dibaca');

    // Putar sesuai orientasi kamera HP (EXIF)
    if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {
        $exif = @exif_read_data($file['tmp_name']);
        $o = $exif['Orientation'] ?? 1;
        $sudut = [3 => 180, 6 => -90, 8 => 90][$o] ?? 0;
        if ($sudut !== 0) {
            $putar = imagerotate($src, $sudut, 0);
            if ($putar) {
                imagedestroy($src);
                $src = $putar;
            }
        }
    }

    // Perkecil bila perlu
    $w = imagesx($src);
    $h = imagesy($src);
    $skala = min(1, FOTO_MAKS_SISI / max($w, $h));
    $bw = max(1, (int)round($w * $skala));
    $bh = max(1, (int)round($h * $skala));

    $dst = imagecreatetruecolor($bw, $bh);
    imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255)); // latar putih untuk PNG transparan
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $bw, $bh, $w, $h);

    $dir = __DIR__ . '/../' . $folder;
    if (!is_dir($dir) && !@mkdir($dir, 0755, true)) {
        imagedestroy($src);
        imagedestroy($dst);
        return $gagal('folder tujuan tidak dapat dibuat');
    }

    $nama = $awalan . '_' . bin2hex(random_bytes(6)) . '.jpg';
    $ok = imagejpeg($dst, $dir . '/' . $nama, 82);
    imagedestroy($src);
    imagedestroy($dst);

    return $ok ? ['ok' => true, 'nama' => $nama] : $gagal('gagal menyimpan file');
}

function hapus_foto($folder, $nama)
{
    $nama = basename((string)$nama);
    if ($nama === '') return;
    $path = __DIR__ . '/../' . $folder . '/' . $nama;
    if (is_file($path)) @unlink($path);
}

/* ---------- PDF ---------- */
const PDF_MAKS_UKURAN = 20971520; // 20 MB per PDF
const PDF_MAKS_JUMLAH = 10;       // maksimal PDF per kegiatan

// Mengembalikan ['ok' => true, 'nama' => 'file.pdf'] atau ['ok' => false, 'pesan' => '...']
function simpan_pdf($file, $folder)
{
    $label = basename((string)$file['name']);
    $gagal = function ($alasan) use ($label) {
        return ['ok' => false, 'pesan' => '"' . $label . '": ' . $alasan];
    };

    if ($file['error'] !== UPLOAD_ERR_OK) return $gagal(pesan_error_unggah($file['error']));
    if ($file['size'] > PDF_MAKS_UKURAN)   return $gagal('lebih dari 20 MB');
    if (!is_uploaded_file($file['tmp_name'])) return $gagal('bukan file unggahan yang sah');

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    if ($finfo->file($file['tmp_name']) !== 'application/pdf') return $gagal('bukan file PDF');
    if (file_get_contents($file['tmp_name'], false, null, 0, 5) !== '%PDF-') return $gagal('isi file bukan PDF yang valid');

    $dasar = strtolower(preg_replace('/[^A-Za-z0-9]+/', '-', pathinfo($label, PATHINFO_FILENAME)));
    $dasar = trim(substr($dasar, 0, 40), '-');
    if ($dasar === '') $dasar = 'dokumen';

    $dir = __DIR__ . '/../' . $folder;
    if (!is_dir($dir) && !@mkdir($dir, 0755, true)) return $gagal('folder tujuan tidak dapat dibuat');

    $nama = $dasar . '_' . bin2hex(random_bytes(4)) . '.pdf';
    if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $nama)) return $gagal('gagal menyimpan file');

    return ['ok' => true, 'nama' => $nama];
}
