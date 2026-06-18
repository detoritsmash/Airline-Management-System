<?php

require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT PassengerID, Password FROM Passengers WHERE Email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    // Verifying if user exists and password matches the encrypted hash
    if ($result && password_verify($password, $result['Password'])) {
        $_SESSION['user_id'] = $result['PassengerID']; 
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid email or password configuration.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Passenger Login</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; padding: 50px; }
        .form-box { background: white; padding: 30px; max-width: 400px; margin: 0 auto; border: 1px solid #ddd; }
        input, button { width: 100%; padding: 10px; margin-top: 10px; box-sizing: border-box; }
        button { background: #0056b3; color: white; border: none; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>
    <div class="form-box">
        <h2>Passenger Login</h2>
        <?php if(isset($_GET['registration'])): ?><p style="color:green;">Account created! Please log in.</p><?php endif; ?>
        <?php if(isset($error)): ?><p style="color:red;"><?= $error ?></p><?php endif; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <p style="text-align:center; font-size:14px;"><a href="register.php">New user? Register here</a></p>
    </div>
</body>
</html>