<?php
/**
 * 2025 DeveMain
 *
 * All rights reserved. For internal use only.
 * Unauthorized copying, modification, or distribution is prohibited.
 *
 * @author    DeveMain <devemain@gmail.com>
 * @copyright 2025 DeveMain
 * @license   PROPRIETARY
 * @link      https://github.com/DeveMain
 */

namespace Devemain;

/**
 * Handles the deployment and removal of configuration files.
 * It can recursively process directories and files, providing detailed
 * feedback on the operations performed.
 */
class ConfigDeployer
{
    /**
     * Directory containing source configuration files.
     */
    private string $sourceDir;

    /**
     * Processed files during deployment.
     */
    private array $processed;

    /**
     * Store any errors encountered.
     */
    private array $errors;

    /**
     * Removed files during removal operation.
     */
    private array $removed;

    /**
     * Creates a new instance.
     *
     * @param CliHelper $cli CLI helper instance for output operations
     */
    public function __construct(
        private readonly CliHelper $cli
    ) {
        $this->sourceDir = '_dm/config';
        $this->processed = [];
        $this->removed = [];
        $this->errors = [];
    }

    /**
     * Main execution method.
     *
     * Determines whether to run in deployment or removal mode based on
     * command line options and executes the appropriate operations.
     */
    public function run(): void
    {
        $removeMode = $this->cli->hasOption('remove');

        $this->cli->frame(($removeMode ? 'Removing' : 'Deploying') . ' configuration files');

        if ($removeMode) {
            $this->processRemoval($this->sourceDir);
        } else {
            $this->processDeployment($this->sourceDir);
        }

        $this->showSummary();

        $this->cli->success('Configuration files ' . ($removeMode ? 'removed' : 'deployed') . ' successfully!', true);
    }

    /**
     * Process deployment of configuration files.
     *
     * Recursively processes a directory and its contents, deploying files
     * to their target locations.
     *
     * @param string $dir Directory to process
     * @param string $basePath Base path for relative path calculation
     */
    private function processDeployment(string $dir, string $basePath = ''): void
    {
        if (!is_dir($dir)) {
            $this->errors[] = 'Source directory not found: ' . $dir;
            return;
        }

        $files = scandir($dir);

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $sourcePath = $dir . '/' . $file;
            $relativePath = ltrim($basePath . '/' . $file, '/');

            if (is_dir($sourcePath)) {
                $this->processDeployment($sourcePath, $relativePath);
            } else {
                $this->deployFile($sourcePath, $relativePath);
            }
        }
    }

    /**
     * Process removal of configuration files.
     *
     * Recursively processes a directory and its contents, removing files
     * that match the source files.
     *
     * @param string $dir Directory to process
     * @param string $basePath Base path for relative path calculation
     */
    private function processRemoval(string $dir, string $basePath = ''): void
    {
        if (!is_dir($dir)) {
            $this->errors[] = 'Source directory not found: ' . $dir;
            return;
        }

        $files = scandir($dir);

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $sourcePath = $dir . '/' . $file;
            $relativePath = ltrim($basePath . '/' . $file, '/');

            if (is_dir($sourcePath)) {
                $this->processRemoval($sourcePath, $relativePath);
            } else {
                $this->removeFile($sourcePath, $relativePath);
            }
        }
    }

    /**
     * Deploy a single file.
     *
     * Creates target directories if needed and copies the source file
     * to the target location.
     *
     * @param string $source Source file path
     * @param string $relativePath Relative path for the target file
     */
    private function deployFile(string $source, string $relativePath): void
    {
        $target = './' . $relativePath;
        $targetDir = dirname($target);

        // Create target directory if it doesn't exist
        if (!is_dir($targetDir)) {
            if (!mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
                $this->errors[] = 'Failed to create directory: ' . $targetDir;
                return;
            }
            $this->cli->info('Created directory: ' . $targetDir, true);
        }

        // Check if source file exists
        if (!file_exists($source)) {
            $this->errors[] = 'Source file not found: ' . $source;
            return;
        }

        // Check if target file exists
        if (file_exists($target)) {
            $this->errors[] = 'Target file already exists: ' . $target;
            return;
        }

        // Copy the file
        if (copy($source, $target)) {
            $this->cli->file($relativePath, true);
            $this->processed[] = $relativePath;
        } else {
            $this->errors[] = 'Failed to copy: ' . $relativePath;
        }
    }

    /**
     * Remove a single file.
     *
     * Checks if the file can be safely removed and removes it if possible.
     * Also removes empty parent directories after file removal.
     *
     * @param string $source Source file path
     * @param string $relativePath Relative path for the target file
     */
    private function removeFile(string $source, string $relativePath): void
    {
        $target = './' . $relativePath;

        // Check if target file exists
        if (!file_exists($target)) {
            $this->cli->info('File not found, skipping: ' . $relativePath, true);
            return;
        }

        // Check if file was originally from our source
        if ($this->isFileFromSource($source, $target)) {
            if (unlink($target)) {
                $this->cli->file($relativePath, true);
                $this->removed[] = $relativePath;

                // Remove empty parent directories
                $this->removeEmptyDirectories(dirname($target));
            } else {
                $this->errors[] = 'Failed to remove: ' . $relativePath;
            }
        } else {
            $this->cli->warning('File was modified, skipping: ' . $relativePath, true);
        }
    }

    /**
     * Check if a target file matches the source file.
     *
     * Compares the contents of the source and target files to determine
     * if they are identical.
     *
     * @param string $source Source file path
     * @param string $target Target file path
     * @return bool True if files match, false otherwise
     */
    private function isFileFromSource(string $source, string $target): bool
    {
        if (!file_exists($source) || !file_exists($target)) {
            return false;
        }

        return file_get_contents($source) === file_get_contents($target);
    }

    /**
     * Remove empty directories.
     *
     * Recursively removes empty directories starting from the given directory.
     *
     * @param string $dir Directory to check and potentially remove
     */
    private function removeEmptyDirectories(string $dir): void
    {
        // Remove directory if it's empty and not the root
        if ($dir !== '.' && $dir !== '..' && is_dir($dir)) {
            if (count(scandir($dir)) === 2) { // Only . and ..
                rmdir($dir);
                $this->cli->info('Removed empty directory: ' . $dir, true);

                // Recursively check parent directory
                $this->removeEmptyDirectories(dirname($dir));
            }
        }
    }

    /**
     * Display operation summary.
     *
     * Shows the results of the deployment or removal operation,
     * including the number of processed files and any errors.
     */
    private function showSummary(): void
    {
        $removeMode = $this->cli->hasOption('remove');

        $this->cli->frame($removeMode ? 'Removal summary' : 'Deployment summary', 'green');

        if (empty($this->removed) && empty($this->processed)) {
            $this->cli->info('Nothing found', true);
        } elseif ($removeMode) {
            $this->cli->success('Removed files: ' . count($this->removed), true);
        } else {
            $this->cli->success('Deployed files: ' . count($this->processed), true);
        }

        if (!empty($this->errors)) {
            $this->cli->error('Errors: ' . count($this->errors), true);
            foreach ($this->errors as $error) {
                $this->cli->frameMiddle('  - ' . $error, 'red');
            }
        }
    }
}
