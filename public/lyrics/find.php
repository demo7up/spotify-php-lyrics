<?php
// public/lyrics/find.php (updated) - adds simple file cache and provider chain
require __DIR__ . '/../oauth/config.php';
ensureHttps();
header('Content-Type: application/json; charset=utf-8');

$artist = isset($_GET['artist']) ? trim($_GET['artist']) : '';
$title  = isset($_GET['title'])  ? trim($_GET['title'])  : '';

if ($artist === '' || $title === '') { http_response_code(400); echo json_encode(['error'=>'missing params']); exit; }

function normalize($s){
  $s = preg_replace('/\s*\(feat\.|featuring|with\)\s.*$/i','',$s);
  $s = preg_replace('/\s*-\s*(Remaster(ed)?|Live|Mono|Stereo|Version|Edit).*$/i','',$s);
  return trim($s);
}

$artistN = normalize($artist);
$titleN  = normalize($title);

// simple file cache
$cacheDir = __DIR__ . '/cache';
if (!is_dir($cacheDir)) { @mkdir($cacheDir, 0775, true); }
$key = preg_replace('/[^a-z0-9]+/i','_', strtolower($artistN.'__'.$titleN));
$cacheFile = $cacheDir . '/' . $key . '.json';
$ttl = 600; // 10 minutes

if (is_file($cacheFile) && (time() - filemtime($cacheFile) < $ttl)) {
  $json = file_get_contents($cacheFile);
  if ($json !== false) { echo $json; exit; }
}

$providers = [
  __DIR__ . '/providers/lrclib.php',
  __DIR__ . '/providers/genius.php'
];

$result = null;
foreach ($providers as $p) {
  if (!file_exists($p)) continue;
  $fn = include $p; // callable
  if (is_callable($fn)) {
    $res = $fn($artistN, $titleN);
    if ($res) { $result = $res; break; }
  }
}

if (!$result) {
  $result = [ 'synced'=>false, 'text'=>'', 'attribution'=>'No provider' ];
}

$out = json_encode($result, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
file_put_contents($cacheFile, $out);
echo $out;
