@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<!-- Professional Dashboard Header -->
<div class="rounded-2xl bg-gradient-to-r from-yellow-100 via-yellow-50 to-white shadow flex items-center px-8 py-6 mb-10 border border-yellow-200">
    <div class="flex items-center justify-center w-16 h-16 bg-yellow-200 rounded-full mr-6 shadow">
        <svg class="w-8 h-8 text-yellow-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.5V6a2 2 0 012-2h14a2 2 0 012 2v7.5M3 13.5l9 6 9-6M3 13.5l9-6 9 6"/></svg>
    </div>
    <div>
        <h1 class="text-3xl font-extrabold text-yellow-800 tracking-tight mb-1">Dashboard</h1>
        <div class="text-gray-600 text-lg font-medium">Conference Management Overview</div>
    </div>
</div>
<hr class="mb-8 border-yellow-200">
@php
    // Dummy conferences and their data
    $dummyConferences = [
        1 => [
            'name' => 'Annual Tech Summit',
            'speakersCount' => 12,
            'speakersGender' => [7, 4, 1],
            'participantsGender' => [120, 95, 5],
            'participantsAge' => [40, 80, 70, 30],
            'participantsNationality' => [60, 40, 50, 30, 40],
            'participantsProfession' => [50, 60, 80, 20, 10],
        ],
        2 => [
            'name' => 'Business Innovation Forum',
            'speakersCount' => 8,
            'speakersGender' => [4, 3, 1],
            'participantsGender' => [60, 38, 2],
            'participantsAge' => [15, 25, 20, 10],
            'participantsNationality' => [20, 10, 15, 10, 5],
            'participantsProfession' => [10, 15, 20, 5, 3],
        ],
    ];
    $selectedConferenceId = request('conference_id', 1);
    $selectedConference = $dummyConferences[$selectedConferenceId] ?? $dummyConferences[1];
