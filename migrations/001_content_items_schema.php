<?php
require_once __DIR__ . '/../includes/functions.php';

$pdo->exec("CREATE TABLE IF NOT EXISTS content_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  page VARCHAR(40) NOT NULL,
  section_key VARCHAR(80) NOT NULL,
  title VARCHAR(255) NOT NULL DEFAULT '',
  subtitle VARCHAR(255) NULL,
  body TEXT NULL,
  image VARCHAR(255) NULL,
  cta_label VARCHAR(120) NULL,
  cta_href VARCHAR(255) NULL,
  extra VARCHAR(120) NULL,
  sort_order INT NOT NULL DEFAULT 0,
  status TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_page_section (page, section_key, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

echo "content_items table ready.\n";
