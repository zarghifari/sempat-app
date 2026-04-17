# ✅ Migration dari Legacy API ke V1 API - COMPLETE!

## 🎉 Perubahan yang Sudah Dilakukan:

### 1. Updated: `app/Channels/FcmChannel.php`
**Sebelum:** Menggunakan Legacy HTTP API dengan Server Key
```php
'Authorization' => 'key=' . $serverKey
POST https://fcm.googleapis.com/fcm/send
```

**Sekarang:** Menggunakan V1 API dengan OAuth2 (Service Account)
```php
'Authorization' => 'Bearer ' . $accessToken
POST https://fcm.googleapis.com/v1/projects/{project_id}/messages:send
```

**Features baru:**
- ✅ OAuth2 authentication (lebih secure)
- ✅ Access token caching (50 menit)
- ✅ JWT generation dari Service Account
- ✅ Support advanced FCM features
- ✅ Better error handling & logging

---

### 2. Updated: `config/services.php`
```php
'fcm' => [
    // V1 API (recommended)
    'project_id' => env('FIREBASE_PROJECT_ID'),
    'service_account_path' => env('FIREBASE_SERVICE_ACCOUNT_PATH'),
],
```

---

### 3. Updated: `.env`
```env
# V1 API - Service Account Authentication
FIREBASE_SERVICE_ACCOUNT_PATH=storage/app/firebase-service-account.json
FIREBASE_PROJECT_ID=sempat-lms

# Frontend config (sudah ada)
FIREBASE_API_KEY=AIzaSyBikp1ZA4W37GHA3GkbtcRIidgsN6jxw
FIREBASE_AUTH_DOMAIN=sempat-lms.firebaseapp.com
FIREBASE_STORAGE_BUCKET=sempat-lms.firebasestorage.app
FIREBASE_MESSAGING_SENDER_ID=98139465939
FIREBASE_APP_ID=1:98139465939:web:5c21b4bd683c124e2ff96f
FIREBASE_VAPID_KEY=BFbiPhZF-...

# Legacy API (deprecated, tidak digunakan)
# FCM_SERVER_KEY=YOUR_FCM_SERVER_KEY_HERE
```

---

### 4. Created: Test Script V1 API
**File:** `test-fcm-v1-notification.php`
- Interactive CLI untuk test push notifications
- Support semua jenis notifikasi
- Custom message support
- Detailed error messages & troubleshooting

---

### 5. Created: Documentation
**File:** `docs/FIREBASE-SERVICE-ACCOUNT-SETUP.md`
- Step-by-step Service Account setup
- Security best practices
- Troubleshooting guide

---

## 🎯 Langkah Selanjutnya (5 menit):

### Step 1: Download Service Account JSON

**Link langsung:**
https://console.firebase.google.com/project/sempat-lms/settings/serviceaccounts/adminsdk

**Cara manual:**
1. Firebase Console → **Project Settings** (ikon gear)
2. Tab **Service accounts**
3. Klik **"Generate new private key"**
4. File JSON akan terdownload

**Contoh nama file:**
```
sempat-lms-firebase-adminsdk-xxxxx-1234567890.json
```

---

### Step 2: Simpan File di Project

1. **Copy file JSON** ke folder Laravel project
2. **Rename** menjadi: `firebase-service-account.json`
3. **Pindahkan** ke: `storage/app/firebase-service-account.json`

**Path lengkap:**
```
d:\Ghazi\sempat-app\storage\app\firebase-service-account.json
```

**PENTING:** Jangan commit file ini ke Git! Sudah di `.gitignore`

---

### Step 3: Verify JSON Content

Buka file JSON, pastikan ada fields ini:
```json
{
  "type": "service_account",
  "project_id": "sempat-lms",
  "private_key_id": "abc123...",
  "private_key": "-----BEGIN PRIVATE KEY-----\n...",
  "client_email": "firebase-adminsdk-xxxxx@sempat-lms.iam.gserviceaccount.com",
  "client_id": "...",
  "auth_uri": "https://accounts.google.com/o/oauth2/auth",
  "token_uri": "https://oauth2.googleapis.com/token"
}
```

✅ **Key fields:** `project_id`, `private_key`, `client_email`

