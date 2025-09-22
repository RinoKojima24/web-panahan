<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Scorecard Panahan</title>
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
            max-width: 400px;
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

        /* Scorecard Styles */
        .scorecard-container {
            background: rgba(45, 52, 54, 0.95);
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            display: none;
        }

        .scorecard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            background: rgba(116, 185, 255, 0.1);
            padding: 15px;
            border-radius: 12px;
        }

        .category-info {
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

        .category-name {
            font-size: 14px;
            font-weight: 600;
        }

        .category-count {
            font-size: 18px;
            font-weight: 700;
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

        .score-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 11px;
            table-layout: fixed;
        }

        .score-table th {
            background: rgba(0, 0, 0, 0.5);
            padding: 6px 2px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            font-weight: 600;
            font-size: 10px;
            text-align: center;
        }

        .score-table td {
            background: rgba(255, 255, 255, 0.05);
            padding: 6px 2px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            text-align: center;
            height: 28px;
            font-size: 11px;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .score-table td:hover {
            background: rgba(116, 185, 255, 0.1);
        }

        .score-table td:not(.round-number):not(.total-col):not(.end-col):empty::after {
            content: '';
            opacity: 0.5;
        }

        .score-table .round-number {
            background: rgba(116, 185, 255, 0.2);
            font-weight: 600;
        }

        .score-table .total-col {
            background: rgba(253, 203, 110, 0.2);
            font-weight: 600;
        }

        .score-table .end-col {
            background: rgba(0, 184, 148, 0.2);
            font-weight: 600;
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
        }

        .edit-btn:hover {
            background: rgba(116, 185, 255, 0.3);
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

            <div class="form-group">
                <label class="form-label">Jumlah Sesi</label>
                <input type="number" class="form-input" id="jumlahSesi" min="1" max="10" value="9" placeholder="9">
            </div>

            <div class="form-group">
                <label class="form-label">Jumlah Anak Panah per Sesi</label>
                <input type="number" class="form-input" id="jumlahPanah" min="1" max="12" value="3" placeholder="3">
            </div>

            <button class="create-btn" onclick="createScorecard()">
                Buat Scorecard
            </button>
        </div>

        <!-- Scorecard Display -->
        <div class="scorecard-container" id="scorecardContainer">
            <div class="scorecard-header">
                <div class="category-info">
                    <div class="category-icon">🎯</div>
                    <div class="category-name">Rambahan</div>
                    <div class="category-count" id="rambahanCount">7</div>
                </div>
                <div class="category-info">
                    <div class="category-icon">🏹</div>
                    <div class="category-name">Anak Panah</div>
                    <div class="category-count" id="panahCount">9</div>
                </div>
            </div>

            <div class="scorecard-title">Informasi Skor</div>

            <!-- Untung Table -->
            <div style="margin-bottom: 20px;">
                <div style="font-size: 14px; font-weight: 600; margin-bottom: 10px; color: #74b9ff;">#Untung</div>
                <div class="table-wrapper">
                    <table class="score-table" id="untungTable">
                        <!-- Table will be generated by JavaScript -->
                    </table>
                </div>
            </div>

            <!-- Yoga Table -->
            <div>
                <div style="font-size: 14px; font-weight: 600; margin-bottom: 10px; color: #74b9ff;">#Yoga</div>
                <div class="table-wrapper">
                    <table class="score-table" id="yogaTable">
                        <!-- Table will be generated by JavaScript -->
                    </table>
                </div>
            </div>

            <button class="edit-btn" onclick="editScorecard()">
                Edit Setup
            </button>
        </div>
    </div>

    <script>
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

            if (jumlahSesi > 12 || jumlahPanah > 15) {
                alert('Maksimal 12 sesi dan 15 anak panah untuk tampilan optimal');
                return;
            }

            // Update header counts
            document.getElementById('rambahanCount').textContent = jumlahSesi;
            document.getElementById('panahCount').textContent = jumlahPanah;

            // Apply dynamic CSS based on input
            applyDynamicCSS(jumlahSesi, jumlahPanah);

            // Generate tables
            generateTable('untungTable', jumlahSesi, jumlahPanah);
            generateTable('yogaTable', jumlahSesi, jumlahPanah);

            // Show scorecard, hide form
            document.getElementById('setupForm').style.display = 'none';
            document.getElementById('scorecardContainer').style.display = 'block';
        }

        function applyDynamicCSS(jumlahSesi, jumlahPanah) {
            // Remove existing dynamic styles
            const existingStyle = document.getElementById('dynamicStyle');
            if (existingStyle) {
                existingStyle.remove();
            }

            // Calculate optimal dimensions
            const totalColumns = jumlahSesi + 3; // +3 for R, Total, End columns
            let cellWidth, fontSize, padding;

            // Adjust based on number of sessions
            if (jumlahSesi <= 6) {
                cellWidth = `${Math.max(12, 85 / totalColumns)}%`;
                fontSize = '11px';
                padding = '8px 4px';
            } else if (jumlahSesi <= 9) {
                cellWidth = `${Math.max(10, 80 / totalColumns)}%`;
                fontSize = '10px';
                padding = '6px 3px';
            } else if (jumlahSesi <= 12) {
                cellWidth = `${Math.max(8, 75 / totalColumns)}%`;
                fontSize = '9px';
                padding = '5px 2px';
            } else {
                cellWidth = `${Math.max(6, 70 / totalColumns)}%`;
                fontSize = '8px';
                padding = '4px 1px';
            }

            // Adjust row height based on number of arrows
            let rowHeight;
            if (jumlahPanah <= 6) {
                rowHeight = '32px';
            } else if (jumlahPanah <= 10) {
                rowHeight = '28px';
            } else {
                rowHeight = '24px';
            }

            // Create dynamic CSS
            const style = document.createElement('style');
            style.id = 'dynamicStyle';
            style.textContent = `
                .score-table td,
                .score-table th {
                    width: ${cellWidth};
                    min-width: ${Math.max(20, 300 / totalColumns)}px;
                    font-size: ${fontSize};
                    padding: ${padding};
                    height: ${rowHeight};
                }

                .score-table .round-number {
                    width: 8%;
                    min-width: 25px;
                    background: rgba(116, 185, 255, 0.3);
                    font-weight: 600;
                }

                .score-table .total-col {
                    width: 12%;
                    min-width: 35px;
                    background: rgba(253, 203, 110, 0.3);
                    font-weight: 600;
                }

                .score-table .end-col {
                    width: 12%;
                    min-width: 35px;
                    background: rgba(0, 184, 148, 0.3);
                    font-weight: 600;
                }

                /* Session columns */
                ${Array.from({length: jumlahSesi}, (_, i) => 
                    `.score-table .session-${i+1} { 
                        background: rgba(255, 255, 255, 0.05); 
                    }`
                ).join('\n')}

                /* Responsive adjustments */
                @media (max-width: 480px) {
                    .score-table td,
                    .score-table th {
                        font-size: ${parseInt(fontSize) - 1}px;
                        padding: ${parseInt(padding.split(' ')[0]) - 2}px 1px;
                    }
                }

                /* Color coding for different score ranges */
                .score-table td[data-score="10"],
                .score-table td[data-score="9"] {
                    background: rgba(0, 184, 148, 0.3) !important;
                    color: #00b894;
                    font-weight: 600;
                }

                .score-table td[data-score="8"],
                .score-table td[data-score="7"] {
                    background: rgba(253, 203, 110, 0.3) !important;
                    color: #fdcb6e;
                    font-weight: 600;
                }

                .score-table td[data-score="6"],
                .score-table td[data-score="5"] {
                    background: rgba(255, 184, 108, 0.3) !important;
                    color: #ff7675;
                }

                /* Container adjustments */
                .scorecard-container {
                    ${jumlahSesi > 9 || jumlahPanah > 10 ? 'overflow-x: auto;' : ''}
                }

                /* Table container for horizontal scroll */
                .table-wrapper {
                    ${jumlahSesi > 9 ? 'overflow-x: auto; -webkit-overflow-scrolling: touch;' : ''}
                }
            `;

            document.head.appendChild(style);
        }

        function generateTable(tableId, jumlahSesi, jumlahPanah) {
            const table = document.getElementById(tableId);
            table.innerHTML = '';

            // Create header
            const thead = table.createTHead();
            const headerRow = thead.insertRow();
            
            // Round column
            const roundHeader = headerRow.insertCell();
            roundHeader.innerHTML = '<strong>R</strong>';
            roundHeader.className = 'round-number';
            
            // Session columns (S1, S2, etc.)
            for (let i = 1; i <= jumlahSesi; i++) {
                const cell = headerRow.insertCell();
                cell.innerHTML = `<strong>S${i}</strong>`;
                cell.className = `session-${i}`;
            }
            
            // Total and End columns
            const totalHeader = headerRow.insertCell();
            totalHeader.innerHTML = '<strong>Total</strong>';
            totalHeader.className = 'total-col';
            
            const endHeader = headerRow.insertCell();
            endHeader.innerHTML = '<strong>End</strong>';
            endHeader.className = 'end-col';

            // Create body with rows for each arrow
            const tbody = table.createTBody();
            for (let arrow = 1; arrow <= jumlahPanah; arrow++) {
                const row = tbody.insertRow();
                
                // Round number
                const roundCell = row.insertCell();
                roundCell.className = 'round-number';
                roundCell.textContent = arrow;
                
                // Session cells (empty for scoring)
                for (let session = 1; session <= jumlahSesi; session++) {
                    const cell = row.insertCell();
                    cell.className = `session-${session}`;
                    cell.addEventListener('click', function() {
                        const score = prompt(`Masukkan skor untuk S${session} anak panah ${arrow} (0-10):`);
                        if (score !== null && score !== '') {
                            const scoreValue = parseInt(score);
                            if (scoreValue >= 0 && scoreValue <= 10) {
                                this.textContent = scoreValue;
                                this.setAttribute('data-score', scoreValue);
                                updateTotals(tableId, jumlahSesi, jumlahPanah);
                            } else {
                                alert('Skor harus antara 0-10');
                            }
                        }
                    });
                }
                
                // Total cell
                const totalCell = row.insertCell();
                totalCell.className = 'total-col';
                totalCell.id = `${tableId}_total_${arrow}`;
                totalCell.textContent = '0';
                
                // End cell
                const endCell = row.insertCell();
                endCell.className = 'end-col';
                endCell.textContent = '87'; // Default value as shown in image
            }
        }

        function updateTotals(tableId, jumlahSesi, jumlahPanah) {
            const table = document.getElementById(tableId);
            const rows = table.querySelectorAll('tbody tr');
            
            rows.forEach((row, index) => {
                const cells = row.querySelectorAll('td');
                let total = 0;
                
                // Sum session scores (skip first cell which is round number)
                for (let i = 1; i <= jumlahSesi; i++) {
                    const score = parseInt(cells[i].textContent) || 0;
                    total += score;
                }
                
                // Update total cell
                const totalCell = document.getElementById(`${tableId}_total_${index + 1}`);
                if (totalCell) {
                    totalCell.textContent = total;
                }
            });
        }

        function editScorecard() {
            document.getElementById('setupForm').style.display = 'block';
            document.getElementById('scorecardContainer').style.display = 'none';
        }
    </script>
</body>
</html>