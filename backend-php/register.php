<?php
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['fullname'];
    $email = $_POST['email'];
    $nationality = $_POST['nationality'];
    //hash the password before saving it to the database
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $sql = "INSERT INTO Passengers (FullName, Email, Password, Nationality) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $email, $password, $nationality);
    
    if ($stmt->execute()) {
        header("Location: login.php?registration=success");
        exit;
    } else {
        $error = "Registration failed. Email might already be registered.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Passenger Registration</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; padding: 50px; }
        .form-box { background: white; padding: 30px; max-width: 400px; margin: 0 auto; border: 1px solid #ddd; }
        input, button { width: 100%; padding: 10px; margin-top: 10px; box-sizing: border-box; }
        button { background: #28a745; color: white; border: none; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>
    <div class="form-box">
        <h2>Passenger Signup</h2>
        <?php if(isset($error)): ?><p style="color:red;"><?= $error ?></p><?php endif; ?>
        <form method="POST">
            <input type="text" name="fullname" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="text" name="nationality" placeholder="Nationality" required>
            <input type="password" name="password" placeholder="Create Password" required>
            <button type="submit">Create Account</button>
        </form>
        <p style="text-align:center; font-size:14px;"><a href="login.php">Already have an account? Login</a></p>
    </div>
</body>
</html>