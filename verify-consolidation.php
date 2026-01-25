#!/usr/bin/env php
<?php
/**
 * Time Tracking Consolidation Verification
 * Checks if new tracker is properly integrated for Articles and Learning Goals
 */

echo "\n🔍 TIME TRACKING CONSOLIDATION VERIFICATION\n";
echo "==========================================\n\n";

$checks = [];
$errors = [];
$warnings = [];

// 1. Check Article Reading Model
echo "1️⃣  Checking ArticleReading Model...\n";
$modelFile = 'app/Models/ArticleReading.php';
if (file_exists($modelFile)) {
    $content = file_get_contents($modelFile);
    if (strpos($content, 'class ArticleReading') !== false) {
        echo "   ✅ ArticleReading model exists\n";
        $checks[] = 'ArticleReading Model';
    } else {
        echo "   ❌ ArticleReading class not found\n";
        $errors[] = 'ArticleReading class missing';
    }
} else {
    echo "   ❌ ArticleReading.php not found\n";
    $errors[] = 'ArticleReading model file missing';
}

// 2. Check Migration
echo "\n2️⃣  Checking Article Tracking Migration...\n";
$migrationPattern = 'database/migrations/*_add_time_tracking_to_articles.php';
$migrations = glob($migrationPattern);
if (count($migrations) > 0) {
    echo "   ✅ Migration found: " . basename($migrations[0]) . "\n";
    $checks[] = 'Article Migration';
    
    // Check if executed
    try {
        $pdo = new PDO('mysql:host=127.0.0.1;dbname=sempat_app', 'root', '');
        $stmt = $pdo->query("SHOW TABLES LIKE 'article_readings'");
        if ($stmt->rowCount() > 0) {
            echo "   ✅ Migration executed (table exists)\n";
            $checks[] = 'Migration Executed';
        } else {
            echo "   ⚠️  Migration not executed yet\n";
            $warnings[] = 'Run: php artisan migrate';
        }
    } catch (Exception $e) {
        echo "   ⚠️  Cannot verify migration execution\n";
        $warnings[] = 'Check database connection';
    }
} else {
    echo "   ❌ Migration file not found\n";
    $errors[] = 'Article tracking migration missing';
}

// 3. Check TimeTrackingService
echo "\n3️⃣  Checking TimeTrackingService...\n";
$serviceFile = 'app/Services/TimeTrackingService.php';
if (file_exists($serviceFile)) {
    $content = file_get_contents($serviceFile);
    
    $methods = ['trackArticleTime'];
    $found = 0;
    foreach ($methods as $method) {
        if (strpos($content, "function $method") !== false) {
            $found++;
        }
    }
    
    if ($found === count($methods)) {
        echo "   ✅ TimeTrackingService has trackArticleTime method\n";
        $checks[] = 'TimeTrackingService Updated';
    } else {
        echo "   ❌ Missing methods in TimeTrackingService\n";
        $errors[] = "Found $found/" . count($methods) . " methods";
    }
} else {
    echo "   ❌ TimeTrackingService.php not found\n";
    $errors[] = 'Service file missing';
}

// 4. Check TimeTrackingController
echo "\n4️⃣  Checking TimeTrackingController...\n";
$controllerFile = 'app/Http/Controllers/Api/TimeTrackingController.php';
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    
    $methods = ['trackArticleTime', 'getArticleTime'];
    $found = 0;
    foreach ($methods as $method) {
        if (strpos($content, "function $method") !== false) {
            $found++;
        }
    }
    
    if ($found === count($methods)) {
        echo "   ✅ TimeTrackingController has article methods ($found methods)\n";
        $checks[] = 'Controller Updated';
    } else {
        echo "   ❌ Missing methods in Controller\n";
        $errors[] = "Found $found/" . count($methods) . " methods";
    }
} else {
    echo "   ❌ TimeTrackingController.php not found\n";
    $errors[] = 'Controller file missing';
}

// 5. Check API Routes
echo "\n5️⃣  Checking API Routes...\n";
$routeFile = 'routes/api.php';
if (file_exists($routeFile)) {
    $content = file_get_contents($routeFile);
    
    $hasArticleTrack = strpos($content, "articles/{article}/track-time") !== false;
    $hasArticleGet = strpos($content, "articles/{article}/time") !== false;
    
    if ($hasArticleTrack && $hasArticleGet) {
        echo "   ✅ Article tracking routes registered\n";
        $checks[] = 'API Routes';
    } else {
        echo "   ❌ Article routes missing\n";
        $errors[] = 'Add article routes to api.php';
    }
} else {
    echo "   ❌ routes/api.php not found\n";
    $errors[] = 'API routes file missing';
}

