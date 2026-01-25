#!/usr/bin/env php
<?php
/**
 * Teacher Dashboard Time Tracking Integration Verification
 * Checks if comprehensive study time tracking is integrated in teacher views
 */

echo "\n🔍 TEACHER TIME TRACKING INTEGRATION VERIFICATION\n";
echo "===================================================\n\n";

$checks = [];
$errors = [];
$warnings = [];

// 1. Check TeacherDashboardController Updates
echo "1️⃣  Checking TeacherDashboardController...\n";
$controllerFile = 'app/Http/Controllers/TeacherDashboardController.php';
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    
    // Check ArticleReading import
    if (strpos($content, 'use App\Models\ArticleReading') !== false) {
        echo "   ✅ ArticleReading model imported\n";
        $checks[] = 'ArticleReading Import';
    } else {
        echo "   ❌ ArticleReading model not imported\n";
        $errors[] = 'Missing ArticleReading import';
    }
    
    // Check students() method enhancements
    if (strpos($content, 'lessons_study_minutes') !== false) {
        echo "   ✅ students() method has lesson time breakdown\n";
        $checks[] = 'Students Method - Lessons';
    }
    
    if (strpos($content, 'articles_study_minutes') !== false) {
        echo "   ✅ students() method has article time tracking\n";
        $checks[] = 'Students Method - Articles';
    }
    
    if (strpos($content, 'goals_study_minutes') !== false) {
        echo "   ✅ students() method has learning goals time\n";
        $checks[] = 'Students Method - Goals';
    }
    
    // Check studentDetail() method enhancements
    if (strpos($content, 'studyTimeBreakdown') !== false) {
        echo "   ✅ studentDetail() method has comprehensive breakdown\n";
        $checks[] = 'StudentDetail Method Enhanced';
    }
    
    // Check article_readings query
    if (strpos($content, 'article_readings') !== false) {
        echo "   ✅ Article reading time query added\n";
        $checks[] = 'Article Reading Query';
    }
    
    // Check learning_goals total_study_seconds
    if (strpos($content, 'total_study_seconds') !== false) {
        echo "   ✅ Learning goals study time query added\n";
        $checks[] = 'Learning Goals Time Query';
    }
} else {
    echo "   ❌ TeacherDashboardController.php not found\n";
    $errors[] = 'Controller file missing';
}

// 2. Check Teacher Students Index View
echo "\n2️⃣  Checking Teacher Students Index View...\n";
$indexView = 'resources/views/teacher/students/index.blade.php';
if (file_exists($indexView)) {
    $content = file_get_contents($indexView);
    
    if (strpos($content, 'Total Study Time') !== false) {
        echo "   ✅ Study time label updated\n";
        $checks[] = 'Index View Label';
    }
    
    // Check for breakdown display
    if (strpos($content, 'lessons_study_minutes') !== false &&
        strpos($content, 'articles_study_minutes') !== false &&
        strpos($content, 'goals_study_minutes') !== false) {
        echo "   ✅ Time breakdown display added (lessons/articles/goals)\n";
        $checks[] = 'Index View Breakdown';
    } else {
        echo "   ⚠️  Time breakdown not displayed in index\n";
        $warnings[] = 'Consider adding breakdown tooltip in index view';
    }
} else {
    echo "   ❌ students/index.blade.php not found\n";
    $errors[] = 'Index view missing';
}

// 3. Check Teacher Student Detail View
echo "\n3️⃣  Checking Teacher Student Detail View...\n";
$showView = 'resources/views/teacher/students/show.blade.php';
if (file_exists($showView)) {
    $content = file_get_contents($showView);
    
    if (strpos($content, 'studyTimeBreakdown') !== false) {
        echo "   ✅ Study time breakdown section exists\n";
        $checks[] = 'Detail View Breakdown Section';
    } else {
        echo "   ❌ Study time breakdown not found\n";
        $errors[] = 'Add breakdown section to detail view';
    }
    
    if (strpos($content, 'View Details') !== false || strpos($content, 'toggleStudyTimeBreakdown') !== false) {
        echo "   ✅ Toggle function for breakdown added\n";
        $checks[] = 'Breakdown Toggle Function';
    }
    
    // Check for visual breakdown
    if (strpos($content, 'Lessons') !== false &&
        strpos($content, 'Articles') !== false &&
        strpos($content, 'Learning Goals') !== false) {
        echo "   ✅ All time sources displayed in breakdown\n";
        $checks[] = 'Complete Breakdown Display';
    }
    
    // Check for progress bar
    if (strpos($content, 'Time Distribution') !== false) {
        echo "   ✅ Visual time distribution bar added\n";
        $checks[] = 'Visual Distribution';
    }
    
    // Check for JavaScript
    if (strpos($content, 'toggleStudyTimeBreakdown()') !== false) {
        echo "   ✅ JavaScript toggle function exists\n";
        $checks[] = 'JavaScript Toggle';
    }
} else {
    echo "   ❌ students/show.blade.php not found\n";
    $errors[] = 'Detail view missing';
}

