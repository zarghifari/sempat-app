# 🚀 Push Notifications - Quick Start Checklist

## ✅ Setup Checklist (15-20 menit)

### Step 1: Firebase Project Setup (5 menit)
- [ ] Buka https://console.firebase.google.com/
- [ ] Create project "sempat-lms"
- [ ] Add Web App dengan nickname "sempat-web"
- [ ] Copy **Firebase Config** (apiKey, authDomain, dll)
- [ ] Paste ke [public/js/firebase-config.js](../public/js/firebase-config.js)

### Step 2: Get FCM Server Key (2 menit)
- [ ] Firebase Console → **Project Settings** → **Cloud Messaging** tab
- [ ] Enable **Cloud Messaging API (Legacy)** jika belum
- [ ] Copy **Server Key** (format: AAAA...)
- [ ] Add to `.env`: `FCM_SERVER_KEY=AAAA...`

### Step 3: Get VAPID Key (2 menit)
- [ ] Masih di **Cloud Messaging** tab
- [ ] Scroll ke **Web Push certificates**
- [ ] Klik **Generate key pair**
- [ ] Copy **Key pair** (format: BM...)
- [ ] Paste ke `public/js/firebase-config.js` di baris `vapidKey`

### Step 4: Update Environment Variables (2 menit)
Edit `.env` dan update:
```env
FCM_SERVER_KEY=AAAA1234567890...           # Dari Step 2
FIREBASE_API_KEY=AIzaSy...                 # Dari Firebase config
FIREBASE_AUTH_DOMAIN=sempat-lms.firebaseapp.com
FIREBASE_PROJECT_ID=sempat-lms
FIREBASE_STORAGE_BUCKET=sempat-lms.appspot.com
FIREBASE_MESSAGING_SENDER_ID=123456789
FIREBASE_APP_ID=1:123456789:web:abc...
FIREBASE_VAPID_KEY=BM12...                 # Dari Step 3
```

### Step 5: Clear Cache & Restart (1 menit)
```powershell
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# Restart server
php artisan serve
```

### Step 6: Test in Browser (5 menit)
- [ ] Buka http://127.0.0.1:8000
- [ ] Login ke aplikasi
- [ ] Browser akan minta **"Allow notifications"** → Klik **Allow**
- [ ] FCM token tersimpan otomatis (check console log)
- [ ] Complete sebuah lesson untuk test notifikasi
- [ ] Notifikasi akan muncul di:
  - ✅ In-app (bell icon)
  - ✅ Desktop/phone notification tray

### Step 7: Test Push Notification (5 menit)
```powershell
# Send test FCM notification
php test-fcm-notification.php

# Pilih user dan jenis notifikasi
# Notifikasi akan muncul di device!
```

---

## 📱 Files Created/Modified

### New Files:
- ✅ `public/js/firebase-config.js` - Firebase configuration
- ✅ `public/firebase-messaging-sw.js` - Service Worker untuk background notifications
- ✅ `public/js/push-notification.js` - Frontend FCM handler
- ✅ `app/Http/Controllers/Api/FcmController.php` - API untuk save FCM token
- ✅ `test-fcm-notification.php` - Test script
- ✅ `docs/FIREBASE-SETUP-GUIDE.md` - Detailed setup guide

### Modified Files:
- ✅ `resources/views/layouts/app.blade.php` - Added Firebase scripts
- ✅ `routes/web.php` - Added FCM API routes
- ✅ `.env` - Added Firebase config variables
- ✅ `database/migrations/*_add_fcm_token_to_users_table.php` - Already exists

---

## 🔥 Testing Scenarios

### Test 1: Foreground Notification (App Open)
1. Login ke app
2. Buka tab lain, run: `php test-fcm-notification.php`
3. Pilih user dan jenis notifikasi
4. Notifikasi akan muncul:
   - ✅ Toast notification di pojok kanan bawah
   - ✅ Browser notification (top-right)
   - ✅ Bell icon badge bertambah

### Test 2: Background Notification (App Minimized)
1. Login ke app, minimize browser/tab
2. Dari terminal: `php test-fcm-notification.php`
3. Send notification
4. ✅ System notification muncul di notification center
5. Klik notification → Browser/tab terbuka ke URL

