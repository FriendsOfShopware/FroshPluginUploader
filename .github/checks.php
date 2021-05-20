<?php

// Verify commit
$commitRegex = '/^(feat|fix|docs|perf|refactor|compat|chore)(\(.+\))?\:\s(.{3,})/m';

foreach ($commits as $item) {
    if (!preg_match($commitRegex, $item['commit']['message'])) {
        $fails[] = sprintf('Your commit message: "%s" does not match the Conventional commits. See https://www.conventionalcommits.org/', $item['commit']['message']);
    }
}