@endphp
<div class="max-w-4xl mx-auto">
    <form method="GET" action="" class="mb-8 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="w-full md:w-1/2">
            <label for="conference_id" class="block text-sm font-medium text-gray-700 mb-1">Select Conference</label>
            <select name="conference_id" id="conference_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" onchange="this.form.submit()">
                @foreach($dummyConferences as $id => $conf)
                    <option value="{{ $id }}" {{ $selectedConferenceId == $id ? 'selected' : '' }}>{{ $conf['name'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-full md:w-1/2 text-right">
            <span class="inline-block bg-yellow-100 text-yellow-800 px-4 py-2 rounded-lg font-semibold">{{ $selectedConference['name'] }}</span>
        </div>
    </form>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
        <!-- Speakers Card -->
        <div class="bg-white rounded-2xl shadow-lg p-6" aria-label="Speakers chart">
            <div class="flex items-center mb-2">
                <span class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 rounded-full mr-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </span>
                <h2 class="text-lg font-semibold text-gray-900">Speakers</h2>
            </div>
            <p class="text-gray-500 mb-2">Total speakers and gender distribution for this event.</p>
            <div class="flex flex-col items-center">
                <div class="text-sm text-gray-500 mb-1">Total: <span class="font-bold text-blue-700">{{ $selectedConference['speakersCount'] }}</span></div>
                <div class="relative w-full flex flex-col items-center min-h-[420px]">
                    <div style="width:400px;height:400px;">
                        <canvas id="speakersGenderChart" width="400" height="400"></canvas>
                    </div>
                    <button onclick="downloadChart('speakersGenderChart')" class="mt-2 text-xs text-blue-600 hover:underline">Download PNG</button>
                </div>
            </div>
        </div>
        <!-- Participants Gender -->
        <div class="bg-white rounded-2xl shadow-lg p-6" aria-label="Participants by Gender chart">
            <div class="flex items-center mb-2">
                <span class="inline-flex items-center justify-center w-8 h-8 bg-pink-100 rounded-full mr-2">
                    <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 7v-6m0 0l-9-5m9 5l9-5"/></svg>
                </span>
                <h2 class="text-lg font-semibold text-gray-900">Participants by Gender</h2>
            </div>
            <p class="text-gray-500 mb-2">Gender breakdown of all participants.</p>
            <div class="text-sm text-gray-500 mb-1">Total: <span class="font-bold text-pink-700">{{ array_sum($selectedConference['participantsGender']) }}</span></div>
            <div class="relative w-full flex flex-col items-center min-h-[420px]">
                <div style="width:400px;height:400px;">
                    <canvas id="participantsGenderChart" width="400" height="400"></canvas>
                </div>
                <button onclick="downloadChart('participantsGenderChart')" class="mt-2 text-xs text-pink-600 hover:underline">Download PNG</button>
            </div>
        </div>
        <!-- Participants Age -->
        <div class="bg-white rounded-2xl shadow-lg p-6" aria-label="Participants by Age Group chart">
            <div class="flex items-center mb-2">
                <span class="inline-flex items-center justify-center w-8 h-8 bg-green-100 rounded-full mr-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
                <h2 class="text-lg font-semibold text-gray-900">Participants by Age Group</h2>
            </div>
            <p class="text-gray-500 mb-2">Distribution of participants by age group.</p>
            <div class="text-sm text-gray-500 mb-1">Total: <span class="font-bold text-green-700">{{ array_sum($selectedConference['participantsAge']) }}</span></div>
            <div class="relative w-full flex flex-col items-center">
                <div class="w-72 h-64">
                    <canvas id="participantsAgeChart"></canvas>
                </div>
                <button onclick="downloadChart('participantsAgeChart')" class="mt-2 text-xs text-green-600 hover:underline">Download PNG</button>
            </div>
        </div>
        <!-- Participants Nationality -->
        <div class="bg-white rounded-2xl shadow-lg p-6" aria-label="Participants by Nationality chart">
            <div class="flex items-center mb-2">
                <span class="inline-flex items-center justify-center w-8 h-8 bg-yellow-100 rounded-full mr-2">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2a10 10 0 100 20 10 10 0 000-20zm0 0v20m10-10H2"/></svg>
                </span>
                <h2 class="text-lg font-semibold text-gray-900">Participants by Nationality</h2>
            </div>
            <p class="text-gray-500 mb-2">Top nationalities represented at this event.</p>
            <div class="text-sm text-gray-500 mb-1">Total: <span class="font-bold text-yellow-700">{{ array_sum($selectedConference['participantsNationality']) }}</span></div>
            <div class="relative w-full flex flex-col items-center min-h-[420px]">
                <div style="width:400px;height:400px;">
                    <canvas id="participantsNationalityChart" width="400" height="400"></canvas>
                </div>
                <button onclick="downloadChart('participantsNationalityChart')" class="mt-2 text-xs text-yellow-600 hover:underline">Download PNG</button>
            </div>
        </div>
        <!-- Participants Profession -->
        <div class="bg-white rounded-2xl shadow-lg p-6 md:col-span-2" aria-label="Participants by Profession chart">
            <div class="flex items-center mb-2">
                <span class="inline-flex items-center justify-center w-8 h-8 bg-purple-100 rounded-full mr-2">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 018 0v2m-4-4V7a4 4 0 10-8 0v6m0 4h8"/></svg>
                </span>
                <h2 class="text-lg font-semibold text-gray-900">Participants by Profession</h2>
            </div>
            <p class="text-gray-500 mb-2">Professional background of participants.</p>
            <div class="text-sm text-gray-500 mb-1">Total: <span class="font-bold text-purple-700">{{ array_sum($selectedConference['participantsProfession']) }}</span></div>
            <div class="relative w-full flex flex-col items-center">
                <div class="w-full md:w-5/6 h-72">
                    <canvas id="participantsProfessionChart"></canvas>
                </div>
                <button onclick="downloadChart('participantsProfessionChart')" class="mt-2 text-xs text-purple-600 hover:underline">Download PNG</button>
            </div>
        </div>
    </div>
</div>
<!-- Chart.js CDN and datalabels plugin -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script>
const confData = @json($selectedConference);
const pieOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { position: 'bottom', labels: { font: { size: 14 } } },
        datalabels: {
            color: '#22223b',
            font: { weight: 'bold', size: 12 },
            formatter: (value, ctx) => {
                let sum = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                let pct = sum ? (value * 100 / sum).toFixed(1) + '%' : '';
                return value + ' (' + pct + ')';
            }
        },
        tooltip: {
            callbacks: {
                label: function(context) {
                    let label = context.label || '';
                    let value = context.parsed;
                    let sum = context.dataset.data.reduce((a, b) => a + b, 0);
                    let pct = sum ? (value * 100 / sum).toFixed(1) + '%' : '';
                    return `${label}: ${value} (${pct})`;
                }
            }
        },
        title: { display: false }
    },
    layout: { padding: 10 },
    animation: { duration: 1200, easing: 'easeOutQuart' }
};
const barOptions = {
    responsive: true,
    plugins: {
        legend: { display: false },
        datalabels: {
            anchor: 'end',
            align: 'top',
            color: '#22223b',
            font: { weight: 'bold', size: 14 },
            formatter: (value) => value
        },
        tooltip: {
            callbacks: {
                label: function(context) {
                    return `${context.label}: ${context.parsed.y}`;
                }
            }
        },
        title: { display: false }
    },
    layout: { padding: 10 },
    scales: {
        x: { grid: { display: false }, ticks: { font: { size: 13 } } },
        y: { grid: { color: '#f3f4f6' }, beginAtZero: true, ticks: { font: { size: 13 } } }
    },
    animation: { duration: 1200, easing: 'easeOutQuart' }
};
const speakersGenderData = {
    labels: ['Male', 'Female', 'Other'],
    datasets: [{
        data: confData.speakersGender,
        backgroundColor: ['#2563eb', '#ec4899', '#f59e42'],
        borderWidth: 2,
        borderColor: '#fff',
        hoverOffset: 8
    }]
};
const participantsGenderData = {
    labels: ['Male', 'Female', 'Other'],
    datasets: [{
        data: confData.participantsGender,
        backgroundColor: ['#2563eb', '#ec4899', '#f59e42'],
        borderWidth: 2,
        borderColor: '#fff',
        hoverOffset: 8
    }]
};
const participantsAgeData = {
    labels: ['18-25', '26-35', '36-50', '51+'],
    datasets: [{
        label: 'Participants',
        data: confData.participantsAge,
        backgroundColor: ['#38bdf8', '#34d399', '#fbbf24', '#f87171'],
        borderRadius: 8,
        maxBarThickness: 40
    }]
};
const participantsNationalityData = {
    labels: ['USA', 'UK', 'India', 'Nigeria', 'Other'],
    datasets: [{
        data: confData.participantsNationality,
        backgroundColor: ['#f87171', '#60a5fa', '#34d399', '#fbbf24', '#a78bfa'],
        borderWidth: 2,
        borderColor: '#fff',
        hoverOffset: 8
    }]
};
const participantsProfessionData = {
    labels: ['Student', 'Academic', 'Industry', 'Government', 'Other'],
    datasets: [{
        label: 'Participants',
        data: confData.participantsProfession,
        backgroundColor: ['#fbbf24', '#60a5fa', '#34d399', '#f87171', '#a78bfa'],
        borderRadius: 8,
        maxBarThickness: 40
    }]
};
window.addEventListener('DOMContentLoaded', function() {
    Chart.register(window.ChartDataLabels);
    window.speakersChart = new Chart(document.getElementById('speakersGenderChart'), {
        type: 'pie',
        data: speakersGenderData,
        options: pieOptions,
        plugins: [ChartDataLabels]
    });
    window.participantsGenderChart = new Chart(document.getElementById('participantsGenderChart'), {
        type: 'pie',
        data: participantsGenderData,
        options: pieOptions,
        plugins: [ChartDataLabels]
    });
    window.participantsAgeChart = new Chart(document.getElementById('participantsAgeChart'), {
        type: 'bar',
        data: participantsAgeData,
        options: barOptions,
        plugins: [ChartDataLabels]
    });
    window.participantsNationalityChart = new Chart(document.getElementById('participantsNationalityChart'), {
        type: 'pie',
        data: participantsNationalityData,
        options: pieOptions,
        plugins: [ChartDataLabels]
    });
    window.participantsProfessionChart = new Chart(document.getElementById('participantsProfessionChart'), {
        type: 'bar',
        data: participantsProfessionData,
        options: barOptions,
        plugins: [ChartDataLabels]
    });
});
function downloadChart(chartId) {
    const chart = window[chartId.replace('Chart', '') + 'Chart'];
    if (!chart) return;
    const link = document.createElement('a');
    link.href = chart.toBase64Image();
    link.download = chartId + '.png';
    link.click();
}
</script>
@endsection
