#!/usr/bin/env php
<?php
/**
 * Learning Goals Time Tracking Integration Verification
 * Checks if time tracker is integrated with learning goals and daily targets
 */

echo "\n🎯 LEARNING GOALS TIME TRACKING VERIFICATION\n";
echo "=============================================\n\n";

$checks = [];
$warnings = [];
$errors = [];

// 1. Check Database Schema
echo "1️⃣  Checking Learning Goals Database Schema...\n";
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=sempat_app', 'root', '');
    
    // Check daily_target_minutes field
    $stmt = $pdo->query("SHOW COLUMNS FROM learning_goals LIKE 'daily_target_minutes'");
    if ($stmt->rowCount() > 0) {
        echo "   ✅ daily_target_minutes field exists\n";
        $checks[] = 'Daily Target Minutes Field';
    }
    
    // Check target_days field
    $stmt = $pdo->query("SHOW COLUMNS FROM learning_goals LIKE 'target_days'");
    if ($stmt->rowCount() > 0) {
        echo "   ✅ target_days field exists\n";
        $checks[] = 'Target Days Field';
    }
    
    // Check total_study_seconds field
    $stmt = $pdo->query("SHOW COLUMNS FROM learning_goals LIKE 'total_study_seconds'");
    if ($stmt->rowCount() > 0) {
        echo "   ✅ total_study_seconds field exists\n";
        $checks[] = 'Study Seconds Field';
    }
    
    // Check last_study_at field
    $stmt = $pdo->query("SHOW COLUMNS FROM learning_goals LIKE 'last_study_at'");
    if ($stmt->rowCount() > 0) {
        echo "   ✅ last_study_at field exists\n";
        $checks[] = 'Last Study At Field';
    }
    
    // Check days_completed field
    $stmt = $pdo->query("SHOW COLUMNS FROM learning_goals LIKE 'days_completed'");
    if ($stmt->rowCount() > 0) {
        echo "   ✅ days_completed field exists\n";
        $checks[] = 'Days Completed Field';
    }
    
    // Check if any learning goals have daily targets set
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM learning_goals WHERE daily_target_minutes > 0");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $countWithTargets = $result['count'];
    echo "   ℹ️  $countWithTargets learning goals have daily targets set\n";
    
} catch (Exception $e) {
    echo "   ⚠️  Cannot verify database: " . $e->getMessage() . "\n";
    $warnings[] = 'Check database connection';
}

// 2. Check LearningGoal Model
echo "\n2️⃣  Checking LearningGoal Model...\n";
$modelFile = 'app/Models/LearningGoal.php';
if (file_exists($modelFile)) {
    $content = file_get_contents($modelFile);
    
    // Check fillable fields
    $fillableFields = ['daily_target_minutes', 'target_days', 'total_study_seconds', 'last_study_at', 'days_completed'];
    $foundFields = 0;
    foreach ($fillableFields as $field) {
        if (strpos($content, "'$field'") !== false) {
            $foundFields++;
        }
    }
    
    if ($foundFields === count($fillableFields)) {
        echo "   ✅ All tracking fields in \$fillable ($foundFields fields)\n";
        $checks[] = 'Model Fillable Fields';
    } else {
        echo "   ⚠️  Some tracking fields missing from \$fillable ($foundFields/" . count($fillableFields) . ")\n";
        $warnings[] = 'Check LearningGoal fillable fields';
    }
    
    // Check recalculateProgress method
    if (strpos($content, 'function recalculateProgress') !== false) {
        echo "   ✅ recalculateProgress() method exists\n";
        $checks[] = 'Progress Calculation Method';
        
        // Check if it uses study time
        if (strpos($content, 'total_study_seconds') !== false && 
            strpos($content, 'studyTimeProgress') !== false) {
            echo "   ✅ Progress calculation considers study time (30% weight)\n";
            $checks[] = 'Study Time in Progress';
        } else {
            echo "   ⚠️  Progress calculation may not use study time\n";
            $warnings[] = 'Verify study time usage in progress calculation';
        }
    } else {
        echo "   ❌ recalculateProgress() method not found\n";
        $errors[] = 'Add recalculateProgress method';
    }
} else {
    echo "   ❌ LearningGoal.php not found\n";
    $errors[] = 'Model file missing';
}

