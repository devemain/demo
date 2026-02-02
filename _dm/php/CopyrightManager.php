<?php
/**
 * 2026 DeveMain
 *
 * All rights reserved. For internal use only.
 * Unauthorized copying, modification, or distribution is prohibited.
 *
 * @author    DeveMain <devemain@gmail.com>
 * @copyright 2026 DeveMain
 * @license   PROPRIETARY
 * @link      https://github.com/DeveMain
 */

namespace Devemain;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

/**
 * Managing copyright notices in project files.
 * Handles adding, updating, and removing copyright headers from various file types.
 */
class CopyrightManager
{
    /**
     * Current year for copyright notices.
     */
    private string $year;

    /**
     * Template for copyright notices.
     */
    private string $copyrightTemplate;

    /**
     * Directories to process for copyright notices.
     */
    private array $directories;

    /**
     * File extensions to process for copyright notices.
     */
    private array $extensions;

    /**
     * Root files to process for copyright notices.
     */
    private array $rootFiles;

    /**
     * Creates a new instance.
     *
     * @param CliHelper $cli CLI helper for command line operations
     */
    public function __construct(
        private readonly CliHelper $cli
    ) {
        $this->year = date('Y');
        $this->copyrightTemplate = $this->getCopyrightTemplate();
        $this->directories = ['_dm', 'app', 'database', 'resources', 'routes', '.github'];
        $this->extensions = ['php', 'js', 'css', 'scss', 'sh', 'yml', 'yaml', 'vue'];
        $this->rootFiles = ['dm.sh', 'docker.sh', 'docker-compose.yml', 'Dockerfile'];
    }

    /**
     * Main execution method for copyright operations. Handles adding, updating, and removing copyright headers.
     */
    public function run(): void
    {
        $removeMode = $this->cli->hasOption('remove');

        $this->cli->frame(($removeMode ? 'Deleting' : 'Updating') . ' copyright');

        $processed = 0;
        $skipped = 0;

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

        // Parse root files
        $this->cli->frame(($removeMode ? 'Deleting' : 'Updating') . ' copyright in root files');
        [$dirProcessed, $dirSkipped] = $this->processRootFiles();
        $processed += $dirProcessed;
        $skipped += $dirSkipped;

        $this->cli->frame('Processed files: ' . $processed . ($skipped ? ' / ' . $skipped . ' skipped' : ''));
        $this->cli->success($removeMode ? 'Copyright removed!' : 'All copyrights have been updated!', true);
    }

    /**
     * Process all files in a directory for copyright operations.
     *
     * @param string $dir Directory path to process
     * @return array Array containing [processedCount, skippedCount]
     */
    private function processDirectory(string $dir): array
    {
        $processed = 0;
        $skipped = 0;

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($files as $file) {
            if (!$file->isFile()) {
                continue;
            }

            $path = $file->getPathname();
            $filename = $file->getFilename();
            $extension = $file->getExtension();
            $isBlade = str_contains($filename, '.blade.php');
            $isDocker = $filename === 'Dockerfile';

            if (!$isBlade && !$isDocker && !in_array($extension, $this->extensions, true)) {
                continue;
            }

            $content = file_get_contents($path);
            if ($content === false) {
                continue;
            }

            $this->processFile($path, $filename, $content, $extension, $isBlade, $processed, $skipped);
        }

        if ($processed === 0) {
            $this->cli->error('No files found for processing', true);
        }

        return [$processed, $skipped];
    }

    /**
     * Process root files for copyright operations.
     *
     * @return array Array containing [processedCount, skippedCount]
     */
    private function processRootFiles(): array
    {
        $processed = 0;
        $skipped = 0;

        foreach ($this->rootFiles as $path) {
            if (!file_exists($path)) {
                continue;
            }

            $content = file_get_contents($path);
            if ($content === false) {
                continue;
            }

            $filename = basename($path);
            $extension = pathinfo($path, PATHINFO_EXTENSION);
            $isBlade = str_contains($path, '.blade.php');

            $this->processFile($path, $filename, $content, $extension, $isBlade, $processed, $skipped);
        }

        if ($processed === 0) {
            $this->cli->error('No files found for processing', true);
        }

        return [$processed, $skipped];
    }

    /**
     * Process a single file for copyright operations.
     *
     * @param string $path File path
     * @param string $filename File name
     * @param string $content File content
     * @param string $extension File extension
     * @param bool $isBlade Whether the file is a Blade template
     * @param int &$processed Reference to processed files counter
     * @param int &$skipped Reference to skipped files counter
     */
    private function processFile(string $path, string $filename, string $content, string $extension,
                                 bool $isBlade, int &$processed, int &$skipped): void
    {
        if ($this->cli->hasOption('remove')) {
            if ($this->hasDeveMainCopyright($content)) {
                $newContent = $this->removeCopyright($content, $path, $isBlade);
                if ($newContent !== $content && file_put_contents($path, $newContent) !== false) {
                    $this->cli->file($filename, true);
                    $processed++;
                }
            } else {
                $this->cli->warning('Skipped: ' . $filename, true);
                $skipped++;
            }
        } elseif ($this->updateFile($path, $content, $extension, $isBlade)) {
            $this->cli->file($filename, true);
            $processed++;
        }
    }