### Test 3: App Closed (Browser Closed)
1. Login ke app sekali (FCM token saved)
2. **Tutup browser sepenuhnya**
3. Send notification via test script
4. ✅ Notification masih muncul (Service Worker bekerja!)
5. Klik notification → Browser terbuka otomatis

### Test 4: Automatic on Lesson Complete
1. Login dan enroll ke course
2. Complete sebuah lesson
3. ✅ Notification otomatis:
   - In-app notification (bell)
   - Push notification (FCM)
4. Check kedua notifikasi muncul

---

## 🎯 For Production Deployment

### Upload Files:
```bash
scp public/js/firebase-config.js user@server:/path/to/public/js/
scp public/firebase-messaging-sw.js user@server:/path/to/public/
scp public/js/push-notification.js user@server:/path/to/public/js/
scp app/Http/Controllers/Api/FcmController.php user@server:/path/to/app/Http/Controllers/Api/
```

### Update Production .env:
```env
FCM_SERVER_KEY=AAAA...  # Same as local
FIREBASE_API_KEY=...     # Same Firebase project
# ... (all Firebase config sama)
```

### Clear Production Cache:
```bash
ssh user@server
cd /var/www/html
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## 📱 For Future Mobile App (WebView)

### Android WebView
Firebase FCM **sudah compatible**! Hanya perlu:

1. **Add Firebase to Android project:**
   ```gradle
   dependencies {
       implementation 'com.google.firebase:firebase-messaging:23.0.0'
   }
   ```

2. **Enable WebView JavaScript:**
   ```java
   WebSettings webSettings = webView.getSettings();
   webSettings.setJavaScriptEnabled(true);
   ```

3. **FCM token handling:**
   - WebView akan auto-request permission
   - Token tersimpan ke server via existing API
   - Push notifications work out-of-the-box!

### iOS WebView
⚠️ **Limited support** karena iOS restrictions.

**Alternative:** 
- Gunakan native iOS push notifications
- Atau hybrid approach (native notification → open WebView)

---

## 🐛 Troubleshooting

### Notification tidak muncul?

**Check 1: Browser Permission**
- Chrome: Settings → Privacy → Notifications → Allow for localhost/domain
- Check: `Notification.permission` in console harus `"granted"`

**Check 2: Service Worker**
- Browser DevTools → Application → Service Workers
- Status harus "activated and running"
- Jika tidak: Unregister dan reload page

**Check 3: FCM Token**
```javascript
// Di browser console:
localStorage.getItem('fcm_token')
// Harus ada value (string panjang)
```

**Check 4: Server Key**
```powershell
php artisan tinker
config('services.fcm.server_key')
// Harus return AAAA... bukan null
```

**Check 5: Network**
- FCM butuh HTTPS di production
- Localhost OK untuk testing
- Check firewall/proxy tidak block FCM

### Error "Messaging is not supported"?
- Browser terlalu lama (update ke versi terbaru)
- Atau coba di Chrome/Firefox/Edge

### Token expired?
- Login lagi untuk refresh token
- Token auto-refresh setiap beberapa hari

---

## ✅ Success Indicators

Jika semua berfungsi:

- [x] No errors di browser console
- [x] Service Worker status: "activated"
- [x] FCM token tersimpan (check localStorage)
- [x] Test notification muncul di desktop/phone
- [x] Automatic notification saat complete lesson
- [x] Notification badge di bell icon update realtime
- [x] Klik notification buka URL yang benar

**🎉 Push Notifications READY!**

---

## 📊 Monitoring

### Check FCM Stats (Firebase Console)
1. Firebase Console → **Cloud Messaging**
2. Lihat grafik notifications sent/delivered
3. Monitor error rates

### Check User Tokens (Database)
```sql
SELECT COUNT(*) as total_users_with_fcm 
FROM users 
WHERE fcm_token IS NOT NULL;
```

### Check Notification Delivery
```powershell
# Send test to specific user
php test-fcm-notification.php

# Monitor Laravel logs
tail -f storage/logs/laravel.log | grep FCM
```

---

## 🚀 Next Steps

After push notifications working:

1. **Schedule Notifications** - Daily reminders, streak alerts
2. **Notification Preferences** - Let users choose notification types
3. **Rich Notifications** - Images, actions, buttons
4. **Topics/Groups** - Send to many users at once
5. **Analytics** - Track open rates, engagement

**Total setup time: ~20 minutes**
**Result: Full push notification system!** 🎉
