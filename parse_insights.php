<?php

// Run PHP Insights and capture JSON output
$command = './vendor/bin/phpinsights --format=json --no-interaction 2>/dev/null';
$output = shell_exec($command);

if ($output === null) {
    echo "Failed to run PHP Insights\n";
    exit(1);
}

$data = json_decode($output, true);

if ($data === null) {
    echo "Failed to parse JSON output\n";
    exit(1);
}

$unusedParams = [];

if (isset($data['report']['issues'])) {
    foreach ($data['report']['issues'] as $issue) {
        if (isset($issue['category']) && $issue['category'] === 'UnusedParameter') {
            $unusedParams[] = $issue;
        }
    }
}

echo "Found " . count($unusedParams) . " unused parameter issues:\n\n";

foreach ($unusedParams as $issue) {
    echo "File: " . $issue['file'] . "\n";
    echo "Line: " . $issue['line'] . "\n";
    echo "Message: " . $issue['message'] . "\n";
    echo "---\n";
}