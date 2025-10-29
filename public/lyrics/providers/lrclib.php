<?php
// public/lyrics/providers/lrclib.php
// Returns a callable ($artist,$title) => array|null
return function(string $artist, string $title){
  // TODO: implement LRCLIB HTTP calls here (server-side).
  // Expected return shape: for synced lyrics
  // [ 'synced' => true, 'lines' => [ ['t'=>ms,'text'=>'...'], ... ], 'attribution' => 'LRCLIB' ]
  // or for plain text fallback
  // [ 'synced' => false, 'text' => "...", 'attribution' => 'LRCLIB' ]
  return null;
};
