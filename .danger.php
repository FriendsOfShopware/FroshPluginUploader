<?php declare(strict_types=1);

use Danger\Config;
use Danger\Rule\CheckPhpCsFixer;
use Danger\Rule\CommitRegex;
use Danger\Rule\MaxCommit;

return (new Config())
    ->useRule(new CommitRegex('/^(feat|fix|docs|perf|refactor|compat|chore)(\(.+\))?\:\s(.{3,})/m'))
    ->useRule(new MaxCommit(1))
;
