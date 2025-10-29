<?php
require __DIR__ . '/config.php';
ensureHttps();

$state = bin2hex(random_bytes(12));
setcookie('spotify_oauth_state', $state, [
  'expires'  => time()+600,
  'path'     => '/',
  'secure'   => true,
  'httponly' => true,
  'samesite' => 'Lax'
]);

$params = [
  'client_id'     => SPOTIFY_CLIENT_ID,
  'response_type' => 'code',
  'redirect_uri'  => SPOTIFY_REDIRECT_URI,
  'scope'         => implode(' ', SPOTIFY_SCOPES),
  'state'         => $state,
  'show_dialog'   => 'false'
];

header('Location: https://accounts.spotify.com/authorize?' . http_build_query($params));
exit;
