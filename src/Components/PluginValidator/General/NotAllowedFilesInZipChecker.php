<?php declare(strict_types=1);

namespace FroshPluginUploader\Components\PluginValidator\General;

use FroshPluginUploader\Components\PluginValidator\ValidationInterface;
use FroshPluginUploader\Structs\ViolationContext;

class NotAllowedFilesInZipChecker implements ValidationInterface
{
    public const NOT_ALLOWED_FILES = ['.DS_Store', 'Thumbs.db', '.git', '__MACOSX'];

    public const NOT_ALLOWED_EXTENSIONS = ['.zip', '.tar', '.gz', '.phar', '.rar'];

    public function supports(ViolationContext $context): bool
    {
        return true;
    }

    public function validate(ViolationContext $context): void
    {
        $foundVcsDir = false;

        /**
         * Go through all files in the zip
         */
        for ($i = 0; $i < $context->getZipArchive()->numFiles; $i++) {
            $fileInfo = $context->getZipArchive()->statIndex($i);

            /**
             * Check for a directory traversal attack
             */
            if (strpos($fileInfo['name'], '../') !== false) {
                $context->addViolation('Directory traversal detected');
            }

            if (!$foundVcsDir && (strpos($fileInfo['name'], '/.git/') !== false || strpos($fileInfo['name'], '.git/') === 0)) {
                $context->addViolation('Found vcs repository inside zip.');
                $foundVcsDir = true;
            }

            foreach (self::NOT_ALLOWED_EXTENSIONS as $forbiddenExtension) {
                if (self::endsWith($fileInfo['name'], $forbiddenExtension)) {
                    $context->addViolation(sprintf('Not allowed file or folder %s detected. Please remove it', $fileInfo['name']));
                }
            }

            /**
             * iterate over all not allowed file extensions and folders
             */
            foreach (self::NOT_ALLOWED_FILES as $forbiddenFile) {

                /**
                 * user lowercase for comparison and escape metacharacters
                 */
                $checkPattern = preg_quote(strtolower($forbiddenFile));

                /**
                 * check for not allowed files
                 */
                if (preg_match('/^.*' . $checkPattern . '$/', strtolower($fileInfo['name']))) {
                    $context->addViolation(sprintf('Not allowed file or folder %s detected. Please remove it', $forbiddenFile));
                }
            }
        }
    }

    private static function endsWith($haystack, $needle): bool
    {
        return (substr($haystack, -strlen($needle)) === $needle);
    }
}