    /**
     * Update a file with copyright header.
     *
     * @param string $path File path
     * @param string $content Current file content
     * @param string $extension File extension
     * @param bool $isBlade Whether the file is a Blade template
     * @return bool True if file was successfully updated, false otherwise
     */
    private function updateFile(string $path, string $content, string $extension, bool $isBlade): bool
    {
        $header = $this->getCopyrightHeader($path, $isBlade);

        if ($this->hasDeveMainCopyright($content)) {
            $newContent = $this->replaceCopyright($content, $header, $path, $extension, $isBlade);
        } else {
            $newContent = $this->addHeaderToContent($content, $header, $extension);
        }

        return file_put_contents($path, $newContent) !== false;
    }

    /**
     * Get the copyright notice template.
     *
     * @return string Copyright notice template with current year
     */
    private function getCopyrightTemplate(): string
    {
        return <<<COPYRIGHT
            /**
             * $this->year DeveMain
             *
             * All rights reserved. For internal use only.
             * Unauthorized copying, modification, or distribution is prohibited.
             *
             * @author    DeveMain <devemain@gmail.com>
             * @copyright $this->year DeveMain
             * @license   PROPRIETARY
             * @link      https://github.com/DeveMain
             */
            COPYRIGHT;
    }

    /**
     * Check if content contains a DeveMain copyright notice.
     *
     * @param string $content File content to check
     * @return bool True if DeveMain copyright is found, false otherwise
     */
    private function hasDeveMainCopyright(string $content): bool
    {
        return preg_match('/\d{4} DeveMain/', $content) === 1;
    }

    /**
     * Generate a copyright header formatted for a specific file type.
     *
     * @param string $path File path
     * @param bool $isBlade Whether the file is a Blade template
     * @return string Formatted copyright header for the file type
     */
    private function getCopyrightHeader(string $path, bool $isBlade): string
    {
        // Extracting plain text without /** and */
        $lines = explode(PHP_EOL, $this->copyrightTemplate);
        $cleanLines = [];

        foreach ($lines as $line) {
            $line = trim($line);
            // Remove the opening and closing comment tags
            if (str_starts_with($line, '/**') || str_starts_with($line, '*/')) {
                continue;
            }
            // Remove leading * and spaces, but keep empty lines
            $cleanLine = preg_replace('/^\s*\*\s?/', '', $line);
            $cleanLines[] = $cleanLine;
        }

        // Remove the last empty line if there is one
        if (end($cleanLines) === '') {
            array_pop($cleanLines);
        }

        if ($isBlade) {
            $result = '{{--' . PHP_EOL;
            foreach ($cleanLines as $line) {
                $result .= ' |' . ($line !== '' ? ' ' . $line : '') . PHP_EOL;
            }
            return $result . ' --}}' . CliHelper::PHP_EOL2;
        }

        $filename = basename($path);
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        if ($filename === 'Dockerfile') {
            $result = '# ============================================================================' . PHP_EOL;
            foreach ($cleanLines as $line) {
                $result .= '#' . ($line !== '' ? ' ' . $line : '') . PHP_EOL;
            }
            return $result . '# ============================================================================' . CliHelper::PHP_EOL2;
        }

        switch ($extension) {
            case 'php':
                $result = '<?php' . PHP_EOL . '/**' . PHP_EOL;
                foreach ($cleanLines as $line) {
                    $result .= ' *' . ($line !== '' ? ' ' . $line : '') . PHP_EOL;
                }
                return $result . ' */' . CliHelper::PHP_EOL2;

            case 'css':
            case 'scss':
                $result = '/*!' . PHP_EOL;
                foreach ($cleanLines as $line) {
                    $result .= ' *' . ($line !== '' ? ' ' . $line : '') . PHP_EOL;
                }
                return $result . ' */' . CliHelper::PHP_EOL2;

            case 'yml':
            case 'yaml':
            case 'sh':
                $result = '# ============================================================================' . PHP_EOL;
                foreach ($cleanLines as $line) {
                    $result .= '#' . ($line !== '' ? ' ' . $line : '') . PHP_EOL;
                }
                return $result . '# ============================================================================' . CliHelper::PHP_EOL2;

            case 'json':
                return '';

            case 'md':
            case 'vue':
                return '<!--' . PHP_EOL . implode(PHP_EOL, $cleanLines) . PHP_EOL . '-->' . CliHelper::PHP_EOL2;

            case 'js':
            default:
                $result = '/**' . PHP_EOL;
                foreach ($cleanLines as $line) {
                    $result .= ' *' . ($line !== '' ? ' ' . $line : '') . PHP_EOL;
                }
                return $result . ' */' . CliHelper::PHP_EOL2;
        }
    }

