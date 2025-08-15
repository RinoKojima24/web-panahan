<?php
include 'panggil.php';

$result = $conn->query("SELECT * FROM users ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Data Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">

    <h5>Data Users</h5>

    <div class="d-flex justify-content-between mb-3">
        <a href="tambah-user.php" class="btn btn-success">Tambah Data</a>
        <form class="d-flex" method="get">
            <input class="form-control me-2" type="search" name="q" placeholder="Cari data...">
            <button class="btn btn-primary" type="submit">Cari</button>
        </form>
    </div>

    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th style="width: 50px;">No</th>
                <th>Nama</th>
                <th>Email</th>
                <th style="width: 150px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while ($row = $result->fetch_assoc()): 
            ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= htmlspecialchars($row['name']); ?></td>
                <td><?= htmlspecialchars($row['email']); ?></td>
                <td>
                    <a href="edit-user.php?id=<?= $row['id']; ?>" class="text-primary">Edit</a> |
                    <a href="hapus-user.php?id=<?= $row['id']; ?>" class="text-danger" onclick="return confirm('Yakin hapus data ini?')">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <a href="dashboard.php">Back</a>

</body>
</html>
