<?php
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify()) {
    redirect(SITE_URL . '/contact.php');
}

$topics  = require __DIR__ . '/includes/contact-topics.php';
$name    = trim((string) ($_POST['name'] ?? ''));
$email   = trim((string) ($_POST['email'] ?? ''));
$phone   = trim((string) ($_POST['phone'] ?? ''));
$topic   = trim((string) ($_POST['subject'] ?? ''));
$about   = trim((string) ($_POST['about'] ?? ''));     // what the visitor came from, such as a project page
$message = trim((string) ($_POST['message'] ?? ''));

// A field people cannot see: only automated form-fillers complete it, so their messages are quietly dropped
if (trim((string) ($_POST['website'] ?? '')) !== '') {
    flash_set('success', 'Thank you. Your message has been received.');
    redirect(SITE_URL . '/contact.php#write');
}

if (!isset($topics[$topic])) $topic = 'General enquiry';
$subject = mb_substr($topic . ($about !== '' ? ': ' . $about : ''), 0, 200);

$problem = null;
if ($name === '' || $email === '' || $message === '') $problem = 'Please fill in your name, your email address and a message before sending.';
elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $problem = 'That email address does not look quite right. Please check it and send again.';
elseif (mb_strlen($name) > 150 || mb_strlen($email) > 150 || mb_strlen($phone) > 50 || mb_strlen($message) > 5000) $problem = 'Part of your message is longer than we can take. Please shorten it and send again.';

if ($problem) {
    // Keep what was typed, so nothing has to be written twice
    $_SESSION['contact_old'] = compact('name', 'email', 'phone', 'message') + ['subject' => $topic, 'about' => $about];
    flash_set('error', $problem);
    redirect(SITE_URL . '/contact.php#write');
}

$stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$name, $email, $phone, $subject, $message]);

flash_set('success', 'Thank you, ' . $name . '. Your message has reached the BetterLife team.');
redirect(SITE_URL . '/contact.php#write');
