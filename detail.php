<?php
// Aktifkan error reporting untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Mulai session jika belum
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah ini request untuk scorecard setup
if (isset($_GET['action']) && $_GET['action'] == 'scorecard') {
    // Include file koneksi database
    try {
        include 'panggil.php';
    } catch (Exception $e) {
        die("Error koneksi database: " . $e->getMessage());
    }

    // Ambil parameter
    $kegiatan_id = isset($_GET['kegiatan_id']) ? intval($_GET['kegiatan_id']) : null;
    $category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : null;

    if (!$kegiatan_id || !$category_id) {
        die("Parameter kegiatan_id dan category_id harus diisi.");
    }

    // Ambil data kegiatan
    $kegiatanData = [];
    try {
        $queryKegiatan = "SELECT id, nama_kegiatan FROM kegiatan WHERE id = ?";
        $stmtKegiatan = $conn->prepare($queryKegiatan);
        $stmtKegiatan->bind_param("i", $kegiatan_id);
        $stmtKegiatan->execute();
        $resultKegiatan = $stmtKegiatan->get_result();
        
        if ($resultKegiatan->num_rows > 0) {
            $kegiatanData = $resultKegiatan->fetch_assoc();
        } else {
            die("Kegiatan tidak ditemukan.");
        }
        $stmtKegiatan->close();
    } catch (Exception $e) {
        die("Error mengambil data kegiatan: " . $e->getMessage());
    }

    // Ambil data kategori
    $kategoriData = [];
    try {
        $queryKategori = "SELECT id, name FROM categories WHERE id = ?";
        $stmtKategori = $conn->prepare($queryKategori);
        $stmtKategori->bind_param("i", $category_id);
        $stmtKategori->execute();
        $resultKategori = $stmtKategori->get_result();
        
        if ($resultKategori->num_rows > 0) {
            $kategoriData = $resultKategori->fetch_assoc();
        } else {
            die("Kategori tidak ditemukan.");
        }
        $stmtKategori->close();
    } catch (Exception $e) {
        die("Error mengambil data kategori: " . $e->getMessage());
    }

    // Ambil data peserta berdasarkan kegiatan dan kategori
    $pesertaList = [];
    try {
        $queryPeserta = "
            SELECT 
                p.id,
                p.nama_peserta,
                p.jenis_kelamin,
                c.name as category_name
            FROM peserta p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.kegiatan_id = ? AND p.category_id = ?
            ORDER BY p.nama_peserta ASC
        ";
        $stmtPeserta = $conn->prepare($queryPeserta);
        $stmtPeserta->bind_param("ii", $kegiatan_id, $category_id);
        $stmtPeserta->execute();
        $resultPeserta = $stmtPeserta->get_result();
        
        while ($row = $resultPeserta->fetch_assoc()) {
            $pesertaList[] = $row;
        }
        $stmtPeserta->close();
    } catch (Exception $e) {
        die("Error mengambil data peserta: " . $e->getMessage());
    }

    // Tutup koneksi database
    $conn->close();
    
    // BAGIAN SCORECARD SETUP
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Setup Scorecard Panahan - <?= htmlspecialchars($kategoriData['name']) ?></title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                background: linear-gradient(135deg, #2D3436 0%, #636e72 100%);
                min-height: 100vh;
                padding: 20px;
                color: white;
            }

            .container {
                max-width: 500px;
                margin: 0 auto;
            }

            .back-btn {
                background: rgba(255, 255, 255, 0.1);
                border: none;
                color: white;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                margin-bottom: 20px;
                transition: background 0.3s ease;
            }

            .back-btn:hover {
                background: rgba(255, 255, 255, 0.2);
            }

            .setup-form {
                background: rgba(45, 52, 54, 0.95);
                border-radius: 20px;
                padding: 30px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
                margin-bottom: 20px;
            }

            .header {
                text-align: center;
                margin-bottom: 30px;
            }

            .logo {
                width: 60px;
                height: 60px;
                background: linear-gradient(135deg, #fdcb6e 0%, #e17055 100%);
                border-radius: 15px;
                margin: 0 auto 20px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 30px;
            }

            .title {
                font-size: 20px;
                font-weight: 600;
                margin-bottom: 10px;
            }

            .subtitle {
                font-size: 14px;
                color: #ddd;
            }

            .category-info {
                background: rgba(116, 185, 255, 0.1);
                border: 1px solid rgba(116, 185, 255, 0.3);
                border-radius: 12px;
                padding: 15px;
                margin-bottom: 25px;
                text-align: center;
            }

            .category-name {
                font-size: 16px;
                font-weight: 600;
                color: #74b9ff;
                margin-bottom: 5px;
            }

            .event-name {
                font-size: 14px;
                color: #ddd;
            }

            .peserta-count {
                font-size: 18px;
                font-weight: 700;
                color: #fdcb6e;
                margin-top: 10px;
            }

            .form-group {
                margin-bottom: 25px;
            }

            .form-label {
                display: block;
                font-size: 16px;
                font-weight: 500;
                margin-bottom: 10px;
                color: #74b9ff;
            }

            .form-input {
                width: 100%;
                background: rgba(116, 185, 255, 0.1);
                border: 1px solid rgba(116, 185, 255, 0.3);
                border-radius: 12px;
                padding: 15px;
                color: white;
                font-size: 18px;
                text-align: center;
                transition: all 0.3s ease;
            }

            .form-input:focus {
                outline: none;
                border-color: #74b9ff;
                background: rgba(116, 185, 255, 0.15);
                box-shadow: 0 0 0 3px rgba(116, 185, 255, 0.1);
            }

            .create-btn {
                width: 100%;
                background: linear-gradient(135deg, #fdcb6e 0%, #e17055 100%);
                border: none;
                border-radius: 15px;
                padding: 16px;
                font-size: 16px;
                font-weight: 600;
                color: white;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .create-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(253, 203, 110, 0.3);
            }

            .create-btn:disabled {
                opacity: 0.6;
                cursor: not-allowed;
                transform: none;
            }

            /* Alert/Warning styles */
            .alert {
                padding: 15px;
                border-radius: 8px;
                margin-bottom: 20px;
            }

            .alert-warning {
                background: rgba(255, 193, 7, 0.1);
                border: 1px solid rgba(255, 193, 7, 0.3);
                color: #ffc107;
            }

            /* Scorecard Styles */
            .scorecard-container {
                background: rgba(45, 52, 54, 0.95);
                border-radius: 20px;
                padding: 20px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
                display: none;
                max-width: none;
                width: 100%;
                max-width: 1200px;
            }

            .scorecard-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 20px;
                background: rgba(116, 185, 255, 0.1);
                padding: 15px;
                border-radius: 12px;
                flex-wrap: wrap;
                gap: 10px;
            }

            .category-header-info {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .category-icon {
                width: 30px;
                height: 30px;
                background: #fdcb6e;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 16px;
            }

            .scorecard-title {
                text-align: center;
                font-size: 16px;
                font-weight: 600;
                margin-bottom: 15px;
                background: rgba(0, 0, 0, 0.3);
                padding: 10px;
                border-radius: 8px;
            }

            /* Player sections */
            .player-section {
                margin-bottom: 30px;
                background: rgba(0, 0, 0, 0.2);
                border-radius: 12px;
                padding: 20px;
            }

            .player-header {
                font-size: 16px;
                font-weight: 600;
                margin-bottom: 15px;
                color: #74b9ff;
                text-align: center;
                background: rgba(116, 185, 255, 0.1);
                padding: 10px;
                border-radius: 8px;
            }

            /* Horizontal Sessions Layout */
            .sessions-container {
                display: flex;
                gap: 10px;
                overflow-x: auto;
                padding-bottom: 10px;
                margin-bottom: 15px;
            }

            .session-card {
                min-width: 80px;
                background: rgba(116, 185, 255, 0.1);
                border: 1px solid rgba(116, 185, 255, 0.3);
                border-radius: 12px;
                padding: 12px;
                text-align: center;
                flex-shrink: 0;
            }

            .session-header {
                font-size: 14px;
                font-weight: 600;
                color: #74b9ff;
                margin-bottom: 10px;
                padding: 8px;
                background: rgba(116, 185, 255, 0.2);
                border-radius: 8px;
            }

            .arrows-container {
                display: flex;
                flex-direction: column;
                gap: 6px;
            }

            .arrow-input {
                background: rgba(255, 255, 255, 0.05);
                border: 1px solid rgba(255, 255, 255, 0.2);
                border-radius: 6px;
                padding: 8px;
                color: white;
                text-align: center;
                font-size: 14px;
                width: 100%;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .arrow-input:hover {
                background: rgba(116, 185, 255, 0.1);
                border-color: #74b9ff;
            }

            .arrow-input:focus {
                outline: none;
                border-color: #74b9ff;
                background: rgba(116, 185, 255, 0.15);
            }

            .session-total {
                margin-top: 10px;
                padding: 8px;
                background: rgba(253, 203, 110, 0.2);
                border-radius: 6px;
                font-weight: 600;
                font-size: 14px;
                color: #fdcb6e;
            }

            .total-summary {
                background: rgba(0, 184, 148, 0.1);
                border: 1px solid rgba(0, 184, 148, 0.3);
                border-radius: 12px;
                padding: 15px;
                text-align: center;
                margin-bottom: 20px;
            }

            .grand-total {
                font-size: 18px;
                font-weight: 700;
                color: #00b894;
            }

            .edit-btn {
                background: rgba(116, 185, 255, 0.2);
                border: 1px solid rgba(116, 185, 255, 0.5);
                color: white;
                padding: 8px 16px;
                border-radius: 8px;
                font-size: 12px;
                cursor: pointer;
                margin-top: 15px;
                width: 100%;
            }

            .edit-btn:hover {
                background: rgba(116, 185, 255, 0.3);
            }

            /* Responsive adjustments */
            @media (max-width: 768px) {
                .container {
                    max-width: 100%;
                    padding: 0 10px;
                }
                
                .sessions-container {
                    gap: 8px;
                }
                
                .session-card {
                    min-width: 70px;
                    padding: 10px;
                }
                
                .arrow-input {
                    padding: 6px;
                    font-size: 12px;
                }

                .scorecard-container {
                    padding: 15px;
                }

                .scorecard-header {
                    flex-direction: column;
                    text-align: center;
                }

                .category-header-info {
                    justify-content: center;
                }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <button class="back-btn" onclick="goBack()">←</button>

            <!-- Form Setup -->
            <div class="setup-form" id="setupForm">
                <div class="header">
                    <div class="logo">🏹</div>
                    <div class="title">Setup Scorecard</div>
                    <div class="subtitle">Atur jumlah sesi dan anak panah</div>
                </div>

                <!-- Info kategori dan peserta -->
                <div class="category-info">
                    <div class="category-name"><?= htmlspecialchars($kategoriData['name']) ?></div>
                    <div class="event-name"><?= htmlspecialchars($kegiatanData['nama_kegiatan']) ?></div>
                    <div class="peserta-count"><?= count($pesertaList) ?> Peserta Terdaftar</div>
                </div>

                <?php if (count($pesertaList) == 0): ?>
                    <div class="alert alert-warning">
                        <strong>Peringatan:</strong> Tidak ada peserta yang terdaftar dalam kategori ini.
                        Silakan pastikan ada peserta yang mendaftar terlebih dahulu.
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label class="form-label">Jumlah Sesi</label>
                    <input type="number" class="form-input" id="jumlahSesi" min="1" max="12" value="9" placeholder="9">
                </div>

                <div class="form-group">
                    <label class="form-label">Jumlah Anak Panah per Sesi</label>
                    <input type="number" class="form-input" id="jumlahPanah" min="1" max="10" value="3" placeholder="3">
                </div>

                <button class="create-btn" onclick="createScorecard()" <?= count($pesertaList) == 0 ? 'disabled' : '' ?>>
                    Buat Scorecard
                </button>
            </div>

            <!-- Scorecard Display -->
            <div class="scorecard-container" id="scorecardContainer">
                <div class="scorecard-header">
                    <div class="category-header-info">
                        <div class="category-icon">🎯</div>
                        <div>
                            <div class="category-name" style="font-size: 14px; margin: 0;"><?= htmlspecialchars($kategoriData['name']) ?></div>
                            <div style="font-size: 12px; color: #ddd;"><?= htmlspecialchars($kegiatanData['nama_kegiatan']) ?></div>
                        </div>
                    </div>
                    <div class="category-header-info">
                        <div class="category-icon">👥</div>
                        <div>
                            <div class="category-name" style="font-size: 14px; margin: 0;" id="pesertaCount"><?= count($pesertaList) ?></div>
                            <div style="font-size: 12px; color: #ddd;">Peserta</div>
                        </div>
                    </div>
                    <div class="category-header-info">
                        <div class="category-icon">🏹</div>
                        <div>
                            <div class="category-name" style="font-size: 14px; margin: 0;" id="panahCount">-</div>
                            <div style="font-size: 12px; color: #ddd;">Anak Panah</div>
                        </div>
                    </div>
                </div>

                <div class="scorecard-title">Informasi Skor</div>

                <!-- Dynamic player sections will be generated here -->
                <div id="playersContainer">
                    <!-- Player sections akan dimuat di sini oleh JavaScript -->
                </div>

                <button class="edit-btn" onclick="editScorecard()">
                    Edit Setup
                </button>
            </div>
        </div>

        <script>
            // Data peserta dari PHP
            const pesertaData = <?= json_encode($pesertaList) ?>;
            
            function goBack() {
                window.history.back();
            }

            function createScorecard() {
                const jumlahSesi = parseInt(document.getElementById('jumlahSesi').value);
                const jumlahPanah = parseInt(document.getElementById('jumlahPanah').value);

                if (!jumlahSesi || !jumlahPanah) {
                    alert('Mohon isi jumlah sesi dan anak panah');
                    return;
                }

                if (jumlahSesi > 12 || jumlahPanah > 10) {
                    alert('Maksimal 12 sesi dan 10 anak panah untuk tampilan optimal');
                    return;
                }

                if (pesertaData.length === 0) {
                    alert('Tidak ada peserta dalam kategori ini. Silakan pilih kategori lain atau tambah peserta.');
                    return;
                }

                // Update header counts
                document.getElementById('panahCount').textContent = jumlahSesi * jumlahPanah;

                // Generate player sections untuk setiap peserta
                generatePlayerSections(jumlahSesi, jumlahPanah);

                // Show scorecard, hide form
                document.getElementById('setupForm').style.display = 'none';
                document.getElementById('scorecardContainer').style.display = 'block';
                
                // Adjust container width untuk scorecard
                document.querySelector('.container').style.maxWidth = '1200px';
            }

            function generatePlayerSections(jumlahSesi, jumlahPanah) {
                const playersContainer = document.getElementById('playersContainer');
                playersContainer.innerHTML = '';

                // Generate section untuk setiap peserta
                pesertaData.forEach((peserta, index) => {
                    const playerId = `peserta_${peserta.id}`;
                    const playerName = peserta.nama_peserta;
                    
                    const playerSection = document.createElement('div');
                    playerSection.className = 'player-section';
                    playerSection.innerHTML = `
                        <div class="player-header">
                            ${playerName} (${peserta.jenis_kelamin})
                        </div>
                        <div class="sessions-container" id="${playerId}_sessions">
                            ${generateSessionCards(playerId, jumlahSesi, jumlahPanah)}
                        </div>
                        <div class="total-summary" id="${playerId}_summary">
                            <div style="font-size: 14px; margin-bottom: 8px;">Total Keseluruhan</div>
                            <div class="grand-total" id="${playerId}_grand_total">0 poin</div>
                        </div>
                    `;
                    
                    playersContainer.appendChild(playerSection);
                });
            }

            function generateSessionCards(playerId, jumlahSesi, jumlahPanah) {
                let sessionsHtml = '';
                
                for (let session = 1; session <= jumlahSesi; session++) {
                    const arrowsHtml = Array.from({length: jumlahPanah}, (_, arrow) => `
                        <input type="number" 
                               class="arrow-input" 
                               id="${playerId}_s${session}_a${arrow + 1}"
                               min="0" 
                               max="10" 
                               placeholder="${arrow + 1}"
                               oninput="updateSessionTotal('${playerId}', ${session}, ${jumlahSesi})"
                               onchange="this.blur()">
                    `).join('');
                    
                    sessionsHtml += `
                        <div class="session-card">
                            <div class="session-header">S${session}</div>
                            <div class="arrows-container" id="${playerId}_session_${session}">
                                ${arrowsHtml}
                            </div>
                            <div class="session-total" id="${playerId}_total_s${session}">Total: 0</div>
                        </div>
                    `;
                }
                
                return sessionsHtml;
            }

            function updateSessionTotal(playerId, session, totalSessions) {
                const sessionInputs = document.querySelectorAll(`input[id^="${playerId}_s${session}_a"]`);
                let sessionTotal = 0;
                
                sessionInputs.forEach(input => {
                    const value = parseInt(input.value) || 0;
                    if (value >= 0 && value <= 10) {
                        sessionTotal += value;
                        input.style.borderColor = value >= 8 ? '#00b894' : value >= 6 ? '#fdcb6e' : 'rgba(255, 255, 255, 0.2)';
                    } else if (input.value !== '') {
                        input.value = '';
                        input.style.borderColor = '#ff7675';
                    }
                });
                
                // Update session total
                const sessionTotalElement = document.getElementById(`${playerId}_total_s${session}`);
                if (sessionTotalElement) {
                    sessionTotalElement.textContent = `Total: ${sessionTotal}`;
                }
                
                // Update grand total
                updateGrandTotal(playerId, totalSessions);
            }

            function updateGrandTotal(playerId, totalSessions) {
                let grandTotal = 0;
                
                for (let session = 1; session <= totalSessions; session++) {
                    const sessionInputs = document.querySelectorAll(`input[id^="${playerId}_s${session}_a"]`);
                    sessionInputs.forEach(input => {
                        const value = parseInt(input.value) || 0;
                        grandTotal += value;
                    });
                }
                
                const grandTotalElement = document.getElementById(`${playerId}_grand_total`);
                if (grandTotalElement) {
                    grandTotalElement.textContent = `${grandTotal} poin`;
                }
            }

            function editScorecard() {
                document.getElementById('setupForm').style.display = 'block';
                document.getElementById('scorecardContainer').style.display = 'none';
                
                // Reset container width
                document.querySelector('.container').style.maxWidth = '500px';
            }
        </script>
    </body>
    </html>
    <?php
    exit;
}

// Cek apakah ini request untuk export Excel
if (isset($_GET['export']) && $_GET['export'] == 'excel') {
    // BAGIAN EXPORT EXCEL
    
    // Include file koneksi database
    try {
        include 'panggil.php';
    } catch (Exception $e) {
        die("Error koneksi database: " . $e->getMessage());
    }

    // Ambil parameter dari URL
    $kegiatan_id = isset($_GET['kegiatan_id']) ? intval($_GET['kegiatan_id']) : null;
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $filter_kategori = isset($_GET['filter_kategori']) ? intval($_GET['filter_kategori']) : 0;
    $filter_gender = isset($_GET['filter_gender']) ? $_GET['filter_gender'] : '';

    if (!$kegiatan_id) {
        die("ID Kegiatan tidak valid.");
    }

    // Ambil data kegiatan
    $kegiatanData = [];
    try {
        $queryKegiatan = "SELECT id, nama_kegiatan FROM kegiatan WHERE id = ?";
        $stmtKegiatan = $conn->prepare($queryKegiatan);
        $stmtKegiatan->bind_param("i", $kegiatan_id);
        $stmtKegiatan->execute();
        $resultKegiatan = $stmtKegiatan->get_result();
        
        if ($resultKegiatan->num_rows > 0) {
            $kegiatanData = $resultKegiatan->fetch_assoc();
        } else {
            die("Kegiatan tidak ditemukan.");
        }
        $stmtKegiatan->close();
    } catch (Exception $e) {
        die("Error mengambil data kegiatan: " . $e->getMessage());
    }

    // Query untuk mengambil data peserta dengan filter yang sama
    $whereConditions = ["p.kegiatan_id = ?"];
    $params = [$kegiatan_id];
    $types = "i";

    if (!empty($search)) {
        $whereConditions[] = "(p.nama_peserta LIKE ? OR p.asal_kota LIKE ? OR p.nama_club LIKE ? OR p.sekolah LIKE ?)";
        $searchParam = "%$search%";
        $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam]);
        $types .= "ssss";
    }

    if ($filter_kategori > 0) {
        $whereConditions[] = "p.category_id = ?";
        $params[] = $filter_kategori;
        $types .= "i";
    }

    if (!empty($filter_gender)) {
        $whereConditions[] = "p.jenis_kelamin = ?";
        $params[] = $filter_gender;
        $types .= "s";
    }

    $whereClause = implode(" AND ", $whereConditions);

    // Query untuk mengambil peserta
    $queryPeserta = "
        SELECT 
            p.id,
            p.nama_peserta,
            p.tanggal_lahir,
            p.jenis_kelamin,
            p.asal_kota,
            p.nama_club,
            p.sekolah,
            p.kelas,
            p.nomor_hp,
            p.bukti_pembayaran,
            c.name as category_name,
            c.min_age,
            c.max_age,
            c.gender as category_gender,
            TIMESTAMPDIFF(YEAR, p.tanggal_lahir, CURDATE()) as umur
        FROM peserta p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE $whereClause
        ORDER BY p.nama_peserta ASC
    ";

    $pesertaList = [];
    try {
        $stmtPeserta = $conn->prepare($queryPeserta);
        if (!empty($params)) {
            $stmtPeserta->bind_param($types, ...$params);
        }
        $stmtPeserta->execute();
        $resultPeserta = $stmtPeserta->get_result();
        
        while ($row = $resultPeserta->fetch_assoc()) {
            $pesertaList[] = $row;
        }
        $stmtPeserta->close();
    } catch (Exception $e) {
        die("Error mengambil data peserta: " . $e->getMessage());
    }

    // Set headers untuk download Excel
    $filename = "Daftar_Peserta_" . preg_replace('/[^A-Za-z0-9_\-]/', '_', $kegiatanData['nama_kegiatan']) . "_" . date('Y-m-d_H-i-s') . ".xls";

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    // Mulai output Excel
    echo '<!DOCTYPE html>';
    echo '<html>';
    echo '<head>';
    echo '<meta charset="UTF-8">';
    echo '<style>';
    echo 'table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; }';
    echo 'th, td { border: 1px solid #000; padding: 8px; text-align: left; vertical-align: top; }';
    echo 'th { background-color: #4472C4; color: white; font-weight: bold; text-align: center; }';
    echo '.center { text-align: center; }';
    echo '.number { text-align: center; }';
    echo '.badge { background-color: #E7E6E6; padding: 2px 6px; border-radius: 3px; font-size: 11px; }';
    echo '.badge-male { background-color: #D4E6F1; color: #1B4F72; }';
    echo '.badge-female { background-color: #FADBD8; color: #922B21; }';
    echo '.badge-paid { background-color: #D5F4E6; color: #0E6655; }';
    echo '.badge-unpaid { background-color: #FADBD8; color: #922B21; }';
    echo '.header-info { margin-bottom: 20px; }';
    echo '.header-info h2 { color: #2E86C1; margin: 5px 0; }';
    echo '.header-info p { margin: 3px 0; color: #566573; }';
    echo '</style>';
    echo '</head>';
    echo '<body>';

    // Header informasi
    echo '<div class="header-info">';
    echo '<h2>' . htmlspecialchars($kegiatanData['nama_kegiatan']) . '</h2>';
    echo '<p><strong>Total Peserta:</strong> ' . count($pesertaList) . ' orang</p>';
    echo '<p><strong>Tanggal Export:</strong> ' . date('d F Y, H:i:s') . '</p>';

    // Tambahkan info filter jika ada
    if (!empty($search) || $filter_kategori > 0 || !empty($filter_gender)) {
        echo '<p><strong>Filter yang diterapkan:</strong>';
        $filters = [];
        if (!empty($search)) $filters[] = "Pencarian: \"$search\"";
        if ($filter_kategori > 0) {
            // Ambil nama kategori
            $queryKat = "SELECT name FROM categories WHERE id = ?";
            $stmtKat = $conn->prepare($queryKat);
            $stmtKat->bind_param("i", $filter_kategori);
            $stmtKat->execute();
            $resultKat = $stmtKat->get_result();
            if ($resultKat->num_rows > 0) {
                $kategori = $resultKat->fetch_assoc();
                $filters[] = "Kategori: " . $kategori['name'];
            }
            $stmtKat->close();
        }
        if (!empty($filter_gender)) $filters[] = "Gender: $filter_gender";
        echo ' ' . implode(', ', $filters);
        echo '</p>';
    }
    echo '</div>';

    // Tabel data peserta
    echo '<table>';
    echo '<thead>';
    echo '<tr>';
    echo '<th style="width: 40px;">No</th>';
    echo '<th style="width: 200px;">Nama Peserta</th>';
    echo '<th style="width: 100px;">Tanggal Lahir</th>';
    echo '<th style="width: 60px;">Umur</th>';
    echo '<th style="width: 100px;">Jenis Kelamin</th>';
    echo '<th style="width: 150px;">Kategori</th>';
    echo '<th style="width: 120px;">Asal Kota</th>';
    echo '<th style="width: 150px;">Nama Club</th>';
    echo '<th style="width: 150px;">Sekolah</th>';
    echo '<th style="width: 80px;">Kelas</th>';
    echo '<th style="width: 130px;">Nomor HP</th>';
    echo '<th style="width: 120px;">Status Pembayaran</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    if (count($pesertaList) > 0) {
        foreach ($pesertaList as $index => $peserta) {
            echo '<tr>';
            echo '<td class="center">' . ($index + 1) . '</td>';
            echo '<td><strong>' . htmlspecialchars($peserta['nama_peserta']) . '</strong></td>';
            echo '<td class="center">' . date('d/m/Y', strtotime($peserta['tanggal_lahir'])) . '</td>';
            echo '<td class="center">' . $peserta['umur'] . ' tahun</td>';
            
            // Jenis kelamin dengan styling
            $genderClass = $peserta['jenis_kelamin'] == 'Laki-laki' ? 'badge-male' : 'badge-female';
            echo '<td class="center"><span class="badge ' . $genderClass . '">' . htmlspecialchars($peserta['jenis_kelamin']) . '</span></td>';
            
            // Kategori dengan info rentang umur
            echo '<td>';
            echo '<span class="badge">' . htmlspecialchars($peserta['category_name']) . '</span><br>';
            echo '<small>(' . $peserta['min_age'] . '-' . $peserta['max_age'] . ' thn, ';
            echo ($peserta['category_gender'] == 'Campuran' ? 'Putra/Putri' : $peserta['category_gender']) . ')</small>';
            echo '</td>';
            
            echo '<td>' . htmlspecialchars($peserta['asal_kota'] ?: '-') . '</td>';
            echo '<td>' . htmlspecialchars($peserta['nama_club'] ?: '-') . '</td>';
            echo '<td>' . htmlspecialchars($peserta['sekolah'] ?: '-') . '</td>';
            echo '<td class="center">' . htmlspecialchars($peserta['kelas'] ?: '-') . '</td>';
            echo '<td>' . htmlspecialchars($peserta['nomor_hp']) . '</td>';
            
            // Status pembayaran (tanpa gambar, hanya teks)
            if (!empty($peserta['bukti_pembayaran'])) {
                echo '<td class="center"><span class="badge badge-paid">SUDAH BAYAR</span><br><small>File: ' . htmlspecialchars($peserta['bukti_pembayaran']) . '</small></td>';
            } else {
                echo '<td class="center"><span class="badge badge-unpaid">BELUM BAYAR</span></td>';
            }
            
            echo '</tr>';
        }
    } else {
        echo '<tr>';
        echo '<td colspan="12" class="center" style="padding: 30px; font-style: italic; color: #666;">Tidak ada data peserta yang ditemukan</td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';

    // Statistik di bagian bawah
    echo '<br><br>';
    echo '<div class="header-info">';
    echo '<h3>Ringkasan Statistik</h3>';

    // Hitung statistik
    $statistik = [
        'total' => count($pesertaList),
        'laki_laki' => 0,
        'perempuan' => 0,
        'sudah_bayar' => 0,
        'belum_bayar' => 0,
        'kategori' => []
    ];

    foreach ($pesertaList as $peserta) {
        if ($peserta['jenis_kelamin'] == 'Laki-laki') {
            $statistik['laki_laki']++;
        } else {
            $statistik['perempuan']++;
        }
        
        if (!empty($peserta['bukti_pembayaran'])) {
            $statistik['sudah_bayar']++;
        } else {
            $statistik['belum_bayar']++;
        }
        
        $kategori = $peserta['category_name'];
        if (!isset($statistik['kategori'][$kategori])) {
            $statistik['kategori'][$kategori] = 0;
        }
        $statistik['kategori'][$kategori]++;
    }

    // Tabel statistik
    echo '<table style="width: 50%; margin-top: 10px;">';
    echo '<tr><th>Keterangan</th><th>Jumlah</th></tr>';
    echo '<tr><td>Total Peserta</td><td class="center"><strong>' . $statistik['total'] . '</strong></td></tr>';
    echo '<tr><td>Laki-laki</td><td class="center">' . $statistik['laki_laki'] . '</td></tr>';
    echo '<tr><td>Perempuan</td><td class="center">' . $statistik['perempuan'] . '</td></tr>';
    echo '<tr><td>Sudah Bayar</td><td class="center">' . $statistik['sudah_bayar'] . '</td></tr>';
    echo '<tr><td>Belum Bayar</td><td class="center">' . $statistik['belum_bayar'] . '</td></tr>';
    echo '</table>';

    // Distribusi per kategori jika ada
    if (!empty($statistik['kategori'])) {
        echo '<br>';
        echo '<h4>Distribusi per Kategori:</h4>';
        echo '<table style="width: 50%;">';
        echo '<tr><th>Kategori</th><th>Jumlah Peserta</th></tr>';
        foreach ($statistik['kategori'] as $kategori => $jumlah) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($kategori) . '</td>';
            echo '<td class="center"><strong>' . $jumlah . '</strong></td>';
            echo '</tr>';
        }
        echo '</table>';
    }

    echo '</div>';
    echo '</body>';
    echo '</html>';

    // Tutup koneksi database
    $conn->close();
    exit;
}

// BAGIAN TAMPILAN NORMAL (HTML)

// Include file koneksi database
try {
    include 'panggil.php';
} catch (Exception $e) {
    die("Error koneksi database: " . $e->getMessage());
}

// Ambil ID kegiatan dari URL atau ambil kegiatan pertama yang tersedia
$kegiatan_id = isset($_GET['kegiatan_id']) ? intval($_GET['kegiatan_id']) : null;

// Jika tidak ada kegiatan_id, ambil kegiatan pertama yang tersedia
if (!$kegiatan_id) {
    try {
        $queryFirstKegiatan = "SELECT id FROM kegiatan WHERE id = " . (isset($_GET['POST']) ? intval($_GET['POST']) : $_GET['id']);
        $resultFirstKegiatan = $conn->query($queryFirstKegiatan);
        if ($resultFirstKegiatan && $resultFirstKegiatan->num_rows > 0) {
            $firstKegiatan = $resultFirstKegiatan->fetch_assoc();
            $kegiatan_id = $firstKegiatan['id'];
        }
    } catch (Exception $e) {
        die("Error mengambil kegiatan: " . $e->getMessage());
    }
}

// Jika masih tidak ada kegiatan
if (!$kegiatan_id) {
    die("Tidak ada kegiatan yang tersedia.");
}

// Ambil data kegiatan
$kegiatanData = [];
try {
    $queryKegiatan = "SELECT id, nama_kegiatan FROM kegiatan WHERE id = ?";
    $stmtKegiatan = $conn->prepare($queryKegiatan);
    $stmtKegiatan->bind_param("i", $kegiatan_id);
    $stmtKegiatan->execute();
    $resultKegiatan = $stmtKegiatan->get_result();
    
    if ($resultKegiatan->num_rows > 0) {
        $kegiatanData = $resultKegiatan->fetch_assoc();
    } else {
        die("Kegiatan tidak ditemukan.");
    }
    $stmtKegiatan->close();
} catch (Exception $e) {
    die("Error mengambil data kegiatan: " . $e->getMessage());
}

// Handle pencarian dan filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter_kategori = isset($_GET['filter_kategori']) ? intval($_GET['filter_kategori']) : 0;
$filter_gender = isset($_GET['filter_gender']) ? $_GET['filter_gender'] : '';

// Query untuk mengambil data peserta dengan filter
$whereConditions = ["p.kegiatan_id = ?"];
$params = [$kegiatan_id];
$types = "i";

if (!empty($search)) {
    $whereConditions[] = "(p.nama_peserta LIKE ? OR p.asal_kota LIKE ? OR p.nama_club LIKE ? OR p.sekolah LIKE ?)";
    $searchParam = "%$search%";
    $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam]);
    $types .= "ssss";
}

