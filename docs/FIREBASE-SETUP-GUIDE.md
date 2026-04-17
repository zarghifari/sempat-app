# 🔥 Firebase Cloud Messaging (FCM) - Setup Guide

## 📋 Step 1: Create Firebase Project (5 menit)

### 1.1 Buka Firebase Console
https://console.firebase.google.com/

### 1.2 Create New Project
1. Klik **"Add project"**
2. Project name: **sempat-lms** (atau nama lain)
3. Enable Google Analytics: **Optional** (boleh skip)
4. Klik **"Create project"**

### 1.3 Add Web App to Project
1. Di Firebase Console, klik **Settings icon** ⚙️ → **Project settings**
2. Scroll ke bawah, klik **"Add app"** → pilih **Web** (icon </>)
3. App nickname: **sempat-web**
4. ✅ Check **"Also set up Firebase Hosting"** (optional)
5. Klik **"Register app"**

### 1.4 Copy Firebase Config
Setelah register, akan muncul **Firebase SDK snippet**.

Copy bagian config ini:
```javascript
const firebaseConfig = {
  apiKey: "AIzaSy...",
  authDomain: "sempat-lms.firebaseapp.com",
  projectId: "sempat-lms",
  storageBucket: "sempat-lms.appspot.com",
  messagingSenderId: "123456789",
  appId: "1:123456789:web:abcdef123456"
};
```

**PASTE config ini ke file:** `public/js/firebase-config.js` (sudah saya buatkan template)

---

## 🔑 Step 2: Get FCM Server Key

### 2.1 Generate Server Key
1. Di Firebase Console → **Project Settings** → Tab **"Cloud Messaging"**
2. Scroll ke **"Cloud Messaging API (Legacy)"**
3. Jika belum enabled:
   - Klik **"⋮"** (3 dots) → **"Manage API in Google Cloud Console"**
   - Klik **"Enable"**
4. Copy **"Server key"** (format: `AAAA...`)

### 2.2 Add to .env
Buka file `.env` dan tambahkan:
```env
FCM_SERVER_KEY=AAAA1234567890abcdefghijklmnopqrstuvwxyz1234567890ABC
```

**IMPORTANT:** Ganti dengan Server Key yang kamu copy!

---

## 📱 Step 3: Get VAPID Key (untuk Web Push)

### 3.1 Generate Web Push Certificate
1. Masih di **Cloud Messaging** tab
2. Scroll ke **"Web configuration"** atau **"Web Push certificates"**
3. Klik **"Generate key pair"**
4. Copy **Key pair** yang muncul (format: `BM12...`)

### 3.2 Paste ke firebase-config.js
Buka `public/js/firebase-config.js` dan paste VAPID key di baris yang sudah disediakan.

---

## ✅ Verification Checklist

Setelah setup, pastikan file-file ini sudah terisi:

### ✅ `.env`
```env
FCM_SERVER_KEY=AAAA...  ← Server Key dari Firebase
```

### ✅ `public/js/firebase-config.js`
```javascript
const firebaseConfig = {
  apiKey: "...",           ← Dari Firebase
  authDomain: "...",       ← Dari Firebase
  projectId: "...",        ← Dari Firebase
  // ... dll
};

const vapidKey = "BM12...";  ← VAPID key dari Web Push certificate
```

---

## 🧪 Step 4: Test Push Notification

### 4.1 Clear Cache
```powershell
php artisan config:clear
php artisan cache:clear
```

### 4.2 Start Server
```powershell
php artisan serve
```

### 4.3 Open in Browser
```
http://127.0.0.1:8000
```

### 4.4 Enable Notifications
1. Login ke aplikasi
2. Browser akan minta **"Allow notifications"** → Klik **"Allow"**
3. FCM token akan tersimpan otomatis

### 4.5 Test dengan Complete Lesson
1. Pilih course
2. Complete lesson
3. **Notifikasi akan muncul di:**
   - ✅ Bell icon (in-app) 
   - ✅ Desktop/phone notification (push)

---

## 🚀 For Production (sempat-app.com)

### Update .env.production
```env
FCM_SERVER_KEY=AAAA...
```

### Update firebase-config.js
Ganti `authDomain` jika perlu:
```javascript
authDomain: "sempat-app.com",  // atau tetap pakai firebaseapp.com
```

### Upload files:
- `public/js/firebase-config.js`
- `public/firebase-messaging-sw.js`
- `public/js/push-notification.js`

---

## 📱 For Future Mobile App (WebView)

### Android WebView
Firebase akan **otomatis bekerja** di Android WebView dengan konfigurasi yang sama!

### iOS WebView
iOS memiliki **batasan** untuk push notifications di WebView. 
Alternative: Gunakan native iOS push notifications.

---

## 🐛 Troubleshooting

### Notifikasi tidak muncul?
1. **Check browser permission:**
   - Chrome: Settings → Privacy → Site Settings → Notifications
   - Allow untuk domain kamu

2. **Check FCM token:**
   ```javascript
   console.log('FCM Token:', localStorage.getItem('fcm_token'));
   ```

3. **Check server key:**
   ```powershell
   php artisan tinker
   config('services.fcm.server_key')
   ```

4. **Check Service Worker:**
   - Browser DevTools → Application → Service Workers
   - Status harus "activated and running"

### Error "Messaging is not supported"?
- Browser harus HTTPS (atau localhost untuk testing)
- Service Worker harus registered

---

## 📊 Testing Tools

### Send Test Notification (Firebase Console)
1. Firebase Console → **Cloud Messaging**
2. Klik **"Send test message"**
3. Paste FCM token dari user
4. Klik **"Test"**

### Send Test Notification (Command)
```powershell
php test-fcm-notification.php
```

---

## ✅ Summary Checklist

- [ ] Firebase project created
- [ ] Web app registered
- [ ] Firebase config copied to `firebase-config.js`
- [ ] FCM Server Key added to `.env`
- [ ] VAPID key added to `firebase-config.js`
- [ ] Service Worker registered
- [ ] Browser permission granted
- [ ] Test notification sent successfully

**Setup time:** ~15-20 menit
**Result:** Push notifications working! 🎉
