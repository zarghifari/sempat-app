# 🔔 Troubleshooting: Notifikasi Tidak Muncul

## ✅ Status Saat Ini:

**Database:**
- ✅ 17 notifikasi tersimpan
- ✅ EventServiceProvider registered
- ✅ Listeners terdaftar dengan benar
- ✅ Queue worker berjalan sukses

**Yang Mungkin Bermasalah:**
- ⚠️ Browser belum refresh/reload
- ⚠️ JavaScript error di console
- ⚠️ Notifikasi sudah di-mark as read sebelumnya

---

## 🧪 Test: Cara Memastikan Notifikasi Muncul

### Step 1: Start Queue Worker (WAJIB!)

**Terminal 1 - Queue Worker (biarkan tetap berjalan):**
```powershell
php artisan queue:work --verbose
```

**Jangan tutup terminal ini!** Queue worker harus running terus untuk process notification jobs.

---

### Step 2: Start Laravel Server

**Terminal 2 - Laravel Server:**
```powershell
php artisan serve
```

Buka: http://127.0.0.1:8000

---

### Step 3: Hard Refresh Browser

**PENTING:** Hard refresh untuk load code terbaru!

```
Windows: Ctrl + Shift + R
Mac: Cmd + Shift + R
```

Atau:
1. Buka Developer Tools (F12)
2. Right-click Refresh button → **Empty Cache and Hard Reload**

---

### Step 4: Check Browser Console untuk Error

1. **Buka Developer Tools** (F12)
2. **Tab Console**
3. **Cari error** (text merah)

**Expected (no errors):**
```javascript
// Tidak ada error merah
// Mungkin ada log info tapi tidak ada error
```

**If you see errors:**
- Screenshot error message
- Share untuk debugging

---

### Step 5: Check Bell Icon di Header

**Bell icon harus ada badge angka:**

```
Header:
[Logo]  🔔•4  [Profile]
         ↑↑↑
     Badge merah
```

**Jika TIDAK ada badge:**
1. Open Developer Tools (F12)
2. Tab **Console**
3. Run command:
   ```javascript
   fetch('/api/notifications/unread-count', {
     headers: { 'X-Requested-With': 'XMLHttpRequest' }
   })
   .then(r => r.json())
   .then(d => console.log('Unread:', d))
   ```
4. Check response

---

### Step 6: Klik Bell Icon

**Klik bell icon:**
1. Dropdown harus muncul
2. Harus ada notifikasi

**Jika dropdown kosong:**
1. Check Console (F12) untuk error
2. Check Network tab:
   - Cari request ke `/api/notifications/recent`
   - Check status code (harus 200)
   - Check response body

---

### Step 7: Test Manual - Trigger Notification Baru

**Complete sebuah lesson:**

1. Login
2. Enroll ke course (jika belum)
3. Buka lesson
4. **Complete lesson** (klik tombol complete/selesai)

**Expected result:**
- Queue worker terminal menampilkan: "SendLessonCompletedNotification DONE"
- Bell badge bertambah +1
- Klik bell → notifikasi baru muncul

---

### Step 8: Check Halaman Full Notifications

**Buka:** http://127.0.0.1:8000/notifications

**Expected:**
- List semua notifikasi
- Tab filter: Semua / Belum Dibaca / Sudah Dibaca
- Bisa click individual notification
- Bisa mark all as read
- Bisa delete notification

---

## 🐛 Debugging Common Issues

### Issue 1: "Bell icon tidak ada badge"

**Penyebab:** JavaScript tidak fetch unread count

**Solusi:**
```javascript
// Browser Console (F12)
// Check Alpine.js loaded:
console.log(window.Alpine)  // Should not be undefined

// Force fetch notifications:
if (Alpine) {
  Alpine.$data(document.querySelector('[x-data="notificationBell"]')).fetchUnreadCount()
}
```

### Issue 2: "Queue worker tidak process jobs"

**Check:**
```powershell
# Count jobs
php artisan tinker --execute="echo DB::table('jobs')->count();"

# If > 0, queue worker not running
# Start queue worker:
php artisan queue:work
```

### Issue 3: "Notifikasi ada tapi badge = 0"

**Penyebab:** Semua notifikasi sudah di-mark as read

**Tes:**
```powershell
# Check unread count
php artisan tinker --execute="echo App\Models\User::first()->unreadNotifications()->count();"

# If 0, create new notification:
php test-module-course-notification.php
```

### Issue 4: "Error di console: CSRF token mismatch"

**Solusi:**
```powershell
# Restart server
php artisan serve
```

Then hard refresh browser (Ctrl+Shift+R)

---

## 📊 Verification Commands

**Terminal commands untuk check status:**

```powershell
# Check total notifikasi
php artisan tinker --execute="echo DB::table('notifications')->count();"

# Check jobs in queue
php artisan tinker --execute="echo DB::table('jobs')->count();"

# Check unread notifications untuk user pertama
php artisan tinker --execute="echo App\Models\User::first()->unreadNotifications()->count();"

# List recent notifications
php check-notifications.php
```

---

## ✅ Success Checklist

Jika semua berfungsi dengan benar:

- [ ] Queue worker running (terminal 1)
- [ ] Laravel server running (terminal 2)
- [ ] Browser hard refreshed (Ctrl+Shift+R)
- [ ] No errors di console (F12)
- [ ] Bell icon ada badge angka
- [ ] Klik bell → dropdown muncul
- [ ] Dropdown menampilkan notifikasi
- [ ] Click "Lihat Semua" → halaman notifications
- [ ] Complete lesson → badge +1

---

## 🚀 Quick Test Script

```powershell
# Terminal 1
php artisan queue:work

# Terminal 2
php artisan serve

# Terminal 3
php test-module-course-notification.php

# Check browser bell icon - badge harus bertambah!
```

---

**Jika masih tidak muncul setelah semua langkah:**
1. Screenshot browser console (F12)
2. Screenshot network tab request ke `/api/notifications/recent`
3. Run: `php check-notifications.php` dan screenshot outputnya
4. Share screenshots untuk debugging lebih lanjut

**PENTING:** Queue worker HARUS running terus-menerus!
