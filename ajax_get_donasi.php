<?php
include 'koneksi.php';

// Get username from query parameter
$username = $_GET['username'] ?? '';

$map = [
    'palestina' => 'Palestina',
    'papua' => 'Papua',
    'anak_yatim' => 'Anak Yatim',
    'bencana_alam' => 'Bencana Alam',
    'pendidikan' => 'Pendidikan',
    'kesehatan' => 'Kesehatan',
    'panti_asuhan' => 'Panti Asuhan',
    'masjid' => 'Masjid',
    'umum' => 'Umum'
];

$query = "SELECT donor_name, amount, phone, created_at, status, tujuan_donasi, is_anonim FROM donations WHERE username = '$username' ORDER BY created_at ASC";
$result = mysqli_query($koneksi, $query);

$donations = [];
while ($row = mysqli_fetch_assoc($result)) {
    $donations[] = [
        'donor_name' => $row['donor_name'],
        'amount' => $row['amount'],
        'phone' => strlen($row['phone']) > 4
            ? substr($row['phone'], 0, 4) . '****' . substr($row['phone'], -2)
            : $row['phone'],
        'created_at' => date('d/m/Y', strtotime($row['created_at'])),
        'status' => $row['status'],
        'tujuan_donasi' => $row['tujuan_donasi'],
        'is_anonim' => $row['is_anonim']
    ];
}

$today = date('Y-m-d');

// Ambil donatur terbaru hari ini berdasarkan username (status confirmed)
$latest_query = mysqli_query($koneksi, "SELECT donor_name, amount, created_at, is_anonim FROM donations WHERE username = '$username' AND status='confirmed' AND DATE(created_at) = '$today' ORDER BY created_at DESC LIMIT 1");

// Kalau gak ada donasi hari ini, ambil paling baru aja berdasarkan username
if (mysqli_num_rows($latest_query) == 0) {
    $latest_query = mysqli_query($koneksi, "SELECT donor_name, amount, created_at, is_anonim FROM donations WHERE username = '$username' AND status='confirmed' ORDER BY created_at DESC LIMIT 1");
}

// Siapkan data donatur terbaru
$latest = null;
if (mysqli_num_rows($latest_query) > 0) {
    $latest_row = mysqli_fetch_assoc($latest_query);

    $donorNameForDisplay = $latest_row['donor_name'];
    if ($latest_row['is_anonim'] == 1 || strtolower($donorNameForDisplay) == 'anonymous' || strtolower($donorNameForDisplay) == 'donatur anonim') {
        $donorNameForDisplay = 'Nono Yaa';
    }

    $latest = [
        'donor_name' => $donorNameForDisplay,
        'amount' => $latest_row['amount'],
        'created_at' => date('d F Y', strtotime($latest_row['created_at']))
    ];
}

header('Content-Type: application/json');
echo json_encode([
    'donations' => $donations,
    'latest' => $latest
]);
