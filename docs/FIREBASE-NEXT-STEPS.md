# 🔥 Firebase Setup - Langkah Selanjutnya

## ✅ Yang Sudah Selesai:
- ✅ Firebase project "sempat-lms" dibuat
- ✅ Web app ditambahkan
- ✅ Firebase config sudah tercopy ke `public/js/firebase-config.js` dan `.env`

---

## 🎯 Langkah Berikutnya (5 menit):

### Step 1: Dapatkan FCM Server Key

1. **Buka Firebase Console:** https://console.firebase.google.com/project/sempat-lms/settings/cloudmessaging

   Atau navigasi manual:
   - Firebase Console → **Project Settings** (ikon gear)
   - Tab **Cloud Messaging**

2. **Enable Cloud Messaging API (Legacy)**
   
   Jika muncul peringatan "Cloud Messaging API (Legacy) is disabled", klik:
   - **...** (titik tiga vertikal)
   - **Manage API in Google Cloud Console**
   - Klik tombol **ENABLE**
   - Tunggu beberapa detik, lalu kembali ke Firebase Console

3. **Copy Server Key**
   
   Di section **Cloud Messaging API (Legacy)**:
   - Cari field **Server key**
   - Format: `AAAA...` (string panjang)
   - Klik icon **copy** di sebelah kanan
   
   **Paste ke `.env`:**
   ```env
   FCM_SERVER_KEY=AAAA1234567890abcdefg...  # Paste Server Key di sini
   ```

---

### Step 2: Generate VAPID Key (Web Push Certificate)

Masih di halaman yang sama (**Cloud Messaging** tab), scroll ke bawah:

1. **Cari section "Web Push certificates"**
   
   Jika belum ada key:
   - Klik **Generate key pair**
   - Firebase akan generate key baru

2. **Copy Key yang muncul**
   
   Format: `BM12...` atau `BD...` (Base64 string panjang)
   
   **Paste ke 2 tempat:**
   
   **A. File `.env`:**
   ```env
   FIREBASE_VAPID_KEY=BM12abcdef...  # Paste VAPID Key di sini
   ```
   
   **B. File `public/js/firebase-config.js`:**
   ```javascript
   const vapidKey = "BM12abcdef...";  // Paste VAPID Key di sini
   ```

---

## 📝 Checklist Lengkap:

- [x] Firebase config tercopy (sudah otomatis)
- [ ] FCM Server Key tercopy ke `.env`
- [ ] VAPID Key tercopy ke `.env`
- [ ] VAPID Key tercopy ke `public/js/firebase-config.js`
- [ ] Run `php artisan config:clear`
- [ ] Test notification

---

## 🧪 Testing (Setelah Semua Setup)

### 1. Clear Cache Laravel:
```powershell
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### 2. Start Server:
```powershell
php artisan serve
```

### 3. Test di Browser:
1. Buka http://127.0.0.1:8000
2. Login ke aplikasi
3. Browser akan minta **"Allow notifications"** → Klik **Allow**
4. Check browser console (F12):
   ```
   ✅ Notification permission granted
   ✅ FCM Token: eyJhbGci...
   ✅ FCM token saved successfully
   ```

5. **Complete sebuah lesson** untuk trigger automatic notification
   - Notifikasi muncul di bell icon (in-app)
   - Notifikasi muncul di system (push notification)

### 4. Test Manual Push Notification:
```powershell
php test-fcm-notification.php
```
- Pilih user
- Pilih jenis notifikasi
- Verifikasi notifikasi muncul di device

---

## 🐛 Troubleshooting

### "Messaging is not supported in this browser"
- Update browser ke versi terbaru (Chrome 50+, Firefox 44+, Edge 79+)
- Atau gunakan browser lain

### "Permission denied"
- Browser console → Check `Notification.permission`
- Jika `"denied"`: Reset permission di browser settings
  - Chrome: Settings → Privacy → Notifications → Hapus sempat-lms/localhost
  - Reload page

### "Service Worker failed to register"
- Check ada error di console
- Pastikan file `public/firebase-messaging-sw.js` exists
- Reload page dengan Ctrl+Shift+R (hard reload)

### "FCM token not saved"
- Check `.env` sudah ada FCM_SERVER_KEY
- Run `php artisan config:clear`
- Check network tab, ada request ke `/api/fcm-token` (status 200)

---

## ⏭️ Setelah Testing Berhasil

Kalau semua berfungsi lokal:

### Deploy ke Production:
1. Copy `.env` config ke server production
2. Upload file-file baru (firebase-config.js, firebase-messaging-sw.js, dll)
3. Clear cache production: `php artisan config:clear`
4. Test di https://sempat-app.com

### Mobile App (Future):
- **Android WebView:** Langsung compatible! 
- Add Firebase to Android project
- Enable JavaScript di WebView
- Done!

---

## 📊 Firebase Free Tier Limits

✅ Yang gratis:
- **Unlimited notifications** per month
- **Analytics** (unlimited events)
- **Cloud Messaging** (unlimited push)
- **1GB storage** (cukup untuk config/tokens)
- **10GB bandwidth/month**

⚠️ Berbayar (opsional):
- Google Analytics 4 advanced features
- Cloud Functions (jika pakai scheduled notifications)

**Untuk app ini:** Free tier **lebih dari cukup**! 🎉

---

**Setelah copy FCM Server Key & VAPID Key, lanjut ke testing!** 🚀
