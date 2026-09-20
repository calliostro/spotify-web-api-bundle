# Upgrade Guide – v1.4.0

Version 1.4.0 introduces the resilient `SpotifyClient`, automatic in-memory token caching, and long-running process support (CLI commands & Symfony Messenger workers).

---

## 🌟 Highlights in v1.4.0

- **Resilient `SpotifyClient`:** Subclasses `SpotifyWebAPI\SpotifyWebAPI` with pre-emptive token freshness checks and automated single-retry handling when receiving expired token exceptions (`401 Unauthorized`).
- **CLI & Messenger Worker Stability:** Long-running processes can now run indefinitely without crashing after 60 minutes due to expired Spotify OAuth tokens.
- **In-Memory Token Caching:** Token requests are cached in memory (TTL: 55 minutes) to eliminate redundant token requests during web requests.
- **Runtime Credential Validation:** `SpotifyClientFactory` validates `client_id` and `client_secret` at runtime, supporting `%env(...)%` resolution with clear setup instructions when misconfigured.
- **Tooling & Standards:** Full PHPStan Level 8 static analysis, PSR-12 code style, and 100% test coverage.

---

## 🔄 Migration Guide

### 1. Update Autowiring Type-Hints (Recommended)

Replace type-hints of `SpotifyWebAPI\SpotifyWebAPI` with `Calliostro\SpotifyWebApiBundle\SpotifyClient`:

```diff
- use SpotifyWebAPI\SpotifyWebAPI;
+ use Calliostro\SpotifyWebApiBundle\SpotifyClient;

final class MusicController
{
-     public function search(SpotifyWebAPI $api): JsonResponse
+     public function search(SpotifyClient $api): JsonResponse
      {
          $results = $api->search('Billie Eilish', 'artist');
          return new JsonResponse($results);
      }
}
```

> [!NOTE]
> `SpotifyClient` extends `SpotifyWebAPI\SpotifyWebAPI`. Existing code type-hinting `SpotifyWebAPI` will continue to work seamlessly. However, the alias has been deprecated to encourage upgrading to `SpotifyClient`.

### 2. Service IDs

- **Recommended:** `calliostro_spotify_web_api.client`
- **Legacy (Deprecated):** `calliostro_spotify_web_api` (kept as alias for full backward compatibility)

### 3. Factory Service

- If you manually used `SpotifyWebApiFactory::factory(...)`, switch to `SpotifyClientFactory::createClient(...)` or instantiate `SpotifyClient` directly.

---

## 📋 Compatibility

- **PHP:** `^8.1` (tested on PHP 8.1–8.6)
- **Symfony:** `^6.4 || ^7.0 || ^8.0`
- **jwilsson/spotify-web-api-php:** `^6.0 || ^7.0`
