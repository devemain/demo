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

class LicenseManager
{
    private string $year;
    private string $licenseTemplate;
    private string $path;

    public function __construct(private CliHelper $cli)
    {
        $this->year = date('Y');
        $this->licenseTemplate = $this->getLicenseTemplate();
        $this->path = '_dm/config/LICENSE.md';
    }

    public function run(): void
    {
        $this->cli->frame('Generating LICENSE.md file');

        $dir = dirname($this->path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
            $this->cli->info('Created directory: ' . $dir, true);
        }

        $action = is_file($this->path) ? 'updated' : 'created';
        if (file_put_contents($this->path, $this->licenseTemplate) !== false) {
            $this->cli->success('License file ' . $action . ': ' . $this->path, true);
        } else {
            $this->cli->error('Failed to create license file: ' . $this->path, true);
        }
    }

    private function getLicenseTemplate(): string
    {
        return <<<LICENSE
            # PROPRIETARY LICENSE
            
            Copyright (c) $this->year DeveMain. All rights reserved.
            
            This software and associated documentation files (the "Software") are
            the proprietary property of DeveMain. The Software is protected by
            copyright law and international treaty provisions.
            
            ## Restrictions
            
            You may not:
            
            - Copy, modify, or distribute the Software
            - Reverse engineer, decompile, or disassemble the Software  
            - Use the Software for any commercial purpose without permission
            - Remove or alter any copyright notices
            
            ## Contact
            
            For licensing inquiries, contact: [devemain@gmail.com](mailto:devemain@gmail.com)
            
            ---
            
            *This license applies to all source code, binaries, and documentation
            in this repository unless otherwise specified.*
            
            LICENSE;
    }
}
