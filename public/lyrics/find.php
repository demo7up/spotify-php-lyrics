<?php
// public/lyrics/find.php - orchestrator; normalizes artist/title and tries providers
require __DIR__ . '/../oauth/config.php';
ensureHttps();
header('Content-Type: application/json; charset=utf-8');

$artist = isset($_GET['artist']) ? trim($_GET['artist']) : '';
$title  = isset($_GET['title'])  ? trim($_GET['title'])  : '';

if ($artist === '' || $title === '') {
  http_response_code(400); echo json_encode(['error'=>'missing params']); exit;
}

function normalize($s){
  $s = preg_replace('/\s*\(feat\.|featuring|with\)\s.*$/i','',$s);
  $s = preg_replace('/\s*-\s*(Remaster(ed)?|Live|Mono|Stereo|Version|Edit).*$/i','',$s);
  return trim($s);
}

$artistN = normalize($artist);
$titleN  = normalize($title);

// TODO: file cache (artist|title) => JSON

// Provider order: LRCLIB (synced), Genius (plain)
$providers = [
  __DIR__ . '/providers/lrclib.php',
  __DIR__ . '/providers/genius.php'
];

$result = null;
foreach ($providers as $p) {
  if (!file_exists($p)) continue;
  $fn = include $p; // each provider returns a callable ($artist,$title) => array|null
  if (is_callable($fn)) {
    $res = $fn($artistN, $titleN);
    if ($res) { $result = $res; break; }
  }
}

if (!$result) {
  echo json_encode([
    'synced' => false,
    'text'   => '',
    'attribution' => 'No provider'
  ]);
  exit;
}

echo json_encode($result);
