<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/policies.php';
$pageTitle = 'Privacy notice';
$pageDescription = 'What personal information the BetterLife International website collects, why, and what we do with it.';
$pageStyles = ['assets/css/about.css', 'assets/css/shop.css', 'assets/css/policy.css'];
require __DIR__ . '/includes/header.php';
policy_page($pdo, 'privacy', 'Privacy notice', 'What personal information this website collects, why we collect it, and the choices you have.');
require __DIR__ . '/includes/footer.php';
