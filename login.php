<?php
// Mulai sesi
session_start();

// Sambungkan ke database
$conn = new mysqli("localhost", "root", "", "tugas_ppl");
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Cek apakah formulir dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query untuk mencocokkan username dan password
    $sql = "SELECT * FROM pengguna WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    // Cek apakah ada hasil
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Simpan data pengguna ke sesi
        $_SESSION['id_pengguna'] = $user['id_pengguna'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['peran'] = $user['peran'];

        // Redirect berdasarkan peran
        if ($user['peran'] == 'Manager') {
            header("Location: dashboard_manager.php");
        } else if ($user['peran'] == 'Staff') {
            header("Location: dashboard_staff.php");
        }
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Login</title>
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <style>
        body.bg-primary {
            background: url('gambar/OIP.jpg') no-repeat center center fixed;
            background-size: cover;
        }
    </style>
</head>
<body class="bg-primary">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header"><h3 class="text-center font-weight-light my-4">Login</h3></div>
                                <div class="card-body">
                                    <?php if (isset($error)) { ?>
                                        <div class="alert alert-danger" role="alert">
                                            <?= $error ?>
                                        </div>
                                    <?php } ?>
                                    <form method="POST" action="login.php">
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="username" name="username" type="text" placeholder="Username" required />
                                            <label for="username">Username</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="password" name="password" type="password" placeholder="Password" required />
                                            <label for="password">Password</label>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-center mt-4 mb-0">
                                            <button class="btn btn-primary" type="submit">Login</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
</body>
</html>
