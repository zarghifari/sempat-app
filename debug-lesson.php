<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$lesson = \App\Models\Lesson::find(143);

if ($lesson) {
    echo "Lesson ID: {$lesson->id}\n";
    echo "Title: {$lesson->title}\n\n";
    
    // Extract image src
    preg_match_all('/src=["\']([^"\']*)["\']/', $lesson->content, $matches);
    
    echo "Images found in content:\n";
    foreach ($matches[1] as $src) {
        if (strpos($src, 'image') !== false || strpos($src, 'jpg') !== false || strpos($src, 'png') !== false || strpos($src, 'gif') !== false) {
            echo "  - $src\n";
        }
    }
} else {
    echo "Lesson not found\n";
}
