<?php
// public/lyrics/providers/lrclib.php
// Returns a callable ($artist,$title) => array|null
// Uses the public LRCLIB API.

return function(string $artist, string $title){
  $base = 'https://lrclib.net/api/search';
  $qs   = http_build_query(['track_name'=>$title, 'artist_name'=>$artist]);
  $url  = $base . '?' . $qs;

  $ch = curl_init($url);
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 8,
    CURLOPT_HTTPHEADER => ['Accept: application/json']
  ]);
  $resp = curl_exec($ch);
  $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);
  if ($code !== 200 || !$resp) return null;

  $data = json_decode($resp, true);
  if (!is_array($data) || !count($data)) return null;

  // choose first result that has any lyrics
  $pick = null;
  foreach ($data as $row){
    if (!empty($row['syncedLyrics']) || !empty($row['plainLyrics'])) { $pick = $row; break; }
  }
  if (!$pick) return null;

  if (!empty($pick['syncedLyrics'])){
    // parse LRC-like text into array of {t,text}
    $lines = [];
    $text = $pick['syncedLyrics'];
    if (!is_string($text)) $text = '';
    $re = '/\[(\d{1,2}):(\d{2})(?:\.(\d{1,2}))?\]([^\n]*)/';
    if (preg_match_all($re, $text, $m, PREG_SET_ORDER)){
      foreach ($m as $mm){
        $min = intval($mm[1]);
        $sec = intval($mm[2]);
        $cs  = isset($mm[3]) && $mm[3] !== '' ? intval(str_pad($mm[3], 2, '0')) : 0;
        $t   = ($min*60 + $sec)*1000 + $cs*10;
        $lines[] = ['t'=>$t, 'text'=>trim($mm[4])];
      }
      usort($lines, function($a,$b){ return $a['t']<=>$b['t'];});
      if ($lines){
        return [
          'synced' => true,
          'lines'  => $lines,
          'attribution' => 'LRCLIB'
        ];
      }
    }
  }

  if (!empty($pick['plainLyrics'])){
    return [
      'synced' => false,
      'text'   => (string)$pick['plainLyrics'],
      'attribution' => 'LRCLIB'
    ];
  }

  return null;
};
