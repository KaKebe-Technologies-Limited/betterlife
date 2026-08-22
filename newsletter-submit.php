<?php
require_once __DIR__ . '/includes/functions.php';

$referer = $_SERVER['HTTP_REFERER'] ?? (SITE_URL . '/index.php');
$email = trim($_POST['email'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
    try {
        $stmt = $pdo->prepare("INSERT INTO newsletter_subscribers (email) VALUES (?)");
        $stmt->execute([$email]);
        flash_set('success', 'You are subscribed! Thanks for joining our mailing list.');
    } catch (PDOException $e) {
        flash_set('success', 'You are already subscribed — thank you!');
    }
} else {
    flash_set('error', 'Please enter a valid email address.');
}

redirect($referer);
