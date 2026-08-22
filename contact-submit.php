<?php
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify()) {
    redirect(SITE_URL . '/contact.php');
}

$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$phone   = trim($_POST['phone'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $message === '') {
    flash_set('error', 'Please fill in your name, a valid email, and a message before sending.');
    redirect(SITE_URL . '/contact.php');
}

$stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$name, $email, $phone, $subject, $message]);

flash_set('success', 'Thank you, ' . $name . '! Your message has been received. Our team will get back to you soon.');
redirect(SITE_URL . '/contact.php');
