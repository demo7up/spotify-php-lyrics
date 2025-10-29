# spotify-php-lyrics

A cPanel-friendly, **PHP-only** Spotify lyrics viewer.

- No Node.js server or custom ports
- Uses your existing SSL at `https://lyrics.firemax.io/`
- OAuth handled via PHP endpoints under `/oauth`
- Tokens stored in HTTP-only cookies
- Pluggable lyrics providers (stub included)

## Quick Deploy (cPanel)
1. In Spotify Developer Dashboard, set **Redirect URI** to:  \n   `https://lyrics.firemax.io/oauth/callback.php`
2. Upload the **contents of `public/`** to your site’s docroot (e.g., `public_html/`).
3. Copy `public/oauth/config.sample.php` to `public/oauth/config.php` and fill in:
   - `SPOTIFY_CLIENT_ID`
   - `SPOTIFY_CLIENT_SECRET`
   - `SPOTIFY_REDIRECT_URI` (must match the value in step 1)
4. Visit `https://lyrics.firemax.io/` → click **Connect to Spotify**.
5. Edit `public/lyrics/fetch.php` to wire your preferred lyrics provider and return `{ "lyrics": "..." }`.

## Structure
```
public/
  .htaccess
  index.php
  assets/
    app.js
    styles.css
  oauth/
    config.sample.php
    config.php           # create from sample, not committed
    authorize.php
    callback.php
    token.php
    refresh.php
    spotify_current.php
    spotify_proxy.php
  lyrics/
    fetch.php
    README.md
```

## Security Notes
- Serve strictly over HTTPS (cPanel SSL takes care of it).
- Keep `config.php` out of version control.
- Consider rate limiting your generic `/oauth/spotify_proxy.php` or lock it to specific endpoints you need.
