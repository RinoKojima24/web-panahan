<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            display: flex;
            height: 100vh;
            background-color: #f8f9fa;
        }

        .container {
            display: flex;
            width: 100%;
        }

        .sidebar {
            width: 20%;
            background-color: #e8f5e9;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 10px;
            border-right: 1px solid #ccc;
        }

        .logo {
            height: 50px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
        }

        .menu-btn, .dropdown-btn {
            width: 100%;
            margin: 5px 0;
            padding: 10px;
            background-color: #81d4fa;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: left;
            font-size: 14px;
        }

        .menu-btn:hover, .dropdown-btn:hover {
            background-color: #0288d1;
            color: #fff;
        }

        .menu-btn a, .dropdown-content a {
            color: inherit;
            text-decoration: none;
            display: block;
            width: 100%;
        }

        /* Dropdown styling */
        .dropdown {
            width: 100%;
        }

        .dropdown-content {
            display: none;
            flex-direction: column;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0px 2px 5px rgba(0,0,0,0.1);
            margin-top: 2px;
        }

        .dropdown-content a {
            padding: 8px 12px;
            text-decoration: none;
            color: black;
        }

        .dropdown-content a:hover {
            background-color: #f0f0f0;
        }

        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 10px;
        }

        .header {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            height: 50px;
            border-bottom: 1px solid #ccc;
            margin-bottom: 10px;
        }

        .username-container {
            display: flex;
            align-items: center;
            gap: 10px; 
        }

        .username {
            font-size: 1em;
            font-weight: bold;
        }

        .profile-logo {
            width: 25px; 
            height: 25px;
            border-radius: 50%; 
            object-fit: cover; 
            border: 1px solid #ccc; 
        }
    </style>

    <?php
    include('panggil.php');
    if ($_SESSION['login'] == false) {
        header('Location: index.php');
        die();
    }
    if (isset($_POST['logout'])) {
        $_SESSION['login'] = false;
        header('Location: index.php');
    }
    ?>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div class="logo">Dashboard</div>
            <button class="menu-btn"><a href="dashboard.php">Dashboard</a></button>

            <!-- Dropdown Master Data -->
            <div class="dropdown">
                <button class="dropdown-btn">Master Data ▾</button>
                <div class="dropdown-content">
                    <a href="users.php">Users</a>
                    <a href="kategori.php">Kategori</a>
                    <a href="pertandingan.php">Pertandingan</a>
                </div>
            </div>

            <button class="menu-btn"><a href="kegiatan.php">Kegiatan</a></button>
            <button class="menu-btn"><a href="peserta.php">Peserta</a></button>
            <hr style="width:100%; margin: 10px 0;">
            <form method="post" style="width:100%;">
                <button type="submit" name="logout" class="menu-btn" style="background-color: transparent; color: red; text-align:left;">Log Out</button>
            </form>
        </div>

        <div class="main-content">
            <div class="header">
                <div class="username-container">
                    <h3><?php echo $_SESSION['name']; ?></h3>
                    <img src="angzay.png" alt="User Profile" class="profile-logo">
                </div>
            </div>
            <h2>Dashboard</h2> <br>
            <h4>Selamat Datang Admin di Aplikasi Pendaftaran Turnamen Panahan</h4>
        </div>
    </div>

    <script>
        // Script dropdown
        document.querySelectorAll('.dropdown-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const content = btn.nextElementSibling;
                content.style.display = content.style.display === 'flex' ? 'none' : 'flex';
                btn.innerHTML = content.style.display === 'flex' ? 'Master Data ▴' : 'Master Data ▾';
            });
        });
    </script>
</body>
</html>
