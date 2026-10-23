<?php
/* ==========================================================
E-MUSANG - Fungsi bantu bersama (keamanan & utilitas)
Di-include setelah config/database.php di setiap halaman.
   ========================================================== */

// Ubah ke true HANYA saat development di komputer lokal
if (!defined('APP_DEBUG')) {
    define('APP_DEBUG', false);
}

ini_set('display_errors', APP_DEBUG ? '1' : '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

/* ---------- Escape output (cegah XSS) ---------- */
function e($teks)
{
    return htmlspecialchars((string)$teks, ENT_QUOTES, 'UTF-8');
}

/* ---------- Whitelist parameter URL ---------- */
const DAFTAR_TAHUN = ['2025', '2026', '2027'];

function daftar_rt()
{
    $rt = [];
    for ($i = 1; $i <= 23; $i++) {
        $rt[] = sprintf('RT %02d', $i);
    }
    return $rt;
}

function ambil_tahun()
{
    $t = $_GET['tahun'] ?? '2025';
    return in_array($t, DAFTAR_TAHUN, true) ? $t : '2025';
}

function ambil_rt()
{
    $r = $_GET['rt'] ?? 'RT 01';
    return in_array($r, daftar_rt(), true) ? $r : 'RT 01';
}

function ambil_kategori()
{
    $k = $_GET['kategori'] ?? 'fisik';
    return in_array($k, ['fisik', 'ekonomi', 'sosial'], true) ? $k : 'fisik';
}

/* ---------- Query aman dengan prepared statement ---------- */
function ambil_data($conn, $sql, $tipe = '', ...$param)
{
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        error_log('SQL prepare gagal: ' . mysqli_error($conn));
        return false;
    }
    if ($tipe !== '') {
        mysqli_stmt_bind_param($stmt, $tipe, ...$param);
    }
    if (!mysqli_stmt_execute($stmt)) {
        error_log('SQL execute gagal: ' . mysqli_stmt_error($stmt));
        return false;
    }
    return mysqli_stmt_get_result($stmt);
}

function hitung_total($conn, $tabel, $tahun)
{
    $boleh = ['usulan_fisik', 'data_ekonomi', 'data_sosial_budaya', 'data_dau', 'data_kerjasama_dinas', 'data_pemetaan_wilayah'];
    if (!in_array($tabel, $boleh, true)) return 0;

    $res = ambil_data($conn, "SELECT COUNT(*) AS total FROM $tabel WHERE tahun_rakorbang = ?", 'i', (int)$tahun);
    if (!$res) return 0;

    $row = mysqli_fetch_assoc($res);
    return (int)($row['total'] ?? 0);
}

/* ---------- Foto: path aman + placeholder lokal ---------- */
const FOTO_KOSONG = 'assets/no-image.svg';

function foto_url($folder, $file)
{
    $file = basename((string)$file); // cegah path traversal
    return $file !== '' ? $folder . '/' . rawurlencode($file) : FOTO_KOSONG;
}

/* ---------- Nomor WhatsApp: 08xx -> 628xx ---------- */
function nomor_wa($no)
{
    $angka = preg_replace('/[^0-9]/', '', (string)$no);
    if (strpos($angka, '0') === 0) {
        $angka = '62' . substr($angka, 1);
    }
    return $angka;
}

/* ---------- PDF Pemetaan Wilayah ---------- */
const FOLDER_PDF = 'assets/data_pemetaan';

function pdf_url($file)
{
    return FOLDER_PDF . '/' . rawurlencode(basename((string)$file));
}

function pdf_ada($file)
{
    $nama = basename((string)$file); // cegah path traversal
    if ($nama === '' || strtolower(pathinfo($nama, PATHINFO_EXTENSION)) !== 'pdf') return false;
    return is_file(__DIR__ . '/../' . FOLDER_PDF . '/' . $nama);
}

function ukuran_file($file)
{
    $path = __DIR__ . '/../' . FOLDER_PDF . '/' . basename((string)$file);
    if (!is_file($path)) return '';
    $b = filesize($path);
    return $b >= 1048576
        ? number_format($b / 1048576, 1, ',', '.') . ' MB'
        : number_format(max($b, 1) / 1024, 0, ',', '.') . ' KB';
}

/* ---------- Banyak foto dalam satu kolom (dipisah koma) ---------- */
// Contoh isi kolom: baksampah_1.jpg,baksampah_2.jpg,baksampah_3.jpg
// Mengembalikan daftar URL foto yang filenya benar-benar ada di folder.
function daftar_foto($teks, $folder)
{
    $hasil = [];
    foreach (explode(',', (string)$teks) as $nama) {
        $nama = basename(trim($nama)); // cegah path traversal
        if ($nama !== '' && is_file(__DIR__ . '/../' . $folder . '/' . $nama)) {
            $hasil[] = $folder . '/' . rawurlencode($nama);
        }
    }
    return $hasil;
}

/* ---------- Banyak PDF dalam satu kolom (dipisah koma) ---------- */
function format_ukuran($byte)
{
    return $byte >= 1048576
        ? number_format($byte / 1048576, 1, ',', '.') . ' MB'
        : number_format(max($byte, 1) / 1024, 0, ',', '.') . ' KB';
}

// Nama tampil dari nama file: "mou-dinas-kesehatan_a1b2c3d4.pdf" => "Mou Dinas Kesehatan"
function label_pdf($nama)
{
    $dasar = pathinfo(basename((string)$nama), PATHINFO_FILENAME);
    $dasar = preg_replace('/_[0-9a-f]{8}$/', '', $dasar);
    $label = ucwords(trim(preg_replace('/[-_]+/', ' ', $dasar)));
    return $label !== '' ? $label : 'Dokumen';
}

// Mengembalikan daftar PDF yang filenya benar-benar ada: [['url','label','ukuran'], ...]
function daftar_pdf($teks, $folder)
{
    $hasil = [];
    foreach (explode(',', (string)$teks) as $nama) {
        $nama = basename(trim($nama)); // cegah path traversal
        $path = __DIR__ . '/../' . $folder . '/' . $nama;
        if ($nama !== '' && strtolower(pathinfo($nama, PATHINFO_EXTENSION)) === 'pdf' && is_file($path)) {
            $hasil[] = [
                'url'    => $folder . '/' . rawurlencode($nama),
                'label'  => label_pdf($nama),
                'ukuran' => format_ukuran(filesize($path)),
            ];
        }
    }
    return $hasil;
}
