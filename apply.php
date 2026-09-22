<?php
session_start();
require 'config/database.php';

// Access check: must be logged in AND must be an applicant
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'applicant') {
    header('Location: login.php');
    exit;
}

$listing_id = $_GET['listing_id'];
$applicant_id = $_SESSION['user_id'];
$message = '';
$canApply = true;

$pdo = getConnection();

// 1. Check the listing exists and is approved
$stmt = $pdo->prepare("SELECT * FROM listings WHERE id = ?");
$stmt->execute([$listing_id]);
$listing = $stmt->fetch();

if (!$listing || $listing['status'] !== 'approved') {
    $message = "This listing is not available for applications.";
    $canApply = false;
}

// 2. Check for an existing application by this applicant for this listing
if ($canApply) {
    $stmt = $pdo->prepare("SELECT * FROM applications WHERE listing_id = ? AND applicant_id = ?");
    $stmt->execute([$listing_id, $applicant_id]);
    $existing = $stmt->fetch();

    if ($existing) {
        $message = "You've already applied to this listing.";
        $canApply = false;
    }
}

// 3. Handle the actual submission
if ($canApply && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $cover_note = $_POST['cover_note'];

    $stmt = $pdo->prepare("INSERT INTO applications (listing_id, applicant_id, cover_note, status) VALUES (?, ?, ?, 'applied')");
    $stmt->execute([$listing_id, $applicant_id, $cover_note]);

    $message = "Application submitted!";
    $canApply = false; // prevent form from showing again after success
}
?>

<?php if ($message): ?>
    <p><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<?php if ($canApply): ?>
    <h1>Apply for <?= htmlspecialchars($listing['title']) ?></h1>
    <form method="POST" action="apply.php?listing_id=<?= htmlspecialchars($listing_id) ?>">
        <textarea name="cover_note" placeholder="Why are you a good fit?" required></textarea><br>
        <button type="submit">Submit Application</button>
    </form>
<?php endif; ?>

<a href="listings.php">Back to listings</a>