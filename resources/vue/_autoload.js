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

import { createApp } from 'vue'

const modules = import.meta.glob('./**/*.vue', { eager: true });
Object.entries(modules).forEach(([path, module]) => {
    let name = path.replace(/\.vue$|[.\/]/g, '')
    document.querySelectorAll(`[data-vue-component="${name}"]`).forEach(el => {
        createApp(module.default).mount(el)
    })
})
