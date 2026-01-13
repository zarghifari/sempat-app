@echo off
echo ================================
echo   CSRF 419 Error Fix Script
echo ================================
echo.

echo Step 1: Clearing all cache...
call php artisan config:clear
call php artisan cache:clear
call php artisan route:clear
call php artisan view:clear
echo ✓ Cache cleared
echo.

echo Step 2: Clearing all sessions...
call php artisan tinker --execute="DB::table('sessions')->truncate();"
echo ✓ Sessions cleared
echo.

echo Step 3: Caching configuration...
call php artisan config:cache
echo ✓ Config cached
echo.

echo ================================
echo   Fix Complete!
echo ================================
echo.
echo Next steps:
echo 1. Close ALL browser tabs
echo 2. Clear browser cache (Ctrl+Shift+Delete)
echo 3. Open NEW Incognito/Private window
echo 4. Go to: http://127.0.0.1:8000/login
echo 5. Try login again
echo.
echo If still error, restart server:
echo   Ctrl+C (stop server)
echo   php artisan serve (start again)
echo.
pause
