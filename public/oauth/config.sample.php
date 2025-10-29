<?php
// Copy this file to config.php and fill in the values.
const SPOTIFY_CLIENT_ID     = 'YOUR_CLIENT_ID';
const SPOTIFY_CLIENT_SECRET = 'YOUR_CLIENT_SECRET';
const SPOTIFY_REDIRECT_URI  = 'https://lyrics.firemax.io/oauth/callback.php'; // adjust to your domain

// Adjust scopes as needed
const SPOTIFY_SCOPES = [
  'user-read-currently-playing',
  'user-read-playback-state'
];

function ensureHttps() {
  if (
    (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') &&
    (!isset($_SERVER['HTTP_X_FORWARDED_PROTO']) || $_SERVER['HTTP_X_FORWARDED_PROTO'] !== 'https')
  ) {
    $url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header("Location: $url", true, 301);
    exit;
  }
}
