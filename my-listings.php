<?php
session_start();
require 'config/database.php';

// Access check: must be logged in AND must be an employer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employer') {
    header('Location: login.php');
    exit;
}

$employer_id = $_SESSION['user_id'];

$pdo = getConnection();
$stmt = $pdo->prepare("SELECT * FROM listings WHERE employer_id = ? ORDER BY id DESC");
$stmt->execute([$employer_id]);
$listings = $stmt->fetchAll();
?>

<h1>My Listings</h1>

<?php if (empty($listings)): ?>
    <p>You haven't posted any listings yet.</p>
<?php else: ?>
    <?php foreach ($listings as $listing): ?>
        <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
            <h3><?= htmlspecialchars($listing['title']) ?></h3>
            <p><?= htmlspecialchars($listing['description']) ?></p>
            <p><strong>Status:</strong> <?= htmlspecialchars($listing['status']) ?></p>
            <a href="view-applicants.php?listing_id=<?= $listing['id'] ?>">View Applicants</a>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<a href="dashboard.php">Back to dashboard</a>