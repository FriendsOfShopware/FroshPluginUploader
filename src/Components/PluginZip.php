<?php declare(strict_types=1);

namespace FroshPluginUploader\Components;

class PluginZip
{
    private $defaultBlacklist = [
        '.travis.yml',
        'build.sh',
        '.editorconfig',
        '.php_cs.dist',
        'ISSUE_TEMPLATE.md',
        '.sw-zip-blacklist',
        'tests',
        'Resources/store',
        'src/Resources/store',
    ];

    public function zip(string $directory, ?string $branch = null): string
    {
        $currentCwd = getcwd();
        $branch = $this->getCheckoutBranch($directory, $branch);
        $pluginName = basename($directory);

        // Cleanup old releases
        $this->exec(sprintf('rm -rf %s %s', escapeshellarg($pluginName), escapeshellarg($pluginName . '-*.zip')));

        // Create a new folder
        $this->exec('mkdir ' . escapeshellarg($pluginName));

        // Extract the git repository there
        $this->exec(sprintf('git archive %s | tar -x -C %s', escapeshellarg($branch), escapeshellarg($pluginName)));

        $composerJson = $directory . '/' . $pluginName . '/composer.json';
        $composerJsonBackup = $composerJson . '.bak';

        if (file_exists($composerJson)) {
            copy($composerJson, $composerJsonBackup);
            $this->filterShopwareDependencies($composerJson);
            // Install composer dependencies
            if ($this->needComposerToRun($composerJson)) {
                $this->exec('composer install --no-dev -n -o -d ' . escapeshellarg($pluginName));
            }

            rename($composerJsonBackup, $composerJson);
        }

        if (file_exists($directory . '/.sw-zip-blacklist')) {
            $blackList = file_get_contents($directory . '/.sw-zip-blacklist');
            $blackList = array_filter(explode("\n", $blackList));
            $this->defaultBlacklist = array_merge($this->defaultBlacklist, $blackList);
        }

        // Cleanup directory using blacklist
        foreach ($this->defaultBlacklist as $item) {
            $this->exec('rm -rf ' . escapeshellarg($pluginName . '/' . $item));
        }

        // Clean branch name for filename
        $branchClean = preg_replace('/[^a-z0-9]+/', '-', strtolower($branch));

        $fileName = $pluginName . '-' . $branchClean . '.zip';
        $filePath = $directory . '/' . $pluginName;

        $this->exec(sprintf('zip -r %s %s -x *.git*', escapeshellarg($fileName), escapeshellarg($pluginName)));

        $this->exec('rm -rf ' . escapeshellarg($currentCwd . '/' . $pluginName));

        if ($currentCwd !== getcwd()) {
            $this->exec(sprintf('mv %s %s', escapeshellarg($fileName), escapeshellarg($currentCwd)));
        }

        chdir($currentCwd);

        return $filePath . '/' . $fileName;
    }

    private function getCheckoutBranch(string $directory, ?string $branch)
    {
        chdir($directory);

        if ($branch) {
            return $branch;
        }

        exec('git tag --sort=-creatordate | head -1', $output, $ret);

        if ($ret !== 0) {
            throw new \RuntimeException('Command "git tag --sort=-creatordate" failed with code %d', $ret);
        }

        return $output[0] ?? 'master';
    }

    private function exec(string $command): void
    {
        exec($command, $output, $ret);

        if ($ret !== 0) {
            throw new \RuntimeException(sprintf('Command "%s" failed with code %d', $command, $ret));
        }
    }

    private function needComposerToRun(string $composerJsonPath): bool
    {
        $json = json_decode(file_get_contents($composerJsonPath), true);

        // Plugin does not require anything
        if (empty($json['require'])) {
            return false;
        }

        return true;
    }

    /**
     * Remove Shopware base packages from composer.json
     * so they aren't bundled with the plugin.
     *
     * @param string $composerJsonPath
     */
    private function filterShopwareDependencies(string $composerJsonPath): void
    {
        $json = json_decode(file_get_contents($composerJsonPath), true);

        $keys = ['shopware/platform', 'shopware/core', 'shopware/storefront', 'shopware/administration'];
        foreach ($keys as $key) {
            if (isset($json['require'][$key])) {
                unset($json['require'][$key]);
            }
        }

        file_put_contents(
            $composerJsonPath,
            json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );
    }
}
