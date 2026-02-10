<?php

declare(strict_types=1);

/**
 * 2026 DeveMain
 *
 * All rights reserved. For internal use only.
 * Unauthorized copying, modification, or distribution is prohibited.
 *
 * @author    DeveMain <devemain@gmail.com>
 * @copyright 2026 DeveMain
 * @license   PROPRIETARY
 *
 * @link      https://github.com/DeveMain
 */

namespace Devemain;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

/**
 * Managing strict types declarations in PHP files.
 * Handles adding, updating, and removing declare(strict_types=1) statements.
 */
class StrictTypesManager
{
    /**
     * Directories to process for strict types declarations.
     */
    private array $directories;

    /**
     * Creates a new instance.
     *
     * @param  CliHelper  $cli  CLI helper for command line operations
     */
    public function __construct(
        private readonly CliHelper $cli
    ) {
        $this->directories = ['_dm', 'app', 'bootstrap', 'config', 'database', 'public', 'routes', 'tests'];
    }

    /**
     * Main execution method for strict types operations. Handles adding and removing declare(strict_types=1).
     */
    public function run(): void
    {
        $removeMode = $this->cli->hasOption('remove');

        $this->cli->frame(($removeMode ? 'Removing' : 'Adding') . ' strict types declarations');

        $processed = $skipped = 0;

        foreach ($this->directories as $dir) {
            if (!is_dir($dir)) {
                $this->cli->warning('Skipped: ' . $dir . ' (no such directory)', true);

                continue;
            }

            $this->cli->dir($dir, true);
            [$dirProcessed, $dirSkipped] = $this->processDirectory($dir);
            $processed += $dirProcessed;
            $skipped += $dirSkipped;
        }

        $this->cli->frame('Processed files: ' . $processed . ($skipped ? ' / ' . $skipped . ' skipped' : ''));
        $this->cli->success($removeMode ? 'Strict types declarations removed!' : 'All strict types declarations have been added!', true);
    }

    /**
     * Process all files in a directory for strict types operations.
     *
     * @param  string  $dir  Directory path to process
     * @return array Array containing [processedCount, skippedCount]
     */
    private function processDirectory(string $dir): array
    {
        $processed = $skipped = 0;

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        // Filter only PHP files
        $files = new RegexIterator($files, '/\.php$/i');

        foreach ($files as $file) {
            if (!$file->isFile()) {
                continue;
            }

            $path = $file->getPathname();

            // Skip cache and view directories
            if (str_contains($path, '/cache/') || str_contains($path, '/views/')) {
                continue;
            }

            $content = file_get_contents($path);
            if ($content === false) {
                continue;
            }

            $this->processFile($path, $content, $processed, $skipped);
        }

        if ($processed === 0) {
            $this->cli->error('No files found for processing', true);
        }

        return [$processed, $skipped];
    }

    /**
     * Process a single file for strict types operations.
     *
     * @param  string  $path  File path to process
     * @param  string  $content  File content
     * @param  int  &$processed  Reference to processed counter
     * @param  int  &$skipped  Reference to skipped counter
     */
    private function processFile(string $path, string $content, int &$processed, int &$skipped): void
    {
        $removeMode = $this->cli->hasOption('remove');

        // Check if the file contains declare(strict_types=1)
        if (preg_match('/' . Fmt::STRICT_TYPES_PATTERN . '/', $content)) {
            if ($removeMode) {
                // Remove the strict types declaration
                $newContent = preg_replace(
                    '/' . Fmt::STRICT_TYPES_PATTERN . '/',
                    '',
                    $content,
                    1
                );
                $newContent = preg_replace('/^<\?php\n\n\/\*\*/', Fmt::PHP_OPEN . Fmt::EOL . '/**', $newContent, 1);

                if ($newContent !== $content) {
                    if (file_put_contents($path, $newContent) !== false) {
                        $this->cli->success('Removed: ' . $path, true);
                        $processed++;
                    } else {
                        $this->cli->error('Failed: ' . $path, true);
                        $skipped++;
                    }
                } else {
                    $this->cli->warning('Skipped: ' . $path, true);
                    $skipped++;
                }
            } else {
                $this->cli->info('Skipped: ' . $path, true);
                $skipped++;
            }

            return;
        }

        if ($removeMode) {
            $this->cli->info('Skipped: ' . $path, true);
            $skipped++;

            return;
        }

        // Add strict_types declaration
        $newContent = $this->addStrictTypesDeclaration($content);

        if ($newContent !== $content) {
            if (file_put_contents($path, $newContent) !== false) {
                $this->cli->success('Added: ' . $path, true);
                $processed++;
            } else {
                $this->cli->error('Failed: ' . $path, true);
                $skipped++;
            }
        } else {
            $this->cli->error('Failed: ' . $path, true);
            $skipped++;
        }
    }

    /**
     * Add strict_types declaration to PHP file content
     *
     * @param  string  $content  Original file content
     * @return string Modified content with strict_types declaration
     */
    private function addStrictTypesDeclaration(string $content): string
    {
        // Check if declare(strict_types=1) already exists
        if (str_contains($content, Fmt::STRICT_TYPES)) {
            return $content;
        }

        $phpTagPos = strpos($content, Fmt::PHP_OPEN);
        if ($phpTagPos === false) {
            return $content;
        }

        // Take the beginning of the file before <?php
        $beforePhpTag = substr($content, 0, $phpTagPos);

        // Take everything after <?php
        $afterPhpTag = substr($content, $phpTagPos + 5);

        // Remove leading spaces/line breaks
        $afterPhpTag = ltrim($afterPhpTag, " \t\r\n");

        // Format according to PSR-12
        return $beforePhpTag .
            Fmt::PHP_OPEN . Fmt::EOL2 . // <?php + empty line
            Fmt::strictTypes() . // declare + empty line
            $afterPhpTag;
    }
}
