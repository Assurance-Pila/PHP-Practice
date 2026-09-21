<?php
require 'config/database.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
        $pdo = getConnection();
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $hashedPassword, $role]);
        $message = "Registration successful!";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}?>
<!DOCTYPE html>
<html>
<head><title>Register</title></head>
<body>
<?php if ($message): ?>
    <p><?= htmlspecialchars($message) ?></p>
<?php endif; ?>
    <form method="POST" action="register.php">
        <input type="text" name="name" placeholder="Full name" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <select name="role">
            <option value="applicant">Applicant</option>
            <option value="employer">Employer</option>
        </select><br>
        <button type="submit">Register</button>
    </form>
</body>
</html>