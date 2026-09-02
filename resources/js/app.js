import './bootstrap';

import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

function normalizeTheme(value) {
    if (value === 'dark') return 'dark';
    if (value === 'light') return 'light';
    if (value === 'soft' || value === 'blush') return 'light';
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
}

document.addEventListener('alpine:init', () => {
    Alpine.data('themeSwitcher', () => ({
        theme: normalizeTheme(localStorage.getItem('mq-theme')),

        init() {
            this.apply();
        },

        apply() {
            this.theme = normalizeTheme(this.theme);
            document.documentElement.setAttribute('data-theme', this.theme);
            localStorage.setItem('mq-theme', this.theme);
        },

        toggle() {
            this.theme = this.theme === 'dark' ? 'light' : 'dark';
            this.apply();
        },
    }));

    Alpine.data('prescriptionForm', () => ({
        rows: [],
        init() {
            this.addRow();
        },
        addRow() {
            this.rows.push({ medication_name: '', dosage: '', frequency: '', duration: '', instructions: '' });
        },
        removeRow(index) {
            if (this.rows.length > 1) {
                this.rows.splice(index, 1);
            }
        },
    }));
});

Alpine.start();
