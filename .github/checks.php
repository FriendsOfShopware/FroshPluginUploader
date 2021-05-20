<?php

// Verify commit
$commitRegex = '/^(feat|fix|docs|perf|refactor|compat|chore)(\(.+\))?\:\s(.{3,})/m';

foreach ($commits as $item) {
    if (!preg_match($commitRegex, $item['commit']['message'])) {
        $fails[] = sprintf('Your commit message: "%s" does not match the Conventional commits. See https://www.conventionalcommits.org/', $item['commit']['message']);
    }
}

if (count($commits) > 1) {
    $notices[] = 'You have more than one commit. Please consider squashing your commits.';
}

// Run php-cs-fixer
exec('php vendor/bin/php-cs-fixer fix --format=json', $cmdOutput, $resultCode);

if (!isset($cmdOutput[0])) {
    $fails[] = 'PHP-CS-Fixer did not run';
}

if (count(json_decode($cmdOutput[0], true)['files'])) {
    $fails[] = 'Found some Code-Style issues. Please run <code>./vendor/bin/php-cs-fixer fix</code> on your branch';
}