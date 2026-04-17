# 🔑 Cara Enable Cloud Messaging API (Legacy) & Dapatkan Server Key

## 📸 Dari Screenshot Kamu:

### ✅ Yang Sudah Selesai:
- ✅ Firebase Cloud Messaging API (V1) - **Enabled**
- ✅ Sender ID: `901394659390`
- ✅ **VAPID Key sudah di-generate dan terupdate!**
  ```
  BFbiPhZF-LSYiJDqZT5VycLlrVcVkujQY7wX-JxqNneYXsZqfL_YyA3vF9duG2DGY5gEUFNoFiRi4ifgR4XLZO8
  ```

### ⚠️ Yang Masih Perlu:
**Cloud Messaging API (Legacy)** - Status: **Disabled**

---

## 🎯 Langkah untuk Enable Legacy API:

### Opsi 1: Via Firebase Console (Mudah)

Dari screenshot kamu saat ini:

1. **Di section "Cloud Messaging API (Legacy)"** yang status-nya "Disabled"
2. Klik **titik tiga vertikal** (⋮) di sebelah kanan
3. Pilih **"Manage API in Google Cloud Console"**
4. Halaman Google Cloud Console akan terbuka
5. Klik tombol biru **"ENABLE"**
6. Tunggu 10-30 detik sampai API enabled
7. **Kembali ke Firebase Console** (refresh halaman Cloud Messaging)
8. Sekarang **"Cloud Messaging API (Legacy)"** akan berubah jadi **"Enabled"** ✅
9. Di bawahnya akan muncul field **"Server key"** (format: AAAA...)
10. **Copy Server Key** tersebut

---

### Opsi 2: Via Google Cloud Console (Direct Link)

Jika opsi 1 tidak berhasil:

1. Buka: https://console.cloud.google.com/apis/library/fcm.googleapis.com?project=sempat-lms
2. Klik **"ENABLE"**
3. Tunggu beberapa detik
4. Kembali ke Firebase Console: https://console.firebase.google.com/project/sempat-lms/settings/cloudmessaging
5. Refresh halaman
6. Copy **Server key** yang muncul

---

## 📝 Paste Server Key ke `.env`:

Setelah dapat Server Key (format: `AAAAxxxxxxx...`), update file `.env`:

```env
FCM_SERVER_KEY=AAAAxxxxxxxxxxxxxxx_xxxxxxxxx  # ← Paste Server Key di sini
```

Ganti `YOUR_FCM_SERVER_KEY_HERE` dengan Server Key yang kamu copy.

---

## ✅ Verifikasi Setup Lengkap:

Setelah update Server Key, pastikan semua ada di `.env`:

```env
# Sudah ada ✅
FIREBASE_API_KEY=AIzaSyBikp1ZA4W37GHA3GkbtcRIidgsN6jxw
FIREBASE_AUTH_DOMAIN=sempat-lms.firebaseapp.com
FIREBASE_PROJECT_ID=sempat-lms
FIREBASE_STORAGE_BUCKET=sempat-lms.firebasestorage.app
FIREBASE_MESSAGING_SENDER_ID=98139465939
FIREBASE_APP_ID=1:98139465939:web:5c21b4bd683c124e2ff96f
FIREBASE_VAPID_KEY=BFbiPhZF-LSYiJDqZT5VycLlrVcVkujQY7wX-JxqNneYXsZqfL_YyA3vF9duG2DGY5gEUFNoFiRi4ifgR4XLZO8

# Masih perlu ⚠️
FCM_SERVER_KEY=YOUR_FCM_SERVER_KEY_HERE  # ← Update ini!
```

---

## 🧪 Testing Setelah Server Key Terupdate:

```powershell
# 1. Clear Laravel cache
php artisan config:clear

# 2. Start server
php artisan serve

# 3. Buka browser
# http://127.0.0.1:8000

# 4. Login → Allow notifications → Complete lesson
# Notification akan muncul! 🎉
```

---

## 🐛 Jika Ada Masalah:

### "Cannot find Server key field"
- Pastikan Legacy API sudah **Enabled**
- Refresh halaman Firebase Console
- Tunggu 1-2 menit setelah enable

### "API Enable button tidak ada"
- Kamu mungkin perlu verifikasi billing (gratis, tapi perlu kartu kredit)
- Atau gunakan akun Google lain yang sudah verified

### "Permission denied"
- Pastikan akun Google kamu adalah **Owner** project Firebase
- Buka Project Settings → Users and permissions → Check role

---

## 📸 Screenshot yang Benar (Setelah Enable):

Setelah berhasil enable Legacy API, screenshot-mu akan terlihat seperti ini:

```
✅ Cloud Messaging API (Legacy) - Enabled

Server key: AAAA1234567890abcdefghijklmnopqrstuvwxyz
```

Copy key tersebut dan paste ke `.env`!

---

**Setelah dapat Server Key, lanjut testing! 🚀**