if ($filter_kategori > 0) {
    $whereConditions[] = "p.category_id = ?";
    $params[] = $filter_kategori;
    $types .= "i";
}

if (!empty($filter_gender)) {
    $whereConditions[] = "p.jenis_kelamin = ?";
    $params[] = $filter_gender;
    $types .= "s";
}

$whereClause = implode(" AND ", $whereConditions);

// Query untuk mengambil peserta (dengan bukti_pembayaran)
$queryPeserta = "
    SELECT 
        p.id,
        p.nama_peserta,
        p.tanggal_lahir,
        p.jenis_kelamin,
        p.asal_kota,
        p.nama_club,
        p.sekolah,
        p.kelas,
        p.nomor_hp,
        p.bukti_pembayaran,
        c.name as category_name,
        c.min_age,
        c.max_age,
        c.gender as category_gender,
        TIMESTAMPDIFF(YEAR, p.tanggal_lahir, CURDATE()) as umur
    FROM peserta p
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE $whereClause
    ORDER BY p.nama_peserta ASC
";

$pesertaList = [];
$totalPeserta = 0;

try {
    $stmtPeserta = $conn->prepare($queryPeserta);
    if (!empty($params)) {
        $stmtPeserta->bind_param($types, ...$params);
    }
    $stmtPeserta->execute();
    $resultPeserta = $stmtPeserta->get_result();
    
    while ($row = $resultPeserta->fetch_assoc()) {
        $pesertaList[] = $row;
    }
    $totalPeserta = count($pesertaList);
    $stmtPeserta->close();
} catch (Exception $e) {
    die("Error mengambil data peserta: " . $e->getMessage());
}

