<?php

$logPath = __DIR__ . '/storage/logs/laravel.log';
$content = file_get_contents($logPath);

$prompt = '';
$response = '';

// Use regex with 's' modifier to match across newlines
if (preg_match_all('/local\.DEBUG: OpenAI Meal Plan Raw Response: (\{.*?\}) \n\[/s', $content . "\n[", $matches)) {
    // Get the last match
    $lastMatch = end($matches[1]);
    $parsed = json_decode($lastMatch, true);
    $response = $parsed['text'] ?? $lastMatch;
}

if (preg_match_all('/local\.DEBUG: OpenAI Meal Plan Prompt: (\{.*?\}) \n\[/s', $content . "\n[", $matches)) {
    // Get the last match
    $lastMatch = end($matches[1]);
    $parsed = json_decode($lastMatch, true);
    $prompt = $parsed['prompt'] ?? $lastMatch;
}

// Write to an artifact
$artifactPath = 'C:/Users/salman/.gemini/antigravity-ide/brain/7cb2aca8-4bff-4244-beb5-ae719fc76bc3/openai_logs.md';

$md = "# OpenAI Communication Logs\n\n";
$md .= "## Last Prompt Sent to OpenAI\n";
$md .= "```text\n" . $prompt . "\n```\n\n";
$md .= "## Raw Response Received from OpenAI\n";
$md .= "```json\n" . $response . "\n```\n";

file_put_contents($artifactPath, $md);

echo "Artifact created successfully.\n";

