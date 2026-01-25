#!/usr/bin/env php
<?php
/**
 * Quick Test Script untuk Time Tracker
 * 
 * Verifikasi semua komponen tracker sudah terpasang dengan benar
 */

echo "🧪 Time Tracker Verification Script\n";
echo "====================================\n\n";

$errors = [];
$warnings = [];
$success = [];

// 1. Check JavaScript File
echo "1. Checking JavaScript file...\n";
$jsPath = __DIR__ . '/public/js/study-time-tracker.js';
if (file_exists($jsPath)) {
    $size = filesize($jsPath);
    echo "   ✅ study-time-tracker.js found (" . number_format($size) . " bytes)\n";
    $success[] = "JavaScript file exists";
    
    // Check if it contains the class
    $content = file_get_contents($jsPath);
    if (strpos($content, 'class StudyTimeTracker') !== false) {
        echo "   ✅ StudyTimeTracker class found\n";
        $success[] = "StudyTimeTracker class exists";
    } else {
        echo "   ❌ StudyTimeTracker class NOT found\n";
        $errors[] = "StudyTimeTracker class missing in JS file";
    }
} else {
    echo "   ❌ study-time-tracker.js NOT found\n";
    $errors[] = "JavaScript file missing: $jsPath";
}

// 2. Check Service Class
echo "\n2. Checking Service class...\n";
$servicePath = __DIR__ . '/app/Services/TimeTrackingService.php';
if (file_exists($servicePath)) {
    echo "   ✅ TimeTrackingService.php found\n";
    $success[] = "TimeTrackingService exists";
    
    $content = file_get_contents($servicePath);
    $methods = ['trackLessonTime', 'trackGoalTime', 'syncEnrollmentTime', 'forceSyncEnrollment'];
    foreach ($methods as $method) {
        if (strpos($content, "function $method") !== false) {
            echo "   ✅ Method $method found\n";
        } else {
            echo "   ⚠️ Method $method NOT found\n";
            $warnings[] = "Method $method missing";
        }
    }
} else {
    echo "   ❌ TimeTrackingService.php NOT found\n";
    $errors[] = "Service class missing: $servicePath";
}

// 3. Check API Controller
echo "\n3. Checking API Controller...\n";
$controllerPath = __DIR__ . '/app/Http/Controllers/Api/TimeTrackingController.php';
if (file_exists($controllerPath)) {
    echo "   ✅ TimeTrackingController.php found\n";
    $success[] = "API Controller exists";
    
    $content = file_get_contents($controllerPath);
    $methods = ['trackLessonTime', 'trackGoalTime', 'getLessonTime', 'getGoalTime', 'getUserStats'];
    foreach ($methods as $method) {
        if (strpos($content, "function $method") !== false) {
            echo "   ✅ Method $method found\n";
        } else {
            echo "   ⚠️ Method $method NOT found\n";
            $warnings[] = "Controller method $method missing";
        }
    }
} else {
    echo "   ❌ TimeTrackingController.php NOT found\n";
    $errors[] = "API Controller missing: $controllerPath";
}

// 4. Check Routes
echo "\n4. Checking API routes...\n";
$routesPath = __DIR__ . '/routes/api.php';
if (file_exists($routesPath)) {
    echo "   ✅ api.php found\n";
    $content = file_get_contents($routesPath);
    
    $routes = [
        'TimeTrackingController',
        'track-time',
        'learning-goals/{goal}/track-time'
    ];
    
    foreach ($routes as $route) {
        if (strpos($content, $route) !== false) {
            echo "   ✅ Route '$route' registered\n";
        } else {
            echo "   ⚠️ Route '$route' NOT found\n";
            $warnings[] = "Route $route not registered";
        }
    }
} else {
    echo "   ❌ api.php NOT found\n";
    $errors[] = "Routes file missing";
}

