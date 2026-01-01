# Validation Checklist - Instagram API 2025 Update

## ✅ Completed Updates

### Core API Updates
- [x] Updated Instagram User-Agent from 27.0 to 316.0
- [x] Updated x-ig-capabilities from `3w==` to `3brTv10=`
- [x] Added x-ig-app-id header (567067343352427)
- [x] Removed deprecated X-Instagram-AJAX header
- [x] Removed deprecated X-CSRFToken: missing header
- [x] Removed deprecated X-Requested-With header
- [x] Updated Accept-Language headers
- [x] Added proper Connection-Type headers

### Authentication Updates
- [x] Updated login() method with encrypted password format
- [x] Added timestamp-based password encryption: `#PWD_INSTAGRAM:0:{time}:{pass}`
- [x] Added adid parameter
- [x] Added google_tokens parameter
- [x] Added country_codes parameter
- [x] Added jazoest anti-spam token
- [x] Updated signature key format
- [x] Added ig_sig_key_version parameter

### API Endpoint Updates
- [x] Updated /users/lookup/ endpoint headers
- [x] Updated /users/{id}/usernameinfo/ endpoint headers
- [x] Updated /users/{id}/followers/ endpoint (via ig class)
- [x] Updated /users/{id}/following/ endpoint (via ig class)
- [x] Updated /media/{id}/comments/ endpoint headers
- [x] Updated /media/{id}/likers/ endpoint headers
- [x] Updated /feed/tag/{tag}/ endpoint headers
- [x] Updated /discover/explore/ endpoint headers

### Error Handling Updates
- [x] Added curl error detection
- [x] Added error logging
- [x] Added challenge_required detection
- [x] Added fail status detection
- [x] Added graceful error returns

### File Updates
- [x] index.php - Core API class updated
- [x] bot.php - Telegram bot integration updated
- [x] explore.php - Explore feature updated
- [x] hashtag.php - Hashtag search updated
- [x] followers.php - Uses updated ig class
- [x] following.php - Uses updated ig class
- [x] search.php - Uses account storage (auto-updated)
- [x] post.php - No changes needed (commented out)

### Documentation Updates
- [x] Updated README.md with 2025 changes
- [x] Created INSTAGRAM_API_UPDATES_2025.md detailed guide
- [x] Created QUICK_REFERENCE.md for quick lookups
- [x] Created VALIDATION_CHECKLIST.md (this file)

## 🧪 Testing Checklist

### Basic Functionality Tests
- [ ] Test bot.php starts without errors
- [ ] Test Telegram bot connection
- [ ] Test /start command response
- [ ] Test account addition (login)
- [ ] Test account storage in accounts.json

### Instagram API Tests
- [ ] Test login with valid credentials
- [ ] Test login with invalid credentials (expect proper error)
- [ ] Test email/username lookup (inInsta function)
- [ ] Test user info retrieval (getInfo function)
- [ ] Test followers collection
- [ ] Test following collection
- [ ] Test hashtag search
- [ ] Test explore feed
- [ ] Test search functionality

### Error Handling Tests
- [ ] Test with expired session
- [ ] Test with rate limiting
- [ ] Test with challenge_required account
- [ ] Test with invalid account
- [ ] Verify error logs are created
- [ ] Verify graceful error responses

### Integration Tests
- [ ] Test full workflow: add account → search → collect users
- [ ] Test Telegram notifications during collection
- [ ] Test stop functionality during collection
- [ ] Verify collected usernames are saved correctly
- [ ] Test multiple accounts simultaneously

## 📊 Verification Commands

### Check User Agent Updates
```bash
grep -r "Instagram 316.0" *.php
# Should find matches in index.php and bot.php
```

### Check Header Updates
```bash
grep -r "3brTv10=" *.php
# Should find matches in updated files
```

### Check Old References (Should Return Nothing)
```bash
grep -r "Instagram 27.0" *.php
grep -r "3w==" *.php
grep -r "X-Instagram-AJAX" *.php
# All should return no results
```

### Verify Error Handling
```bash
grep -r "curl_errno" *.php
grep -r "error_log" *.php
# Should find matches in index.php
```

## 🔍 Post-Update Monitoring

### What to Monitor
1. **PHP Error Logs**
   - Location: Check php.ini for error_log path
   - Watch for: "Instagram API Error" messages
   - Watch for: "challenge_required" warnings

2. **Telegram Bot Responses**
   - Monitor for successful login messages
   - Monitor for collection progress updates
   - Watch for error messages sent to Telegram

3. **API Response Changes**
   - If Instagram updates API again, watch for:
     - New error messages
     - Changed response structures
     - New required parameters

### Success Indicators
✅ Accounts can be added successfully  
✅ User searches return results  
✅ Hashtag collection works  
✅ No PHP errors in logs  
✅ Telegram notifications work  

### Failure Indicators
❌ "challenge_required" on all accounts  
❌ "login_required" immediately after login  
❌ Empty responses from API  
❌ PHP curl errors  
❌ Telegram bot unresponsive  

## 🛠️ Maintenance Schedule

### Weekly
- Check if Instagram app has updated
- Monitor error logs
- Test login functionality

### Monthly
- Update user agent if Instagram released new version
- Review API changes in Instagram updates
- Test all major features

### As Needed
- When Instagram API changes detected
- When getting consistent errors
- When new Instagram version releases

## 📝 Known Working Configuration (2025)

```
Instagram Version: 316.0.0.38.120
Android Version: 30 (Android 11)
Device: Samsung SM-G991B
Capabilities: 3brTv10=
App ID: 567067343352427
PHP Version: 7.4+ (Recommended: 8.0+)
```

## 🚨 Troubleshooting Common Issues

### Issue: All logins fail with "challenge_required"
**Cause:** Instagram detected automation  
**Solution:** 
1. Use less aggressive collection settings
2. Add more delays between requests
3. Use residential IPs if possible
4. Create new accounts

### Issue: "feedback_required" errors
**Cause:** Account flagged for spam  
**Solution:**
1. Stop automation for 24-48 hours
2. Reduce request frequency
3. Switch to different account

### Issue: Empty API responses
**Cause:** API parameters changed  
**Solution:**
1. Capture new Instagram app traffic
2. Update headers to match
3. Check for new required parameters

### Issue: Session expires quickly
**Cause:** Instagram security measures  
**Solution:**
1. Re-login when needed
2. Store sessions properly
3. Handle session refresh

## ✅ Sign-Off

**Date Updated:** January 1, 2025  
**Updated By:** AI Assistant  
**Files Modified:** 8 files  
**Status:** ✅ All updates completed  
**Tested:** Pending user testing  

---

## Next Steps for User

1. **Backup Current Setup**
   ```bash
   # Backup accounts.json if it exists
   copy accounts.json accounts.json.backup
   ```

2. **Test the Updates**
   ```bash
   php bot.php
   ```

3. **Add a Test Account**
   - Use Telegram bot
   - Try adding an account
   - Verify it works

4. **Monitor for Issues**
   - Watch PHP error logs
   - Check Telegram responses
   - Report any errors

5. **Provide Feedback**
   - Report what works
   - Report what doesn't work
   - Share any error messages

---

**Status:** ✅ Ready for testing