// 4. Check Database Tables
echo "\n4️⃣  Checking Database Tables...\n";
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=sempat_app', 'root', '');
    
    // Check article_readings table
    $stmt = $pdo->query("SHOW TABLES LIKE 'article_readings'");
    if ($stmt->rowCount() > 0) {
        echo "   ✅ article_readings table exists\n";
        $checks[] = 'Article Readings Table';
        
        // Check if table has data
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM article_readings");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "      ℹ️  $count article reading records found\n";
    } else {
        echo "   ⚠️  article_readings table not found\n";
        $warnings[] = 'Run migration for article_readings';
    }
    
    // Check learning_goals.total_study_seconds field
    $stmt = $pdo->query("SHOW COLUMNS FROM learning_goals LIKE 'total_study_seconds'");
    if ($stmt->rowCount() > 0) {
        echo "   ✅ learning_goals.total_study_seconds field exists\n";
        $checks[] = 'Learning Goals Time Field';
    } else {
        echo "   ⚠️  total_study_seconds field missing in learning_goals\n";
        $warnings[] = 'Run learning goals time tracking migration';
    }
    
    // Check enrollments.total_study_minutes
    $stmt = $pdo->query("SHOW COLUMNS FROM enrollments LIKE 'total_study_minutes'");
    if ($stmt->rowCount() > 0) {
        echo "   ✅ enrollments.total_study_minutes field exists\n";
        $checks[] = 'Enrollments Time Field';
    }
} catch (Exception $e) {
    echo "   ⚠️  Cannot verify database: " . $e->getMessage() . "\n";
    $warnings[] = 'Check database connection';
}

// 5. Integration Summary
echo "\n5️⃣  Integration Summary...\n";
$requiredChecks = [
    'ArticleReading Import',
    'Students Method - Lessons',
    'Students Method - Articles', 
    'Students Method - Goals',
    'StudentDetail Method Enhanced',
    'Detail View Breakdown Section',
];

$completedRequired = array_intersect($requiredChecks, $checks);
$percentage = count($completedRequired) > 0 ? round((count($completedRequired) / count($requiredChecks)) * 100) : 0;

echo "   Integration Progress: $percentage% (" . count($completedRequired) . "/" . count($requiredChecks) . " required features)\n";

if ($percentage === 100) {
    echo "   ✅ Full integration complete!\n";
} else {
    echo "   ⚠️  Partial integration - see missing features below\n";
    foreach (array_diff($requiredChecks, $completedRequired) as $missing) {
        echo "      - Missing: $missing\n";
    }
}

// Final Summary
echo "\n" . str_repeat("=", 50) . "\n";
echo "📊 FINAL SUMMARY\n";
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
    echo "\n❌ INTEGRATION INCOMPLETE\n\n";
    exit(1);
} else {
    echo "\n✅ TIME TRACKING FULLY INTEGRATED IN TEACHER DASHBOARD!\n";
    echo "\n📝 How Teachers Can View Study Time:\n";
    echo "   1. Navigate to: /teacher/students\n";
    echo "   2. Each student card shows:\n";
    echo "      - Total study time (HH:MM format)\n";
    echo "      - Breakdown: 📚 Lessons, 📰 Articles, 🎯 Goals\n";
    echo "   3. Click 'View Details' on any student\n";
    echo "   4. In detail page, click 'View Details' on Total Study Time card\n";
    echo "   5. See comprehensive breakdown with:\n";
    echo "      - Individual time per source\n";
    echo "      - Visual distribution bar\n";
    echo "      - Percentage breakdown\n\n";
    echo "📊 Data Sources:\n";
    echo "   - Lessons: enrollments.total_study_minutes\n";
    echo "   - Articles: article_readings.time_spent_seconds\n";
    echo "   - Learning Goals: learning_goals.total_study_seconds\n\n";
    exit(0);
}