// 5. Check Views
echo "\n5. Checking Blade views...\n";
$views = [
    'resources/views/lessons/show.blade.php' => 'StudyTimeTracker',
    'resources/views/learning-goals/show.blade.php' => 'StudyTimeTracker',
    'resources/views/layouts/app.blade.php' => 'study-time-tracker.js'
];

foreach ($views as $viewPath => $needle) {
    $fullPath = __DIR__ . '/' . $viewPath;
    if (file_exists($fullPath)) {
        $content = file_get_contents($fullPath);
        if (strpos($content, $needle) !== false) {
            echo "   ✅ $viewPath contains '$needle'\n";
            $success[] = basename($viewPath) . " integrated";
        } else {
            echo "   ⚠️ $viewPath MISSING '$needle'\n";
            $warnings[] = basename($viewPath) . " not integrated";
        }
    } else {
        echo "   ❌ $viewPath NOT found\n";
        $errors[] = basename($viewPath) . " missing";
    }
}

// 6. Check Migration
echo "\n6. Checking migration...\n";
$migrationPattern = __DIR__ . '/database/migrations/*_add_indexes_and_fix_time_tracking.php';
$migrations = glob($migrationPattern);
if (count($migrations) > 0) {
    echo "   ✅ Time tracking migration found\n";
    $success[] = "Migration exists";
    
    // Check if migration has been run
    if (file_exists(__DIR__ . '/vendor/autoload.php')) {
        require __DIR__ . '/vendor/autoload.php';
        $app = require_once __DIR__ . '/bootstrap/app.php';
        $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
        $kernel->bootstrap();
        
        try {
            $ran = DB::table('migrations')
                ->where('migration', 'like', '%add_indexes_and_fix_time_tracking%')
                ->exists();
            
            if ($ran) {
                echo "   ✅ Migration has been executed\n";
                $success[] = "Migration executed";
            } else {
                echo "   ⚠️ Migration NOT executed yet\n";
                $warnings[] = "Need to run: php artisan migrate";
            }
        } catch (\Exception $e) {
            echo "   ⚠️ Could not check migration status: " . $e->getMessage() . "\n";
            $warnings[] = "Migration status unknown";
        }
    }
} else {
    echo "   ❌ Time tracking migration NOT found\n";
    $errors[] = "Migration missing";
}

// 7. Check Test Page
echo "\n7. Checking test page...\n";
$testPath = __DIR__ . '/public/test-tracker.html';
if (file_exists($testPath)) {
    echo "   ✅ test-tracker.html found\n";
    echo "   ℹ️ Access at: http://127.0.0.1:8000/test-tracker.html\n";
    $success[] = "Test page available";
} else {
    echo "   ⚠️ test-tracker.html NOT found\n";
    $warnings[] = "Test page missing (optional)";
}

// Summary
echo "\n====================================\n";
echo "📊 VERIFICATION SUMMARY\n";
echo "====================================\n\n";

echo "✅ Success: " . count($success) . " checks passed\n";
foreach ($success as $item) {
    echo "   • $item\n";
}

if (count($warnings) > 0) {
    echo "\n⚠️ Warnings: " . count($warnings) . " issues found\n";
    foreach ($warnings as $item) {
        echo "   • $item\n";
    }
}

if (count($errors) > 0) {
    echo "\n❌ Errors: " . count($errors) . " critical issues\n";
    foreach ($errors as $item) {
        echo "   • $item\n";
    }
    echo "\n❌ Time tracker NOT ready!\n";
    exit(1);
} else {
    echo "\n🎉 All critical components present!\n";
    
    if (count($warnings) === 0) {
        echo "✅ Time tracker is 100% READY!\n";
    } else {
        echo "⚠️ Time tracker is ready but has minor warnings\n";
    }
    
    echo "\n📖 Next steps:\n";
    echo "   1. Run: php artisan serve\n";
    echo "   2. Open: http://127.0.0.1:8000/test-tracker.html\n";
    echo "   3. Login as student and open a lesson page\n";
    echo "   4. Watch timer start automatically\n";
    
    exit(0);
}
