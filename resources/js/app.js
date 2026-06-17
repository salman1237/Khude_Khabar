import './bootstrap';

import Alpine from 'alpinejs';
import {
    Chart,
    LineController,
    LineElement,
    PointElement,
    LinearScale,
    CategoryScale,
    Tooltip,
    Legend,
    Filler,
} from 'chart.js';

// Register only the Chart.js pieces we use (keeps the bundle small).
Chart.register(
    LineController,
    LineElement,
    PointElement,
    LinearScale,
    CategoryScale,
    Tooltip,
    Legend,
    Filler,
);

window.Chart = Chart;

/**
 * Alpine component that renders a child's weight/height growth chart.
 */
Alpine.data('growthChart', ({ labels, weight, height }) => ({
    chart: null,
    render() {
        this.chart = new Chart(this.$refs.canvas, {
            type: 'line',
            data: {
                labels,
                datasets: [
                    {
                        label: 'ওজন (কেজি)',
                        data: weight,
                        borderColor: '#1f8a52',
                        backgroundColor: 'rgba(31, 138, 82, 0.12)',
                        tension: 0.35,
                        fill: true,
                        yAxisID: 'y',
                    },
                    {
                        label: 'উচ্চতা (সেমি)',
                        data: height,
                        borderColor: '#f0883e',
                        backgroundColor: 'rgba(240, 136, 62, 0.10)',
                        tension: 0.35,
                        fill: false,
                        yAxisID: 'y1',
                    },
                ],
            },
            options: {
                responsive: true,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { labels: { font: { family: 'Hind Siliguri' } } },
                },
                scales: {
                    y: {
                        type: 'linear',
                        position: 'left',
                        title: { display: true, text: 'ওজন (কেজি)' },
                    },
                    y1: {
                        type: 'linear',
                        position: 'right',
                        grid: { drawOnChartArea: false },
                        title: { display: true, text: 'উচ্চতা (সেমি)' },
                    },
                },
            },
        });
    },
}));

window.Alpine = Alpine;

Alpine.start();
