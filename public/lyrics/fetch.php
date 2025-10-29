<?php
// Stub lyrics provider. Expects ?artist=...&title=... and returns JSON.
header('Content-Type: application/json; charset=utf-8');

$artist = isset($_GET['artist']) ? trim($_GET['artist']) : '';
$title  = isset($_GET['title'])  ? trim($_GET['title'])  : '';

if ($artist === '' || $title === '') {
  echo json_encode(['lyrics' => '']);
  exit;
}

// TODO: integrate your real provider here (server-side).

$respText = 'Lyrics for ' . $artist . ' — ' . $title . ' are not configured yet.\n\nConfigure a provider in /lyrics/fetch.php.';

echo json_encode([
  'lyrics' => $respText
]);
