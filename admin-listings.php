<?php
session_start();
require 'config/database.php';

// Access check: must be logged in AND must be an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$pdo = getConnection();

// Handle approve/reject action if submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $listing_id = $_POST['listing_id'];
    $new_status = $_POST['new_status'];

    $stmt = $pdo->prepare("UPDATE listings SET status = ? WHERE id = ?");
    $stmt->execute([$new_status, $listing_id]);
}

// Fetch all listings, regardless of status
$stmt = $pdo->prepare("SELECT * FROM listings ORDER BY id DESC");
$stmt->execute();
$listings = $stmt->fetchAll();
?>

<h1>All Listings (Admin)</h1>

<?php if (empty($listings)): ?>
    <p>No listings yet.</p>
<?php else: ?>
    <?php foreach ($listings as $listing): ?>
        <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
            <h3><?= htmlspecialchars($listing['title']) ?></h3>
            <p><?= htmlspecialchars($listing['description']) ?></p>
            <p><strong>Status:</strong> <?= htmlspecialchars($listing['status']) ?></p>

            <form method="POST" action="admin-listings.php">
                <input type="hidden" name="listing_id" value="<?= $listing['id'] ?>">
                <button type="submit" name="new_status" value="approved">Approve</button>
                <button type="submit" name="new_status" value="rejected">Reject</button>
            </form>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<a href="dashboard.php">Back to dashboard</a>