---

### Step 4: Test Notification

```powershell
# Clear cache
php artisan config:clear

# Run test script
php test-fcm-v1-notification.php
```

**Expected output:**
```
✅ Service Account Path: storage/app/firebase-service-account.json
✅ Project ID: sempat-lms
✅ JSON file exists
✅ Service Account Email: firebase-adminsdk-xxxxx@sempat-lms.iam.gserviceaccount.com

📊 Found X user(s) with FCM token:
  ✅ User Name (ID: 1)
     Token: abc123...

Pilih user untuk test notification (1-X): 1
...
🚀 Sending notification via FCM V1 API...

✅ Notification sent successfully!
```

---

### Step 5: Test di Browser

```powershell
# Start server
php artisan serve

# Buka browser
# http://127.0.0.1:8000
```

1. **Login** ke aplikasi
2. **Allow notifications** jika belum
3. **Complete lesson** untuk trigger automatic notification
4. **Check notification** muncul di:
   - System notification tray (desktop/mobile)
   - Bell icon (in-app)

---

## 🎁 Keuntungan V1 API:

| Feature | Legacy API | **V1 API** |
|---------|-----------|-----------|
| **Status** | Deprecated (2024) | ✅ **Active** |
| **Authentication** | Server Key (static) | OAuth2 (dynamic token) |
| **Security** | Medium | ✅ **High** (rotating tokens) |
| **Setup Error** | ❌ Enable API failed | ✅ **Already enabled!** |
| **Token Lifespan** | Permanent | 1 hour (cached 50 min) |
| **Advanced Features** | Limited | ✅ Full support |
| **Topics/Groups** | Basic | ✅ Advanced |
| **Analytics** | Basic | ✅ Detailed |

---

## 🔐 Security Notes:

### Development:
```env
FIREBASE_SERVICE_ACCOUNT_PATH=storage/app/firebase-service-account.json
```

### Production:
```bash
# Set proper permissions
chmod 600 storage/app/firebase-service-account.json
chown www-data:www-data storage/app/firebase-service-account.json
```

**Never commit:**
- ❌ `firebase-service-account.json`
- ❌ Private keys
- ❌ Access tokens

**Safe to expose (public):**
- ✅ `FIREBASE_API_KEY`
- ✅ `FIREBASE_AUTH_DOMAIN`
- ✅ `FIREBASE_PROJECT_ID`
- ✅ `FIREBASE_VAPID_KEY`

---

## 📊 How It Works:

### Authentication Flow:
```
1. Load Service Account JSON
2. Create JWT (signed with private key)
3. Exchange JWT for OAuth2 access token
4. Cache token (50 minutes)
5. Use token to send FCM notification
```

### First Request:
```
Load JSON → Generate JWT → Get OAuth2 token → Send notification
(~500ms)
```

### Subsequent Requests (cached):
```
Use cached token → Send notification
(~100ms)
```

---

## 🐛 Troubleshooting:

### "Service Account JSON file not found"
```powershell
# Check file exists
ls storage/app/firebase-service-account.json

# If not, download from Firebase Console
```

### "Invalid Service Account JSON format"
```powershell
# Verify JSON content
cat storage/app/firebase-service-account.json | ConvertFrom-Json

# Should show: private_key, client_email, project_id
```

### "Failed to get OAuth2 access token"
- Check `private_key` format (must have `\n` newlines)
- Check `client_email` is valid
- Check internet connection
- Check Laravel logs: `storage/logs/laravel.log`

### "FCM V1 notification failed"
- Check `FIREBASE_PROJECT_ID` in .env matches JSON
- Check FCM token is valid (user login recently?)
- Check Laravel logs for error details

---

## ✅ Summary:

**Migration Complete:**
- ✅ FcmChannel updated to V1 API
- ✅ OAuth2 authentication implemented
- ✅ Config updated
- ✅ .env updated
- ✅ Test script created
- ✅ Documentation created

**Next Action:**
1. Download Service Account JSON
2. Save to `storage/app/firebase-service-account.json`
3. Run `php test-fcm-v1-notification.php`
4. See push notifications! 🎉

---

**No need to enable Legacy API! V1 API is better and already enabled!** ✨