// 6. Check Articles View Integration
echo "\n6️⃣  Checking Articles View Integration...\n";
$articleView = 'resources/views/articles/show.blade.php';
if (file_exists($articleView)) {
    $content = file_get_contents($articleView);
    
    $hasTimer = strpos($content, 'article-timer') !== false;
    $hasTracker = strpos($content, 'StudyTimeTracker') !== false;
    $hasStyles = strpos($content, 'reading-timer') !== false;
    
    if ($hasTimer && $hasTracker && $hasStyles) {
        echo "   ✅ Article view has timer display and tracker\n";
        $checks[] = 'Article View Integration';
    } else {
        echo "   ⚠️  Article view integration incomplete:\n";
        if (!$hasTimer) echo "      - Missing timer display element\n";
        if (!$hasTracker) echo "      - Missing StudyTimeTracker initialization\n";
        if (!$hasStyles) echo "      - Missing timer styles\n";
        $warnings[] = 'Check articles/show.blade.php integration';
    }
} else {
    echo "   ❌ articles/show.blade.php not found\n";
    $errors[] = 'Article view file missing';
}

// 7. Check Learning Goals View Upgrade
echo "\n7️⃣  Checking Learning Goals View Upgrade...\n";
$goalView = 'resources/views/learning-goals/show.blade.php';
if (file_exists($goalView)) {
    $content = file_get_contents($goalView);
    
    $hasNewTracker = strpos($content, 'StudyTimeTracker') !== false;
    $hasOldTimer = strpos($content, 'StudyTimer') !== false; // Old class
    
    if ($hasNewTracker) {
        echo "   ✅ Learning Goals using new StudyTimeTracker\n";
        $checks[] = 'Learning Goals Upgraded';
        
        if ($hasOldTimer) {
            echo "   ⚠️  Old StudyTimer code still present (should be removed)\n";
            $warnings[] = 'Remove old StudyTimer references from learning-goals/show.blade.php';
        }
    } else {
        echo "   ❌ Learning Goals not using new tracker\n";
        $errors[] = 'Learning Goals view needs upgrade';
    }
} else {
    echo "   ❌ learning-goals/show.blade.php not found\n";
    $errors[] = 'Learning Goals view file missing';
}

// 8. Check if old study-timer.js should be removed
echo "\n8️⃣  Checking Old Timer File...\n";
$oldTimerFile = 'public/js/study-timer.js';
if (file_exists($oldTimerFile)) {
    echo "   ⚠️  Old study-timer.js still exists\n";
    echo "      This file is DEPRECATED and should be removed after verification\n";
    $warnings[] = 'Consider removing public/js/study-timer.js (deprecated)';
} else {
    echo "   ✅ Old study-timer.js already removed\n";
}

// Summary
echo "\n" . str_repeat("=", 50) . "\n";
echo "📊 SUMMARY\n";
echo str_repeat("=", 50) . "\n\n";

echo "✅ Checks Passed: " . count($checks) . "\n";
foreach ($checks as $check) {
    echo "   - $check\n";
}

if (count($warnings) > 0) {
    echo "\n⚠️  Warnings: " . count($warnings) . "\n";
    foreach ($warnings as $warning) {
        echo "   - $warning\n";
    }
}

if (count($errors) > 0) {
    echo "\n❌ Errors: " . count($errors) . "\n";
    foreach ($errors as $error) {
        echo "   - $error\n";
    }
    echo "\n❌ CONSOLIDATION INCOMPLETE - Fix errors above\n\n";
    exit(1);
} else {
    echo "\n✅ CONSOLIDATION COMPLETE!\n";
    echo "\n📝 Next Steps:\n";
    echo "   1. Test article page: Open any article and verify timer appears\n";
    echo "   2. Test learning goals: Verify automatic tracking (no manual buttons)\n";
    echo "   3. Check browser console: Should have NO errors\n";
    echo "   4. Monitor Network tab: Should see POST requests every 30-60s\n";
    echo "   5. After testing: Remove public/js/study-timer.js (optional)\n\n";
    exit(0);
}
