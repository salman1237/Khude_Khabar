<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$child = App\Models\Child::first();
if (!$child) {
    echo "No child found.\n";
    exit;
}

$planner = new App\Services\OpenAIMealPlanner();
$plan = $planner->generate($child);

if (count($plan) === 21) {
    echo "SUCCESS: Generated 21 meals\n";
} else {
    echo "FAILED: Generated " . count($plan) . " meals\n";
}

echo "First meal name: " . ($plan[0]['meal_name_bengali'] ?? 'N/A') . "\n";
echo "First meal type: " . ($plan[0]['meal_type'] ?? 'N/A') . "\n";
echo "First meal ingredients count: " . count($plan[0]['ingredients'] ?? []) . "\n";
