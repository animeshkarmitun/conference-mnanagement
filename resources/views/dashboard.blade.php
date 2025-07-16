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
<!-- Conference Progress Section -->
<div class="max-w-4xl mx-auto mb-8">
    <div class="bg-white rounded-2xl shadow flex flex-col md:flex-row items-center justify-between p-6 border-l-4 border-yellow-400">
        <div class="flex-1 flex flex-col md:flex-row md:items-center gap-4">
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center justify-center w-12 h-12 bg-yellow-100 rounded-full">
                    <svg class="w-7 h-7 text-yellow-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
                <div>
                    <div class="text-lg font-bold text-yellow-700">Conference Progress</div>
                    <div class="text-sm text-gray-500">Annual Tech Summit</div>
                </div>
            </div>
            <div class="flex flex-col md:ml-8">
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="8" width="18" height="13" rx="2"/><path d="M16 3v4M8 3v4"/></svg>
                    <span>June 10–14, 2024</span>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-600 mt-1">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2a4 4 0 018 0v2m-4-4V7a4 4 0 10-8 0v6m0 4h8"/></svg>
                    <span>Sessions Completed: <span class="font-semibold text-green-700">8</span> / 12</span>
                </div>
            </div>
        </div>
        <div class="flex flex-col items-end mt-4 md:mt-0 md:ml-8 min-w-[180px]">
            <div class="flex items-center gap-2 mb-1">
                <span class="text-sm text-gray-500">Progress:</span>
                <span class="text-lg font-bold text-yellow-700">67%</span>
            </div>
            <div class="w-40 h-3 bg-gray-200 rounded-full overflow-hidden mb-1">
                <div class="h-3 bg-yellow-400 rounded-full" style="width: 67%"></div>
            </div>
            <div class="flex items-center gap-2 text-xs text-gray-500">
                <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3"/></svg>
                <span>2 days remaining</span>
            </div>
        </div>
    </div>
</div>
<!-- End Conference Progress Section -->
<!-- Task Progress Section -->
<div class="max-w-4xl mx-auto mb-8">
    <div class="bg-white rounded-2xl shadow flex flex-col md:flex-row items-center justify-between p-6 border-l-4 border-green-400">
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center justify-center w-12 h-12 bg-green-100 rounded-full">
                <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2l4-4M7 20h10a2 2 0 002-2V6a2 2 0 00-2-2H7a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </span>
            <div>
                <div class="text-lg font-bold text-green-700">Task Progress</div>
                <div class="text-sm text-gray-500">Completed Tasks</div>
            </div>
        </div>
        <div class="flex flex-col items-end mt-4 md:mt-0 md:ml-8 min-w-[180px]">
            <div class="flex items-center gap-2 mb-1">
                <span class="text-sm text-gray-500">15 / 20</span>
                <span class="text-lg font-bold text-green-700">75%</span>
            </div>
            <div class="w-40 h-3 bg-gray-200 rounded-full overflow-hidden mb-1">
                <div class="h-3 bg-green-400 rounded-full" style="width: 75%"></div>
            </div>
            <div class="flex items-center gap-2 text-xs text-gray-500">
                <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2l4-4"/></svg>
                <span>5 tasks remaining</span>
            </div>
        </div>
    </div>
</div>
<!-- End Task Progress Section -->
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
    <!-- Summary Stats Section -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6 mb-10">
        <!-- Invited -->
        <div class="bg-white rounded-2xl shadow flex flex-col items-center p-5 border-t-4 border-blue-400">
            <span class="inline-flex items-center justify-center w-10 h-10 bg-blue-100 rounded-full mb-2">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </span>
            <div class="text-2xl font-bold text-blue-700">250</div>
            <div class="text-sm text-gray-500">Invited</div>
        </div>
        <!-- Accepted -->
        <div class="bg-white rounded-2xl shadow flex flex-col items-center p-5 border-t-4 border-green-400">
            <span class="inline-flex items-center justify-center w-10 h-10 bg-green-100 rounded-full mb-2">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </span>
            <div class="text-2xl font-bold text-green-700">180</div>
            <div class="text-sm text-gray-500">Accepted</div>
        </div>
        <!-- Flying -->
        <div class="bg-white rounded-2xl shadow flex flex-col items-center p-5 border-t-4 border-yellow-400">
            <span class="inline-flex items-center justify-center w-10 h-10 bg-yellow-100 rounded-full mb-2">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a5 5 0 00-10 0v2a5 5 0 00-2 4v5a2 2 0 002 2h10a2 2 0 002-2v-5a5 5 0 00-2-4z"/></svg>
            </span>
            <div class="text-2xl font-bold text-yellow-700">120</div>
            <div class="text-sm text-gray-500">Flying</div>
        </div>
        <!-- Status Breakdown -->
        <div class="bg-white rounded-2xl shadow flex flex-col items-center p-5 border-t-4 border-pink-400">
            <span class="inline-flex items-center justify-center w-10 h-10 bg-pink-100 rounded-full mb-2">
                <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01"/></svg>
            </span>
            <div class="flex flex-col items-center">
                <div class="text-xs text-gray-500">Pending: <span class="font-bold text-pink-700">40</span></div>
                <div class="text-xs text-gray-500">Approved: <span class="font-bold text-green-700">130</span></div>
                <div class="text-xs text-gray-500">Declined: <span class="font-bold text-red-700">10</span></div>
            </div>
            <div class="text-sm text-gray-500 mt-1">Status</div>
        </div>
        <!-- Speakers -->
        <div class="bg-white rounded-2xl shadow flex flex-col items-center p-5 border-t-4 border-purple-400">
            <span class="inline-flex items-center justify-center w-10 h-10 bg-purple-100 rounded-full mb-2">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a3 3 0 11-6 0 3 3 0 016 0zM17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
            </span>
            <div class="text-2xl font-bold text-purple-700">{{ $selectedConference['speakersCount'] }}</div>
            <div class="text-sm text-gray-500">Speakers</div>
        </div>
    </div>
    <!-- End Summary Stats Section -->
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
