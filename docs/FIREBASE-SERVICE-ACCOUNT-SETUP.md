# 🔑 Cara Dapatkan Service Account Key untuk FCM V1 API

## 🎯 Langkah-langkah:

### Step 1: Buka Firebase Console

1. Go to: https://console.firebase.google.com/project/sempat-lms/settings/serviceaccounts/adminsdk
   
   Atau navigasi manual:
   - Firebase Console → **Project Settings** (ikon gear)
   - Tab **Service accounts**

### Step 2: Generate New Private Key

1. Pastikan kamu di tab **"Firebase Admin SDK"**
2. Pilih **Node.js** atau **PHP** (tidak masalah, JSON-nya sama)
3. Klik tombol **"Generate new private key"**
4. Popup konfirmasi muncul → Klik **"Generate key"**
5. File JSON akan otomatis terdownload ke komputer kamu
   - Nama file: `sempat-lms-firebase-adminsdk-xxxxx.json`

### Step 3: Simpan File di Laravel Project

1. **Upload file JSON** ke folder `storage/app/` di Laravel project
2. Rename file menjadi: `firebase-service-account.json`
3. Path lengkap: `storage/app/firebase-service-account.json`

**PENTING:** Jangan commit file ini ke Git! Sudah ada di `.gitignore`

### Step 4: Update .env

Tambahkan path ke `.env`:

```env
# Firebase Service Account (untuk V1 API)
FIREBASE_SERVICE_ACCOUNT_PATH=storage/app/firebase-service-account.json
```

---

## ✅ Verifikasi File JSON

File JSON harus berisi:

```json
{
  "type": "service_account",
  "project_id": "sempat-lms",
  "private_key_id": "abc123...",
  "private_key": "-----BEGIN PRIVATE KEY-----\n...\n-----END PRIVATE KEY-----\n",
  "client_email": "firebase-adminsdk-xxxxx@sempat-lms.iam.gserviceaccount.com",
  "client_id": "123456789...",
  "auth_uri": "https://accounts.google.com/o/oauth2/auth",
  "token_uri": "https://oauth2.googleapis.com/token",
  "auth_provider_x509_cert_url": "https://www.googleapis.com/oauth2/v1/certs",
  "client_x509_cert_url": "https://www.googleapis.com/robot/v1/metadata/x509/firebase-adminsdk-xxxxx%40sempat-lms.iam.gserviceaccount.com"
}
```

**Key penting:** `project_id`, `private_key`, `client_email`

---

## 🔐 Security Best Practices

### Di Development (.env):
```env
FIREBASE_SERVICE_ACCOUNT_PATH=storage/app/firebase-service-account.json
```

### Di Production (.env.production):
```env
# Gunakan path absolut atau environment variable
FIREBASE_SERVICE_ACCOUNT_PATH=/var/www/html/storage/app/firebase-service-account.json
```

### Permissions:
```bash
# Set permission agar hanya Laravel yang bisa baca
chmod 600 storage/app/firebase-service-account.json
chown www-data:www-data storage/app/firebase-service-account.json  # Di production
```

---

## 🚀 Setelah File JSON di Tempat:

Saya akan update `FcmChannel.php` untuk menggunakan V1 API dengan Service Account authentication.

**Keuntungan V1 API:**
- ✅ Lebih secure (OAuth2)
- ✅ Support advanced features
- ✅ Tidak perlu enable Legacy API
- ✅ Future-proof (tidak akan deprecated)
- ✅ Error handling lebih baik

---

## 📸 Screenshot Lokasi Service Account:

Setelah buka link di atas, kamu akan lihat:

```
Firebase Admin SDK

Admin SDK configuration snippet
[Language: Node.js]

1. Download service account key
   Generate new private key  [Button]

2. Add SDK
   ...
```

Klik **"Generate new private key"** → Download → Simpan di `storage/app/`

---

**Setelah file JSON tersimpan, lanjut update code! 🎉**
