<?php
include 'panggil.php'; // Pastikan session_start() hanya ada di sini, jangan dobel

if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    header('Location: ');
    exit;
}

if (isset($_POST['submit'])) {
    $name = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if ($name === '' || $password === '') {
        echo "<script>alert('Harap isi username dan password');</script>";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE name = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();

        if (!$data || $password !== $data['password']) {
            echo "<script>alert('Login gagal');</script>";
        } else {
            $_SESSION['login'] = true;
            $_SESSION['name'] = $data['name'];
            header('Location: dashboard.php');
            exit;
        }
    }
}
?>