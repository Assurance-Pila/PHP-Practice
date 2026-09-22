<?php
session_start();
require 'config/database.php';

// Access check: must be logged in AND must be an employer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employer') {
    header('Location: login.php');
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $employer_id = $_SESSION['user_id'];

    $pdo = getConnection();
    $stmt = $pdo->prepare("INSERT INTO listings (title, description, employer_id, status) VALUES (?, ?, ?, 'pending')");
    $stmt->execute([$title, $description, $employer_id]);

    $message = "Listing submitted for approval!";
}
?>

<?php if ($message): ?>
    <p><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<form method="POST" action="create-listing.php">
    <input type="text" name="title" placeholder="Listing title" required><br>
    <textarea name="description" placeholder="Description" required></textarea><br>
    <button type="submit">Submit Listing</button>
</form>

<a href="dashboard.php">Back to dashboard</a>