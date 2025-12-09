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

class ConfigDeployer
{
    private string $sourceDir;
    private array $processed;
    private array $errors;
    private array $removed;

    public function __construct(private CliHelper $cli)
    {
        $this->sourceDir = '_dm/config';
        $this->processed = [];
        $this->removed = [];
        $this->errors = [];
    }

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

    private function isFileFromSource(string $source, string $target): bool
    {
        if (!file_exists($source) || !file_exists($target)) {
            return false;
        }

        return file_get_contents($source) === file_get_contents($target);
    }

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
