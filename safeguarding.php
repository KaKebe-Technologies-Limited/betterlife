<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/policies.php';
$pageTitle = 'Safeguarding';
$pageDescription = 'BetterLife International’s Child Safeguarding, Anti-Fraud and Corruption, and Whistleblower Protection policies, and how to raise a concern.';
$pageStyles = ['assets/css/about.css', 'assets/css/shop.css', 'assets/css/policy.css'];
require __DIR__ . '/includes/header.php';
policy_page($pdo, 'safeguarding', 'Safeguarding', 'BetterLife’s policies for keeping children safe, preventing fraud and corruption, and protecting anyone who reports a concern, and how to tell us if you are worried.');
require __DIR__ . '/includes/footer.php';
