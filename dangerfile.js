
danger.github.commits.forEach((commit) => {
    const commitRegex = /^(feat|fix|docs|perf|refactor|compat|chore)(\(.+\))?\:\s(.{3,})/gm;

    commit = commit.commit;

    if (commit.committer.name === 'GitHub') {
        return;
    }

    if (!commitRegex.exec(commit.message)) {
        fail(`Your commit message: "${commit.message}" does not match the Conventional commits. See https://www.conventionalcommits.org/`);
    }
});