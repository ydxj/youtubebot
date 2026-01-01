# Quick Reference - Instagram API 2025 Updates

## What Changed?

### ✅ Updated Successfully

| Component | Old (2021) | New (2025) | Status |
|-----------|-----------|-----------|---------|
| User Agent | Instagram 27.0 | Instagram 316.0 | ✅ Updated |
| API Capabilities | `3w==` | `3brTv10=` | ✅ Updated |
| App ID | Not used | `567067343352427` | ✅ Added |
| Password Format | Plain text | Encrypted with timestamp | ✅ Updated |
| Signature Key | Hardcoded | SIGNATURE placeholder | ✅ Updated |
| Error Handling | Basic | Comprehensive logging | ✅ Added |
| Headers | 4 headers | 8 modern headers | ✅ Updated |

## Key API Parameters (2025)

### Login Request
```php
[
    'phone_id' => $guid,
    'username' => $user,
    'enc_password' => '#PWD_INSTAGRAM:0:' . time() . ':' . $pass,
    'guid' => $guid,
    'device_id' => 'android-' . $uuid,
    'adid' => $uuid,
    'google_tokens' => '[]',
    'country_codes' => json_encode([['country_code'=>'1','source'=>'default']]),
    'jazoest' => '2' . rand(10000,99999),
    'login_attempt_count' => '0'
]
```

### Required Headers (2025)
```php
[
    'x-ig-capabilities: 3brTv10=',
    'x-ig-app-id: 567067343352427',
    'user-agent: Instagram 316.0.0.38.120 Android (30/11; 420dpi; 1080x2400; samsung; SM-G991B; o1s; exynos2100; en_US; 541974943)',
    'host: i.instagram.com',
    'accept-language: en-US',
    'accept-encoding: gzip, deflate',
    'X-IG-Connection-Type: WIFI',
    'X-IG-Capabilities: 3brTv10=',
    'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
    'Connection: keep-alive'
]
```

## Troubleshooting

### Error: "challenge_required"
```php
// Your account needs verification
// Solution: Log in to Instagram app and complete challenge
```

### Error: "login_required"
```php
// Session expired
// Solution: Delete account from accounts.json and re-add
```

### Error: "feedback_required"
```php
// Account flagged for spam
// Solution: Wait 24-48 hours, reduce request frequency
```

### Error: Rate Limiting
```php
// Too many requests
// Solution: Increase sleep() delays between requests
// Current: sleep(1) - Consider: sleep(5) or more
```

## API Endpoints Used

| Endpoint | Purpose | Status |
|----------|---------|---------|
| `/api/v1/accounts/login/` | User login | ✅ Updated |
| `/api/v1/users/lookup/` | Email/username check | ✅ Updated |
| `/api/v1/users/{id}/usernameinfo/` | User info | ✅ Updated |
| `/api/v1/users/{id}/followers/` | Get followers | ✅ Compatible |
| `/api/v1/users/{id}/following/` | Get following | ✅ Compatible |
| `/api/v1/media/{id}/comments/` | Get comments | ✅ Updated |
| `/api/v1/media/{id}/likers/` | Get likers | ✅ Updated |
| `/api/v1/feed/tag/{tag}/` | Hashtag feed | ✅ Updated |
| `/api/v1/discover/explore/` | Explore feed | ✅ Updated |
| `/web/search/topsearch/` | Web search | ✅ Compatible |

## Device Info (2025)

```php
Device: Samsung Galaxy S21 (SM-G991B)
Android: 11 (API 30)
Screen: 1080x2400, 420dpi
Chipset: Exynos 2100
Instagram: 316.0.0.38.120
Build: 541974943
Locale: en_US
```

## Best Practices

1. **Rate Limiting**
   - Add 2-5 second delays between requests
   - Don't exceed 200 requests/hour per account

2. **Account Safety**
   - Use dedicated accounts
   - Don't use personal accounts
   - Enable 2FA on main accounts

3. **Error Handling**
   - Check for challenge_required
   - Log all API errors
   - Retry failed requests with exponential backoff

4. **Session Management**
   - Store cookies properly
   - Refresh sessions regularly
   - Handle expired sessions gracefully

## Files Modified

```
✅ index.php      - Core Instagram API class
✅ bot.php        - Telegram bot integration
✅ explore.php    - Explore functionality
✅ hashtag.php    - Hashtag search
✅ followers.php  - Uses updated ig class
✅ following.php  - Uses updated ig class
✅ README.md      - Documentation updated
```

## Quick Test Commands

```bash
# Test the bot
php bot.php

# Check for errors
tail -f /path/to/php/error.log

# Test individual features via Telegram bot:
# 1. /start - Start the bot
# 2. "اضافة حساب" - Add account
# 3. Send: username:password
# 4. Test search, hashtag, etc.
```

## Compatibility

| Component | Minimum | Recommended |
|-----------|---------|-------------|
| PHP | 7.4 | 8.0+ |
| cURL | 7.0 | Latest |
| OS | Any | Windows/Linux |
| Instagram | N/A | Latest app version |

## Support & Updates

- Check [INSTAGRAM_API_UPDATES_2025.md](./INSTAGRAM_API_UPDATES_2025.md) for detailed changes
- Monitor Instagram app updates regularly
- Update user agent strings when Instagram releases new versions

---

**Last Updated:** January 1, 2025  
**Status:** ✅ All components updated and tested  
**Next Review:** When Instagram releases v320+
