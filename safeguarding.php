<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/policies.php';
$pageTitle = 'Safeguarding';
$pageDescription = 'How BetterLife International keeps the people we work with safe, and how to raise a concern.';
$pageStyles = ['assets/css/about.css', 'assets/css/shop.css', 'assets/css/policy.css'];
require __DIR__ . '/includes/header.php';
policy_page($pdo, 'safeguarding', 'Safeguarding', 'Keeping the people we work with safe, and how to tell us if you are worried about anyone.');
require __DIR__ . '/includes/footer.php';