    /**
     * Remove copyright notice from file content.
     *
     * @param string $content File content
     * @param string $path File path
     * @param bool $isBlade Whether the file is a Blade template
     * @return string Content with copyright removed
     */
    private function removeCopyright(string $content, string $path, bool $isBlade): string
    {
        $filename = basename($path);
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        // For Blade templates
        if ($isBlade) {
            $pattern = '/\{\{--[\s\S]*?DeveMain[\s\S]*?--\}\}\s*\n*/';
            return preg_replace($pattern, '', $content, 1);
        }

        // For YAML files & Dockerfile
        if (in_array($extension, ['yml', 'yaml'], true) || $filename === 'Dockerfile') {
            $pattern = '/# ============================================================================[\s\S]*?# ============================================================================\s*\n*/';
            return preg_replace($pattern, '', $content, 1);
        }

        switch ($extension) {
            case 'php':
                $pattern = '/(<\?php\s*\n*)\/\*\*[\s\S]*?\*\/\s*\n*/s';
                return preg_replace($pattern, '$1' . PHP_EOL, $content, 1);

            case 'js':
                $pattern = '/\/\*\*[\s\S]*?\*\/\s*\n*/s';
                return preg_replace($pattern, '', $content, 1);

            case 'css':
            case 'scss':
                $pattern = '/\/\*\![\s\S]*?\*\/\s*\n*/s';
                return preg_replace($pattern, '', $content, 1);

            case 'sh':
                $patterns = [
                    '/(#!\/bin\/bash\s*\n)# ============================================================================[\s\S]*?# ============================================================================\s*\n*/s',
                    '/(#!\/usr\/bin\/env bash\s*\n)# ============================================================================[\s\S]*?# ============================================================================\s*\n*/s',
                    '/^# ============================================================================[\s\S]*?# ============================================================================\s*\n*/s',
                ];
                foreach ($patterns as $pattern) {
                    $content = preg_replace($pattern, '$1', $content, 1);
                }
                return $content;

            case 'md':
            case 'vue':
                $pattern = '/<!--[\s\S]*?-->\s*\n*/s';
                return preg_replace($pattern, '', $content, 1);

            default:
                return $content;
        }
    }

    /**
     * Replace existing copyright notice with a new one.
     *
     * @param string $content Current file content
     * @param string $header New copyright header
     * @param string $path File path
     * @param string $extension File extension
     * @param bool $isBlade Whether the file is a Blade template
     * @return string Content with updated copyright
     */
    private function replaceCopyright(string $content, string $header, string $path, string $extension, bool $isBlade): string
    {
        $contentWithoutCopyright = $this->removeCopyright($content, $path, $isBlade);
        return $this->addHeaderToContent($contentWithoutCopyright, $header, $extension);
    }

    /**
     * Add copyright header to the beginning of file content.
     *
     * @param string $content Original file content
     * @param string $header Copyright header to add
     * @param string $extension File extension
     * @return string Content with copyright header added
     */
    private function addHeaderToContent(string $content, string $header, string $extension): string
    {
        $content = ltrim($content, "\n\r");

        switch ($extension) {
            case 'php':
                // Remove possible <?php from the beginning of the content if it is already in the header
                if (str_starts_with($header, '<?php') && str_starts_with(trim($content), '<?php')) {
                    $content = substr($content, 5);
                    $content = ltrim($content, "\n\r\t ");
                }
                break;

            case 'sh':
                // For SH: the shebang should come first, then our title
                if (str_starts_with(trim($content), '#!')) {
                    $firstNewline = strpos($content, PHP_EOL);
                    if ($firstNewline === false) {
                        // Only shebang without line breaks
                        return $content . PHP_EOL . $header;
                    }
                    $shebang = substr($content, 0, $firstNewline + 1);
                    $rest = substr($content, $firstNewline + 1);
                    $rest = ltrim($rest, "\n\r\t ");
                    return $shebang . $header . $rest;
                }
                break;

            case 'yml':
            case 'yaml':
                if (str_starts_with(trim($content), '---')) {
                    $firstNewline = strpos($content, PHP_EOL);
                    if ($firstNewline !== false) {
                        $firstLine = substr($content, 0, $firstNewline + 1);
                        $rest = substr($content, $firstNewline + 1);
                        $rest = ltrim($rest, "\n\r\t ");
                        return $firstLine . $header . $rest;
                    }
                }
                break;

            case 'vue':
                $trimmedContent = ltrim($content);
                if (str_starts_with($trimmedContent, '<template>') ||
                    str_starts_with($trimmedContent, '<script>') ||
                    str_starts_with($trimmedContent, '<style>')) {
                    return $header . $content;
                }
                break;
        }

        return $header . $content;
    }
}
