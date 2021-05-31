<?php declare(strict_types=1);

use Danger\Config;
use Danger\Rule\CheckPhpCsFixerRule;
use Danger\Rule\CommitRegexRule;
use Danger\Rule\MaxCommitRule;

return (new Config())
    ->useRule(new CommitRegexRule('/^(feat|fix|docs|perf|refactor|compat|chore)(\(.+\))?\:\s(.{3,})/m'))
    ->useRule(new MaxCommitRule(1))
;
