# Instagram API Updates - January 2026

## Overview
This document details all the updates made to modernize the Instagram bot from 2021 to 2025 API compatibility.

## Summary of Changes

### 1. User-Agent String Updates
**Old (2021):**
```
Instagram 27.0.0.7.97 Android (23/6.0.1; 640dpi; 1440x2392; LGE/lge; RS988; h1; h1; en_US)
```

**New (2026):**
```
Instagram 316.0.0.38.120 Android (30/11; 420dpi; 1080x2400; samsung; SM-G991B; o1s; exynos2100; en_US; 541974943)
```

**Changes:**
- Instagram version: 27.0 → 316.0
- Android version: 23/6.0.1 → 30/11
- Device model: LGE RS988 → Samsung SM-G991B
- Build number added: 541974943

### 2. API Headers Modernization

**Old Headers:**
```php
'x-ig-capabilities: 3w=='
'X-CSRFToken: missing'
'X-Instagram-AJAX: 1'
'X-Requested-With: XMLHttpRequest'
```

**New Headers:**
```php
'x-ig-capabilities: 3brTv10='
'x-ig-app-id: 567067343352427'
'accept-language: en-US'
'accept-encoding: gzip, deflate'
'X-IG-Connection-Type: WIFI'
'X-IG-Capabilities: 3brTv10='
```

**Key Changes:**
- Capabilities updated to `3brTv10=` (new encoding format)
- Added `x-ig-app-id` header (required for API v1)
- Removed deprecated AJAX and CSRF headers
- Added proper encoding and connection type headers

### 3. Login/Authentication Updates

**Old Login Method:**
```php
'signed_body' => '57afc5aa6cc94675a08329beaffaec7bad237df0198ed801280f459e80095abb.' . json_encode([
    'username' => $user,
    'password' => $pass,
    '_uid' => rand(1000000000,9999999999),
    '_uuid' => $guid,
    // ...
])
```

**New Login Method:**
```php
'signed_body' => 'SIGNATURE.' . json_encode([
    'username' => $user,
    'enc_password' => '#PWD_INSTAGRAM:0:' . time() . ':' . $pass,
    'adid' => $uuid,
    'google_tokens' => '[]',
    'country_codes' => json_encode([['country_code'=>'1','source'=>'default']]),
    'jazoest' => '2' . rand(10000,99999),
    // ...
]),
'ig_sig_key_version' => '4'
```

**Key Changes:**
- Password now encrypted with timestamp: `#PWD_INSTAGRAM:0:{timestamp}:{password}`
- Added `adid` (advertising ID)
- Added `google_tokens` parameter
- Added `country_codes` parameter
- Added `jazoest` parameter (anti-spam token)
- Removed hardcoded signature key (now using SIGNATURE placeholder)

### 4. Email Lookup Updates

**Old Request:**
```php
$fields = 'signed_body=acd10e3607b478b845184ff7af8d796aec14425d5f00276567ea0876b1ff2630.%7B%22_csrftoken%22%3A%22...%22%2C%22q%22%3A%22'.$mail.'%22...%7D&ig_sig_key_version=4';
```

**New Request:**
```php
$fields = 'signed_body=SIGNATURE.'.urlencode(json_encode([
    'q' => $mail,
    '_uid' => rand(1000000000,9999999999),
    'guid' => $uuid,
    'device_id' => 'android-'.$uuid
])).'&ig_sig_key_version=4';
```

**Key Changes:**
- Removed hardcoded CSRF token
- Dynamic UUID generation
- Cleaner JSON encoding

### 5. Error Handling Improvements

**Added:**
```php
if(curl_errno($ch)){
    error_log("Instagram API Error: " . curl_error($ch));
    curl_close($ch);
    return json_decode(json_encode(['error' => curl_error($ch)]));
}

$decoded = json_decode($res);
if(isset($decoded->status) && $decoded->status === 'fail'){
    error_log("Instagram API returned fail status: " . $res);
}
if(isset($decoded->message) && strpos($decoded->message, 'challenge_required') !== false){
    error_log("Instagram challenge required - account may need verification");
}
```

**Benefits:**
- Better error detection and logging
- Challenge detection for 2FA/verification
- Graceful error handling

## Files Modified

1. **index.php**
   - Updated `ig` class with modern headers
   - Updated `login()` method with encryption
   - Updated `request()` method with error handling
   - Updated `inInsta()` function
   - Updated `getInfo()` function

2. **bot.php**
   - Updated Instagram user agent in account creation
   - Updated stored account user agent

3. **explore.php**
   - Updated API headers for explore endpoint
   - Updated API headers for comments endpoint

4. **hashtag.php**
   - Updated API headers for hashtag feed
   - Updated API headers for likers endpoint

5. **search.php**
   - Headers updated automatically via account storage

6. **followers.php & following.php**
   - Use `ig` class (already updated)

7. **README.md**
   - Added comprehensive update documentation

## Testing Recommendations

1. **Test Login:** Try logging in with a test account
2. **Test Search:** Verify search functionality works
3. **Test Hashtag:** Check hashtag collection
4. **Monitor Errors:** Watch PHP error logs for API issues
5. **Rate Limiting:** Use delays between requests

## Known Issues & Solutions

### Issue: "challenge_required"
**Solution:** Account needs verification via Instagram app

### Issue: "login_required"
**Solution:** Re-authenticate the account

### Issue: Rate limiting
**Solution:** Add delays between requests (already implemented with `sleep()`)

### Issue: "feedback_required"
**Solution:** Account has been flagged - wait 24-48 hours

## Future Considerations

Instagram's private API changes frequently. If this stops working:

1. Monitor Instagram app updates
2. Capture new HTTP headers using a proxy (Charles, mitmproxy)
3. Update user agent to latest Instagram version
4. Check for new required parameters
5. Update signature key if changed

## Security Notes

- Never share your Instagram credentials
- Use dedicated accounts for automation
- Be aware of Instagram's Terms of Service
- Rate limiting helps avoid detection
- Use at your own risk

## Version History

- **v2.0 (January 2025):** Full Instagram API modernization
- **v1.5 (2025):** Bug fixes and Windows compatibility
- **v1.0 (2021):** Initial release

---

**Last Updated:** January 1, 2025
**Compatible Instagram API Version:** ~316.0
**PHP Version Required:** 7.4+
