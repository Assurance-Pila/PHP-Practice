<?php
session_start();
require 'config/database.php';

$pdo = getConnection();
$stmt = $pdo->prepare("SELECT * FROM listings WHERE status = ?");
$stmt->execute(['approved']);
$listings = $stmt->fetchAll();
?>

<h1>Browse Listings</h1>

<?php if (empty($listings)): ?>
    <p>No approved listings yet.</p>
<?php else: ?>
    <?php foreach ($listings as $listing): ?>
        <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
            <h3><?= htmlspecialchars($listing['title']) ?></h3>
            <p><?= htmlspecialchars($listing['description']) ?></p>
            <a href="apply.php?listing_id=<?= $listing['id'] ?>">Apply</a>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<a href="dashboard.php">Back to dashboard</a>