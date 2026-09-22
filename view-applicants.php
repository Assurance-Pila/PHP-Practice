<?php
session_start();
require 'config/database.php';

// Access check: must be logged in AND must be an employer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employer') {
    header('Location: login.php');
    exit;
}

$employer_id = $_SESSION['user_id'];
$listing_id = $_GET['listing_id'];

$pdo = getConnection();

// Ownership check: does this listing actually belong to this employer?
$stmt = $pdo->prepare("SELECT * FROM listings WHERE id = ? AND employer_id = ?");
$stmt->execute([$listing_id, $employer_id]);
$listing = $stmt->fetch();

if (!$listing) {
    die("You don't have permission to view this listing's applicants.");
}

// Handle status update (accept/reject) if submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $application_id = $_POST['application_id'];
    $new_status = $_POST['new_status'];

    $stmt = $pdo->prepare("UPDATE applications SET status = ? WHERE id = ? AND listing_id = ?");
    $stmt->execute([$new_status, $application_id, $listing_id]);
}

// Fetch all applicants for this listing, joined with their user details
$stmt = $pdo->prepare("
    SELECT applications.id AS application_id, applications.cover_note, applications.status, applications.created_at,
           users.name, users.email
    FROM applications
    JOIN users ON applications.applicant_id = users.id
    WHERE applications.listing_id = ?
    ORDER BY applications.created_at DESC
");
$stmt->execute([$listing_id]);
$applicants = $stmt->fetchAll();
?>

<h1>Applicants for: <?= htmlspecialchars($listing['title']) ?></h1>

<?php if (empty($applicants)): ?>
    <p>No applicants yet.</p>
<?php else: ?>
    <?php foreach ($applicants as $applicant): ?>
        <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
            <h3><?= htmlspecialchars($applicant['name']) ?></h3>
            <p><?= htmlspecialchars($applicant['email']) ?></p>
            <p><?= htmlspecialchars($applicant['cover_note']) ?></p>
            <p><strong>Status:</strong> <?= htmlspecialchars($applicant['status']) ?></p>

            <form method="POST" action="view-applicants.php?listing_id=<?= $listing_id ?>">
                <input type="hidden" name="application_id" value="<?= $applicant['application_id'] ?>">
                <select name="new_status">
                    <option value="reviewed">Reviewed</option>
                    <option value="accepted">Accepted</option>
                    <option value="rejected">Rejected</option>
                </select>
                <button type="submit">Update Status</button>
            </form>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<a href="my-listings.php">Back to my listings</a>