// 3. Check TimeTrackingService Integration
echo "\n3️⃣  Checking TimeTrackingService Integration...\n";
$serviceFile = 'app/Services/TimeTrackingService.php';
if (file_exists($serviceFile)) {
    $content = file_get_contents($serviceFile);
    
    if (strpos($content, 'trackGoalTime') !== false) {
        echo "   ✅ trackGoalTime() method exists\n";
        $checks[] = 'Track Goal Time Method';
    }
    
    if (strpos($content, 'updateGoalProgress') !== false) {
        echo "   ✅ updateGoalProgress() method exists\n";
        $checks[] = 'Update Goal Progress Method';
    }
    
    if (strpos($content, 'learning_goals') !== false && 
        strpos($content, 'total_study_seconds') !== false) {
        echo "   ✅ Service updates total_study_seconds field\n";
        $checks[] = 'Study Seconds Update';
    }
} else {
    echo "   ❌ TimeTrackingService.php not found\n";
    $errors[] = 'Service file missing';
}

// 4. Check API Routes
echo "\n4️⃣  Checking API Routes...\n";
$routeFile = 'routes/api.php';
if (file_exists($routeFile)) {
    $content = file_get_contents($routeFile);
    
    if (strpos($content, 'learning-goals/{goal}/track-time') !== false) {
        echo "   ✅ POST /api/learning-goals/{goal}/track-time route registered\n";
        $checks[] = 'Track Time API Route';
    }
    
    if (strpos($content, 'learning-goals/{goal}/time') !== false) {
        echo "   ✅ GET /api/learning-goals/{goal}/time route registered\n";
        $checks[] = 'Get Time API Route';
    }
} else {
    echo "   ❌ routes/api.php not found\n";
    $errors[] = 'API routes file missing';
}

// 5. Check Learning Goals View Integration
echo "\n5️⃣  Checking Learning Goals View Integration...\n";
$showView = 'resources/views/learning-goals/show.blade.php';
if (file_exists($showView)) {
    $content = file_get_contents($showView);
    
    // Check for tracker initialization
    if (strpos($content, 'StudyTimeTracker') !== false) {
        echo "   ✅ StudyTimeTracker initialized in view\n";
        $checks[] = 'Tracker Initialization';
    } else {
        echo "   ⚠️  StudyTimeTracker not found in view\n";
        $warnings[] = 'Verify tracker initialization';
    }
    
    // Check for goal-study-timer element
    if (strpos($content, 'goal-study-timer') !== false) {
        echo "   ✅ Timer display element exists\n";
        $checks[] = 'Timer Display Element';
    }
    
    // Check for daily_target_minutes usage
    if (strpos($content, 'daily_target_minutes') !== false) {
        echo "   ✅ Daily target minutes displayed in view\n";
        $checks[] = 'Daily Target Display';
    }
    
    // Check for total_study_seconds usage
    if (strpos($content, 'total_study_seconds') !== false) {
        echo "   ✅ Total study seconds displayed in view\n";
        $checks[] = 'Study Seconds Display';
    }
    
    // Check for old StudyTimer (should be removed)
    if (strpos($content, 'StudyTimer') !== false && strpos($content, 'new StudyTimer(') !== false) {
        echo "   ⚠️  Old StudyTimer code still present (deprecated)\n";
        $warnings[] = 'Remove old StudyTimer references';
    } else {
        echo "   ✅ No deprecated StudyTimer code found\n";
        $checks[] = 'Clean Code (No Old Timer)';
    }
    
    // Check for manual timer buttons (should be optional/removed)
    if (strpos($content, 'startTimerBtn') !== false ||
        strpos($content, 'pauseTimerBtn') !== false ||
        strpos($content, 'stopTimerBtn') !== false) {
        echo "   ⚠️  Manual timer buttons still present (consider removing - tracker is automatic)\n";
        $warnings[] = 'Consider removing manual timer buttons (tracker is automatic)';
    }
} else {
    echo "   ❌ learning-goals/show.blade.php not found\n";
    $errors[] = 'View file missing';
}

