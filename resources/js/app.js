import './bootstrap';

import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

document.addEventListener('alpine:init', () => {
    Alpine.data('themeSwitcher', () => ({
        theme: localStorage.getItem('mq-theme') || 'blush',

        init() {
            this.apply();
        },

        apply() {
            document.documentElement.setAttribute('data-theme', this.theme);
            localStorage.setItem('mq-theme', this.theme);
        },

        toggle() {
            this.theme = this.theme === 'blush' ? 'soft' : 'blush';
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
