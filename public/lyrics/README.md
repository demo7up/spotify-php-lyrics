# Lyrics Providers

`fetch.php` is a stub. You can integrate a real provider by calling its API server-side and returning JSON to the browser.

## Example (Pseudo-code)
```php
$apiKey  = 'YOUR_KEY';
$artist  = urlencode($_GET['artist']);
$title   = urlencode($_GET['title']);
$url = "https://example-lyrics-api.com/search?artist={$artist}&title={$title}&apikey={$apiKey}";

$ch = curl_init($url);
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_TIMEOUT => 10
]);
$resp = curl_exec($ch);
curl_close($ch);
// parse response and echo json_encode(['lyrics' => $text]);
```