// Query untuk mengambil semua kategori untuk filter
$kategoriesList = [];
try {
    $queryKategori = "
        SELECT DISTINCT c.id, c.name 
        FROM categories c 
        INNER JOIN kegiatan_kategori kk ON c.id = kk.category_id 
        WHERE kk.kegiatan_id = ? AND c.status = 'active'
        ORDER BY c.name ASC
    ";
    $stmtKategori = $conn->prepare($queryKategori);
    $stmtKategori->bind_param("i", $kegiatan_id);
    $stmtKategori->execute();
    $resultKategori = $stmtKategori->get_result();
    
    while ($row = $resultKategori->fetch_assoc()) {
        $kategoriesList[] = $row;
    }
    $stmtKategori->close();
} catch (Exception $e) {
    // Biarkan kosong jika error
}

// Statistik
$statistik = [
    'total' => $totalPeserta,
    'laki_laki' => 0,
    'perempuan' => 0,
    'kategori' => [],
    'sudah_bayar' => 0,
    'belum_bayar' => 0
];

foreach ($pesertaList as $peserta) {
    if ($peserta['jenis_kelamin'] == 'Laki-laki') {
        $statistik['laki_laki']++;
    } else {
        $statistik['perempuan']++;
    }
    
    // Statistik pembayaran
    if (!empty($peserta['bukti_pembayaran'])) {
        $statistik['sudah_bayar']++;
    } else {
        $statistik['belum_bayar']++;
    }
    
    $kategori = $peserta['category_name'];
    if (!isset($statistik['kategori'][$kategori])) {
        $statistik['kategori'][$kategori] = 0;
    }
    $statistik['kategori'][$kategori]++;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Peserta - <?= htmlspecialchars($kegiatanData['nama_kegiatan']) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .header p {
            font-size: 16px;
            opacity: 0.9;
        }

        .kegiatan-info {
            background: rgba(255, 255, 255, 0.1);
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
        }

        .content {
            padding: 30px;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #4facfe;
            text-decoration: none;
            font-weight: 600;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .statistics {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
        }

        .stat-card.primary {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .stat-card.success {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        .stat-card.warning {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }

        .stat-card.info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .stat-card.danger {
            background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
        }

        .stat-number {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 13px;
            opacity: 0.9;
        }

        .filters {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr auto;
            gap: 15px;
            align-items: end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-group label {
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .form-control {
            padding: 10px 12px;
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #4facfe;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }

        .btn-success {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            color: white;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        /* Input button styles */
        .input-btn {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 10px 16px;
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-left: 8px;
            display: none;
        }

        .input-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(40, 167, 69, 0.3);
            color: white;
            text-decoration: none;
        }

        .filter-buttons {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .table-container {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 10px;
            text-align: left;
            font-weight: 600;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .table td {
            padding: 12px 10px;
            border-bottom: 1px solid #e9ecef;
            vertical-align: top;
        }

        .table tbody tr:hover {
            background: #f8f9fa;
        }

        .table tbody tr:nth-child(even) {
            background: rgba(79, 172, 254, 0.02);
        }

        .table tbody tr:nth-child(even):hover {
            background: #f0f8ff;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            text-align: center;
        }

        .badge-male {
            background: #e3f2fd;
            color: #1976d2;
        }

        .badge-female {
            background: #fce4ec;
            color: #c2185b;
        }

        .badge-category {
            background: #f3e5f5;
            color: #7b1fa2;
            margin-bottom: 3px;
        }

        .age-info {
            font-size: 12px;
            color: #666;
            font-style: italic;
        }

        .payment-status {
            text-align: center;
            padding: 8px;
        }

        .payment-icon {
            font-size: 24px;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .payment-icon:hover {
            transform: scale(1.2);
        }

        .payment-success {
            color: #28a745;
            filter: drop-shadow(0 2px 4px rgba(40, 167, 69, 0.3));
        }

        .payment-pending {
            color: #dc3545;
            filter: drop-shadow(0 2px 4px rgba(220, 53, 69, 0.3));
        }

        .payment-tooltip {
            position: relative;
            display: inline-block;
        }

        .payment-tooltip .tooltip-text {
            visibility: hidden;
            width: 140px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 8px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            margin-left: -70px;
            opacity: 0;
            transition: opacity 0.3s;
            font-size: 12px;
        }

        .payment-tooltip:hover .tooltip-text {
            visibility: visible;
            opacity: 1;
        }

        /* Modal untuk preview image */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
        }

        .modal-content {
            position: relative;
            margin: 5% auto;
            width: 90%;
            max-width: 700px;
            background: white;
            border-radius: 12px;
            overflow: hidden;
        }

        .modal-header {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            padding: 20px;
            text-align: center;
        }

        .modal-body {
            padding: 20px;
            text-align: center;
        }

        .modal-body img {
            max-width: 100%;
            max-height: 500px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .close {
            position: absolute;
            right: 20px;
            top: 15px;
            color: white;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            z-index: 1001;
        }

        .close:hover {
            opacity: 0.7;
        }

        .no-data {
            text-align: center;
            padding: 50px 20px;
            color: #666;
            font-style: italic;
        }

        .actions {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            justify-content: space-between;
            align-items: center;
        }

        .export-btn {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .export-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(67, 233, 123, 0.3);
        }

        .category-distribution {
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 12px;
        }

        .category-distribution h4 {
            margin-bottom: 15px;
            color: #333;
        }

        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 10px;
        }

        .category-item {
            background: white;
            padding: 12px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        @media (max-width: 1024px) {
            .filters-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .filter-buttons {
                flex-direction: row;
                gap: 10px;
            }
            
            .statistics {
                grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            }
            
            .table-container {
                overflow-x: auto;
            }
            
            .table {
                min-width: 1000px;
            }
        }

        @media (max-width: 768px) {
            .content {
                padding: 20px;
            }
            
            .header {
                padding: 20px;
            }
            
            .actions {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Daftar Peserta Terdaftar</h1>
            <p>Kelola dan pantau peserta yang telah mendaftar</p>
            
            <div class="kegiatan-info">
                <h3><?= htmlspecialchars($kegiatanData['nama_kegiatan']) ?></h3>
                <p>Total Peserta Terdaftar: <?= $totalPeserta ?> orang</p>
            </div>
        </div>

        <div class="content">
            <a href="kegiatan.view.php" class="back-link">← Kembali Ke Kegiatan</a>

            <!-- Statistik -->
            <div class="statistics">
                <div class="stat-card primary">
                    <div class="stat-number"><?= $statistik['total'] ?></div>
                    <div class="stat-label">Total Peserta</div>
                </div>
                <div class="stat-card success">
                    <div class="stat-number"><?= $statistik['laki_laki'] ?></div>
                    <div class="stat-label">Laki-laki</div>
                </div>
                <div class="stat-card warning">
                    <div class="stat-number"><?= $statistik['perempuan'] ?></div>
                    <div class="stat-label">Perempuan</div>
                </div>
                <div class="stat-card info">
                    <div class="stat-number"><?= $statistik['sudah_bayar'] ?></div>
                    <div class="stat-label">Sudah Bayar</div>
                </div>
                <div class="stat-card danger">
                    <div class="stat-number"><?= $statistik['belum_bayar'] ?></div>
                    <div class="stat-label">Belum Bayar</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= count($statistik['kategori']) ?></div>
                    <div class="stat-label">Kategori</div>
                </div>
            </div>

            <!-- Filter dan Pencarian -->
            <div class="filters">
                <form method="GET" action="">
                    <input type="hidden" name="kegiatan_id" value="<?= $kegiatan_id ?>">
                    
                    <div class="filters-grid">
                        <div class="filter-group">
                            <label for="search">Cari Peserta</label>
                            <input type="text" 
                                   id="search" 
                                   name="search" 
                                   class="form-control" 
                                   placeholder="Nama, kota, club, atau sekolah..."
                                   value="<?= htmlspecialchars($search) ?>">
                        </div>
                        
                        <div class="filter-group">
                            <label for="filter_kategori">Kategori</label>
                            <select id="filter_kategori" name="filter_kategori" class="form-control">
                                <option value="">Semua Kategori</option>
                                <?php foreach ($kategoriesList as $kategori): ?>
                                    <option value="<?= $kategori['id'] ?>" 
                                            <?= $filter_kategori == $kategori['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($kategori['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="filter_gender">Jenis Kelamin</label>
                            <select id="filter_gender" name="filter_gender" class="form-control">
                                <option value="">Semua</option>
                                <option value="Laki-laki" <?= $filter_gender == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                                <option value="Perempuan" <?= $filter_gender == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                            </select>
                        </div>
                        
                        <div class="filter-group filter-buttons">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            
                            <!-- Input Button - Hanya muncul jika kategori dipilih -->
                            <?php if (isset($filter_kategori) && $filter_kategori > 0): ?>
                                <a href="?action=scorecard&kegiatan_id=<?= $kegiatan_id ?>&category_id=<?= $filter_kategori ?>" 
                                   class="btn btn-success input-btn" 
                                   title="Setup scorecard untuk kategori yang dipilih">
                                    Input
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Actions -->
            <div class="actions">
                <div>
                    <?php if ($totalPeserta > 0): ?>
                        <a href="?export=excel&kegiatan_id=<?= $kegiatan_id ?>&search=<?= urlencode($search) ?>&filter_kategori=<?= $filter_kategori ?>&filter_gender=<?= urlencode($filter_gender) ?>" 
                           class="export-btn" target="_blank">
                            📊 Export ke Excel
                        </a>
                    <?php endif; ?>
                </div>
                <div>
                    <?php if ($totalPeserta > 0): ?>
                        <span style="color: #666; font-size: 14px;">
                            Menampilkan <?= $totalPeserta ?> peserta
                            <?php if (!empty($search) || $filter_kategori > 0 || !empty($filter_gender)): ?>
                                dengan filter
                            <?php endif; ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Tabel Peserta -->
            <div class="table-container">
                <?php if ($totalPeserta > 0): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="width: 30px;">No</th>
                                <th style="width: 180px;">Nama Peserta</th>
                                <th style="width: 80px;">Umur</th>
                                <th style="width: 90px;">Gender</th>
                                <th style="width: 140px;">Kategori</th>
                                <th style="width: 100px;">Asal Kota</th>
                                <th style="width: 130px;">Club</th>
                                <th style="width: 130px;">Sekolah</th>
                                <th style="width: 70px;">Kelas</th>
                                <th style="width: 110px;">No. HP</th>
                                <th style="width: 80px;">Pembayaran</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pesertaList as $index => $peserta): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($peserta['nama_peserta']) ?></strong>
                                        <div class="age-info">
                                            Lahir: <?= date('d/m/Y', strtotime($peserta['tanggal_lahir'])) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <strong><?= $peserta['umur'] ?> tahun</strong>
                                    </td>
                                    <td>
                                        <span class="badge <?= $peserta['jenis_kelamin'] == 'Laki-laki' ? 'badge-male' : 'badge-female' ?>">
                                            <?= $peserta['jenis_kelamin'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="badge badge-category">
                                            <?= htmlspecialchars($peserta['category_name']) ?>
                                        </div>
                                        <div class="age-info">
                                            <?= $peserta['min_age'] ?>-<?= $peserta['max_age'] ?> thn 
                                            (<?= $peserta['category_gender'] == 'Campuran' ? 'Putra/Putri' : $peserta['category_gender'] ?>)
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($peserta['asal_kota'] ?: '-') ?></td>
                                    <td><?= htmlspecialchars($peserta['nama_club'] ?: '-') ?></td>
                                    <td><?= htmlspecialchars($peserta['sekolah'] ?: '-') ?></td>
                                    <td><?= htmlspecialchars($peserta['kelas'] ?: '-') ?></td>
                                    <td>
                                        <a href="tel:<?= htmlspecialchars($peserta['nomor_hp']) ?>" 
                                           style="color: #4facfe; text-decoration: none;">
                                            <?= htmlspecialchars($peserta['nomor_hp']) ?>
                                        </a>
                                    </td>
                                    <td>
                                        <div class="payment-status">
                                            <?php if (!empty($peserta['bukti_pembayaran'])): ?>
                                                <div class="payment-tooltip">
                                                    <span class="payment-icon payment-success" 
                                                          onclick="showPaymentModal('<?= htmlspecialchars($peserta['nama_peserta']) ?>', '<?= $peserta['bukti_pembayaran'] ?>')">
                                                        📄✅
                                                    </span>
                                                    <span class="tooltip-text">Klik untuk lihat bukti pembayaran</span>
                                                </div>
                                            <?php else: ?>
                                                <div class="payment-tooltip">
                                                    <span class="payment-icon payment-pending">❌</span>
                                                    <span class="tooltip-text">Belum upload bukti pembayaran</span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-data">
                        <h3>Belum Ada Peserta Terdaftar</h3>
                        <p>
                            <?php if (!empty($search) || $filter_kategori > 0 || !empty($filter_gender)): ?>
                                Tidak ada peserta yang sesuai dengan filter yang dipilih.
                                <br><br>
                                <a href="?kegiatan_id=<?= $kegiatan_id ?>" class="btn btn-secondary">Reset Filter</a>
                            <?php else: ?>
                                Belum ada peserta yang mendaftar untuk kegiatan ini.
                                <br><br>
                                <a href="form_pendaftaran.php?kegiatan_id=<?= $kegiatan_id ?>" class="btn btn-success">Daftarkan Peserta Pertama</a>
                            <?php endif; ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($statistik['kategori'])): ?>
                <div class="category-distribution">
                    <h4>Distribusi per Kategori:</h4>
                    <div class="category-grid">
                        <?php foreach ($statistik['kategori'] as $kategori => $jumlah): ?>
                            <div class="category-item">
                                <strong><?= htmlspecialchars($kategori) ?></strong><br>
                                <span style="color: #4facfe; font-size: 18px; font-weight: 600;"><?= $jumlah ?> orang</span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal untuk preview bukti pembayaran -->
    <div id="paymentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close" onclick="closePaymentModal()">&times;</span>
                <h3 id="modal-title">Bukti Pembayaran</h3>
            </div>
            <div class="modal-body">
                <div id="modal-image-container">
                    <!-- Image akan dimuat di sini -->
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto submit form saat filter berubah
        document.querySelectorAll('select[name="filter_kategori"], select[name="filter_gender"]').forEach(function(select) {
            select.addEventListener('change', function() {
                this.form.submit();
            });
        });

        // Enter key untuk search
        document.getElementById('search').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                this.form.submit();
            }
        });

        // Clear search dengan Escape key
        document.getElementById('search').addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                this.value = '';
                this.form.submit();
            }
        });

        // Inisialisasi Input Button
        document.addEventListener('DOMContentLoaded', function() {
            const kategoriSelect = document.getElementById('filter_kategori');
            const filterButtons = document.querySelector('.filter-buttons');
            
            // Buat input button
            const inputBtn = document.createElement('a');
            inputBtn.className = 'input-btn';
            inputBtn.textContent = 'Input';
            inputBtn.title = 'Setup scorecard untuk kategori yang dipilih';
            
            // Masukkan button setelah filter button
            filterButtons.appendChild(inputBtn);
            
            // Function untuk toggle button
            function toggleInputButton() {
                const selectedKategori = kategoriSelect.value;
                
                if (selectedKategori && selectedKategori !== '') {
                    inputBtn.href = `?action=scorecard&kegiatan_id=<?= $kegiatan_id ?>&category_id=${selectedKategori}`;
                    inputBtn.style.display = 'inline-block';
                } else {
                    inputBtn.style.display = 'none';
                }
            }
            
            // Event listener untuk perubahan kategori
            kategoriSelect.addEventListener('change', toggleInputButton);
            
            // Check initial state
            toggleInputButton();
        });

        // Fungsi untuk menampilkan modal bukti pembayaran
        function showPaymentModal(namaPeserta, fileName) {
            const modal = document.getElementById('paymentModal');
            const modalTitle = document.getElementById('modal-title');
            const imageContainer = document.getElementById('modal-image-container');
            
            modalTitle.textContent = 'Bukti Pembayaran - ' + namaPeserta;
            
            // Cek ekstensi file untuk menentukan cara menampilkan
            const fileExtension = fileName.toLowerCase().split('.').pop();
            const imagePath = 'uploads/pembayaran/' + fileName;
            
            if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
                // Tampilkan sebagai gambar
                imageContainer.innerHTML = `
                    <img src="${imagePath}" alt="Bukti Pembayaran" style="max-width: 100%; max-height: 500px; border-radius: 8px;">
                    <div style="margin-top: 15px; padding: 10px; background: #f8f9fa; border-radius: 6px; font-size: 14px; color: #666;">
                        <strong>File:</strong> ${fileName}<br>
                        <strong>Peserta:</strong> ${namaPeserta}
                    </div>
                `;
            } else if (fileExtension === 'pdf') {
                // Tampilkan link download untuk PDF
                imageContainer.innerHTML = `
                    <div style="text-align: center; padding: 40px;">
                        <div style="font-size: 48px; color: #dc3545; margin-bottom: 20px;">📄</div>
                        <h4>File PDF</h4>
                        <p style="margin: 15px 0; color: #666;">File bukti pembayaran dalam format PDF</p>
                        <a href="${imagePath}" target="_blank" class="btn btn-primary" style="margin: 10px;">
                            Buka PDF
                        </a>
                        <a href="${imagePath}" download="${fileName}" class="btn btn-success" style="margin: 10px;">
                            Download
                        </a>
                        <div style="margin-top: 20px; padding: 10px; background: #f8f9fa; border-radius: 6px; font-size: 14px; color: #666;">
                            <strong>File:</strong> ${fileName}<br>
                            <strong>Peserta:</strong> ${namaPeserta}
                        </div>
                    </div>
                `;
            } else {
                // File tidak dikenal
                imageContainer.innerHTML = `
                    <div style="text-align: center; padding: 40px;">
                        <div style="font-size: 48px; color: #ffc107; margin-bottom: 20px;">⚠️</div>
                        <h4>File tidak dapat ditampilkan</h4>
                        <p style="margin: 15px 0; color: #666;">Format file tidak didukung untuk preview</p>
                        <a href="${imagePath}" target="_blank" class="btn btn-primary" style="margin: 10px;">
                            Buka File
                        </a>
                        <a href="${imagePath}" download="${fileName}" class="btn btn-success" style="margin: 10px;">
                            Download
                        </a>
                        <div style="margin-top: 20px; padding: 10px; background: #f8f9fa; border-radius: 6px; font-size: 14px; color: #666;">
                            <strong>File:</strong> ${fileName}<br>
                            <strong>Peserta:</strong> ${namaPeserta}
                        </div>
                    </div>
                `;
            }
            
            modal.style.display = 'block';
        }

        // Fungsi untuk menutup modal
        function closePaymentModal() {
            document.getElementById('paymentModal').style.display = 'none';
        }

        // Tutup modal jika klik di luar area modal
        window.onclick = function(event) {
            const modal = document.getElementById('paymentModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }

        // Tutup modal dengan tombol ESC
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closePaymentModal();
            }
        });
    </script>
</body>
</html>