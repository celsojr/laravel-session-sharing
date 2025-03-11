### Laravel Multi-App Session Sharing Setup (Docker + Redis + Cookies)

This guide outlines how to set up **Laravel session sharing across multiple apps** using **Docker, Redis, and cookies**.

## 1. Prerequisites
Ensure you have:
- **Docker & Docker Compose** installed.
- A valid **hosts file** update:

```sh
# /etc/hosts (Linux/macOS) or C:\Windows\System32\drivers\etc\hosts (Windows)
127.0.0.1 app1.test.local
127.0.0.1 app2.test.local
```

## 2. Environment Configuration

### Laravel `.env` Settings (Shared Across All Apps)
```ini
APP_NAME=Laravel
APP_ENV=local
# Be sure to set the exact same APP_KEY for all related apps
APP_KEY=base64:iqPWOBQIfPWWmTsc9Wo26EFPDVXCsUizlyz12pRiBtQ=
APP_DEBUG=true
# Each app should have its own APP_URL as defined in the hosts file
# And don't forget the port
APP_URL=http://app1.test.local:8001

# Session & Cache
# QUEUE_CONNECTION=sync
# SESSION_DRIVER=file
# SESSION_DRIVER=redis
SESSION_DRIVER=cookie  # Change to 'redis' if needed
SESSION_DOMAIN=.test.local
# SESSION_SECURE_COOKIE=true # Set this when using HTTPS protocol. No need on local.
SESSION_COOKIE=mysharedsession # This is important when APP_NAME is different
SESSION_LIFETIME=120
QUEUE_CONNECTION=redis
CACHE_DRIVER=redis

# Redis Configuration
# REDIS_HOST=127.0.0.1
REDIS_HOST=redis
```

### Important Notes:
- **SESSION_DRIVER=cookie** → Stores session in cookies for cross-domain access.
- **SESSION_DOMAIN=.test.local** → Enables sharing across subdomains (Note the "." at the beginning).
- **Same-Site Policy:** (`config/session.php`)
  ```php
  # Must to be lax rather than none
  # No need to change the php.ini
  'same_site' => 'lax',
  ```

## 3. Laravel Session Testing
Use these routes to test **session sharing** between apps:

```php
// Set session in app1
Route::get('/set-session', function () {
    session(['user' => 'Laravel User from APP 1']);
    return "Session set in " . request()->getHost();
});

// Get session from app2
Route::get('/get-session', function () {
    return session('user', 'No session found') . " from " . request()->getHost();
});
```

### Testing
1. **Set a session in app1:**
   ```
   http://app1.test.local:8001/set-session
   ```
2. **Retrieve it from app2:**
   ```
   http://app2.test.local:8002/get-session
   ```
   Expected Output:  
   ```
   Laravel User from APP 1 from app2.test.local
   ```

## 4. Docker Commands
### Rebuild & Restart Everything
```sh
docker-compose build --no-cache
docker-compose up -d
docker-compose ps
```

### Check Logs
```sh
# Use these commands to wait for the migration and for artisan serving
# Before that, the apps may not be available yet in the browser
docker logs -f app1_container
docker logs -f app2_container
```

### Clear Laravel Config & Cache
```sh
docker exec -it app1_container php artisan config:clear
docker exec -it app1_container php artisan cache:clear
docker exec -it app2_container php artisan config:clear
docker exec -it app2_container php artisan cache:clear
docker-compose restart
```

### Verify the `large_cookie` Cookie with `cURL`
```sh
curl -I http://app1.test.local:8001
# Set-Cookie: large_cookie=AAA...
```

### Check Cookie Attributes (Secure, SameSite, HttpOnly)
Run this in your browser’s **Developer Tools (F12)** → **Application** → **Storage** → **Cookies** and check if the cookie is there.

If the cookie is missing, check the response headers via **Network** tab in **DevTools**:

1. Open **DevTools (F12)**.
2. Go to **Network** tab.
3. Refresh the page (Ctrl + R with Disable cache checked).
4. Click on the request and check **Headers** → **Cookie** then check the Response Cookies.
