<?php
session_start();
require 'config/database.php';

// Access check: must be logged in AND must be an applicant
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'applicant') {
    header('Location: login.php');
    exit;
}

$applicant_id = $_SESSION['user_id'];

$pdo = getConnection();
$stmt = $pdo->prepare("
    SELECT applications.id, applications.status, applications.cover_note, applications.created_at,
           listings.title, listings.description
    FROM applications
    JOIN listings ON applications.listing_id = listings.id
    WHERE applications.applicant_id = ?
    ORDER BY applications.created_at DESC
");
$stmt->execute([$applicant_id]);
$applications = $stmt->fetchAll();
?>

<h1>My Applications</h1>

<?php if (empty($applications)): ?>
    <p>You haven't applied to any listings yet.</p>
<?php else: ?>
    <?php foreach ($applications as $app): ?>
        <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
            <h3><?= htmlspecialchars($app['title']) ?></h3>
            <p><?= htmlspecialchars($app['description']) ?></p>
            <p><strong>Status:</strong> <?= htmlspecialchars($app['status']) ?></p>
            <p><strong>Applied on:</strong> <?= htmlspecialchars($app['created_at']) ?></p>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<a href="dashboard.php">Back to dashboard</a>