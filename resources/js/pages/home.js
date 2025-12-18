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

class FactFetcher {
    constructor() {
        this.apiUrl = '/api/v1/fact';
        this.currentFact = null;
        this.bindEvents();
        this.loadFactOnStart();
    }

    bindEvents() {
        const getFactBtn = document.getElementById('get-fact');
        const copyFactBtn = document.getElementById('copy-fact');

        if (getFactBtn) {
            getFactBtn.addEventListener('click', () => this.fetchFact());
        }
        if (copyFactBtn) {
            copyFactBtn.addEventListener('click', () => this.copyFact());
        }
    }

    async fetchFact() {
        const button = document.getElementById('get-fact');
        const container = document.querySelector('#fact-content .fact');

        if (!button || !container) return;

        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
        if (container) container.classList.add('loading');

        try {
            const response = await fetch(this.apiUrl);
            const data = await response.json();

            if (data.success) {
                this.displayFact(data.data);
                this.currentFact = data.data;
            } else {
                this.displayError(data.message || 'Failed to load fact');
            }
        } catch (error) {
            console.error('Fetch error:', error);
            this.displayError('Network error. Please try again.');
        } finally {
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-rotate"></i> Get New Fact';
            if (container) container.classList.remove('loading');
        }
    }

    displayFact(fact) {
        const container = document.getElementById('fact-content');
        const now = new Date().toLocaleTimeString();

        if (!container) return;
        container.innerHTML = `<p class="fact">${fact.content}</p>`;

        document.getElementById('views-count').textContent = fact.views || '0';
        document.getElementById('fact-id').textContent = fact.id || '-';
        document.getElementById('last-updated').textContent = now;
    }

    displayError(message) {
        const container = document.getElementById('fact-content');
        if (!container) return;

        container.innerHTML = `<p class="fact fact-error">❌ ${message}</p>`;
    }

    async copyFact() {
        if (!this.currentFact) {
            alert('No fact to copy! Get one first.');
            return;
        }

        try {
            await navigator.clipboard.writeText(this.currentFact.content);

            const btn = document.getElementById('copy-fact');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
            btn.style.background = '#27ae60';

            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.style.background = '';
            }, 1500);

        } catch (err) {
            console.error('Copy failed:', err);
            alert('Failed to copy fact');
        }
    }

    loadFactOnStart() {
        setTimeout(() => this.fetchFact(), 500);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new FactFetcher();
});
