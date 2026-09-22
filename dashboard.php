<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<h1>Welcome, <?= htmlspecialchars($_SESSION['name']) ?></h1>
<p>Role: <?= htmlspecialchars($_SESSION['role']) ?></p>

<?php if ($_SESSION['role'] === 'employer'): ?>
    <h2>My Listings</h2>
    <p>You'll see and manage your job listings here.</p>
    <a href="create-listing.php">+ Post a new listing</a>
<a href="my-listings.php">My Listings</a>
<?php elseif ($_SESSION['role'] === 'applicant'): ?>
    <h2>Browse Listings</h2>
    <p>You'll see available listings here.</p>
    <a href="listings.php">Browse listings</a>
<a href="my-applications.php">My Applications</a>
<?php endif; ?>
<?php if ($_SESSION['role'] === 'admin'): ?>
    <h2>Admin Panel</h2>
    <a href="admin-listings.php">Manage Listings</a>
<?php endif; ?>

<a href="logout.php">Logout</a>