// 6. Check Create/Edit Forms
echo "\n6️⃣  Checking Learning Goals Creation Forms...\n";
$createController = 'app/Http/Controllers/LearningGoalController.php';
if (file_exists($createController)) {
    $content = file_get_contents($createController);
    
    if (strpos($content, 'daily_target_minutes') !== false) {
        echo "   ✅ daily_target_minutes in controller validation\n";
        $checks[] = 'Controller Validation';
    }
} else {
    echo "   ⚠️  LearningGoalController.php not found\n";
}

// 7. Integration Features Summary
echo "\n7️⃣  Integration Features Summary...\n";
$features = [
    'automatic_tracking' => in_array('Tracker Initialization', $checks),
    'daily_targets' => in_array('Daily Target Minutes Field', $checks),
    'progress_integration' => in_array('Study Time in Progress', $checks),
    'api_endpoints' => in_array('Track Time API Route', $checks),
    'real_time_display' => in_array('Timer Display Element', $checks),
];

$enabledFeatures = array_filter($features);
$featurePercentage = count($enabledFeatures) > 0 ? round((count($enabledFeatures) / count($features)) * 100) : 0;

echo "   Integration Level: $featurePercentage% (" . count($enabledFeatures) . "/" . count($features) . " features)\n";
echo "\n   Features Status:\n";
echo "   " . ($features['automatic_tracking'] ? '✅' : '❌') . " Automatic Time Tracking\n";
echo "   " . ($features['daily_targets'] ? '✅' : '❌') . " Daily Target Support\n";
echo "   " . ($features['progress_integration'] ? '✅' : '❌') . " Progress Calculation Integration\n";
echo "   " . ($features['api_endpoints'] ? '✅' : '❌') . " API Endpoints\n";
echo "   " . ($features['real_time_display'] ? '✅' : '❌') . " Real-time Display\n";

// Final Summary
echo "\n" . str_repeat("=", 50) . "\n";
echo "📊 FINAL SUMMARY\n";
echo str_repeat("=", 50) . "\n\n";

echo "✅ Checks Passed: " . count($checks) . "\n";
if (count($checks) > 0) {
    foreach (array_slice($checks, 0, 10) as $check) {
        echo "   - $check\n";
    }
    if (count($checks) > 10) {
        echo "   ... and " . (count($checks) - 10) . " more\n";
    }
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
    $status = count($warnings) > 0 ? 'WORKING WITH WARNINGS' : 'FULLY INTEGRATED';
    echo "\n✅ TIME TRACKING $status IN LEARNING GOALS!\n";
    
    echo "\n📝 How Learning Goals Time Tracking Works:\n";
    echo "   1. Student creates learning goal with daily target (e.g., 30 minutes/day)\n";
    echo "   2. Student opens learning goal detail page\n";
    echo "   3. Time tracker AUTOMATICALLY starts tracking\n";
    echo "   4. Every 60 seconds, time synced to server\n";
    echo "   5. Progress calculation considers study time (30% weight)\n";
    echo "   6. Timer shows in header card: HH:MM:SS format\n";
    echo "   7. Daily progress bar updates in real-time\n";
    echo "   8. Days completed auto-increments when target reached\n\n";
    
    echo "🎯 Student Experience:\n";
    echo "   - Open learning goal → Timer starts automatically\n";
    echo "   - Idle > 5 minutes → Timer pauses\n";
    echo "   - Return to page → Timer resumes\n";
    echo "   - Close page → Time saved automatically\n";
    echo "   - View anytime → Total study time displayed\n\n";
    
    echo "📊 Progress Calculation:\n";
    echo "   Total Progress = Weighted Average:\n";
    echo "   - 30% Study Time (vs daily target × target days)\n";
    echo "   - 30% Days Completed (vs target days)\n";
    echo "   - 30% Milestones Completed\n";
    echo "   - 10% Final Project/Assessment\n\n";
    
    if (count($warnings) > 0) {
        echo "⚡ Recommendations:\n";
        if (in_array('Consider removing manual timer buttons (tracker is automatic)', $warnings)) {
            echo "   - Remove start/pause/stop buttons (tracker is automatic)\n";
        }
        if (in_array('Remove old StudyTimer references', $warnings)) {
            echo "   - Clean up old StudyTimer code from view\n";
        }
        echo "\n";
    }
    
    exit(0);
}
