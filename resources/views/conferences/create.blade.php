@extends('layouts.app')

@section('title', 'Add Conference')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl shadow p-8">
    <h2 class="text-2xl font-bold mb-6">Add Conference</h2>
    <form method="POST" action="{{ route('conferences.store') }}" class="space-y-6">
        @csrf
        
        <!-- Basic Conference Information -->
        <div class="bg-gray-50 p-6 rounded-lg">
            <h3 class="text-lg font-semibold mb-4 text-gray-800 border-b border-gray-200 pb-2">Conference Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Title *</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                    @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700">Location *</label>
                    <input type="text" id="location" name="location" value="{{ old('location') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" placeholder="e.g., New York, NY">
                    @error('location')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date *</label>
                    <input type="date" id="start_date" name="start_date" value="{{ old('start_date') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                    @error('start_date')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">End Date *</label>
                    <input type="date" id="end_date" name="end_date" value="{{ old('end_date') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                    @error('end_date')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Venue Selection/Creation -->
        <div class="bg-blue-50 p-6 rounded-lg">
            <h3 class="text-lg font-semibold mb-4 text-blue-800 border-b border-blue-200 pb-2">Venue Information</h3>
            
            <!-- Venue Selection -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-3">
                    <label class="block text-sm font-medium text-gray-700">Select Venue *</label>
                    <button type="button" id="openVenueModalBtn" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create New Venue
                    </button>
                </div>
                
                <!-- Searchable Venue Dropdown -->
                <div class="relative">
                    <div class="relative">
                        <input 
                            type="text" 
                            id="venue_search" 
                            placeholder="Search venues..." 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 pr-16"
                            autocomplete="off"
                        >
                        <button 
                            type="button" 
                            id="clear_venue" 
                            class="absolute right-8 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden"
                            title="Clear selection"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                        <button 
                            type="button" 
                            id="venue_dropdown_toggle" 
                            class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                            title="Show all venues"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Hidden input for form submission -->
                    <input type="hidden" id="venue_id" name="venue_id" value="{{ old('venue_id') }}">
                    
                    <!-- Dropdown -->
                    <div id="venue_dropdown" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto">
                        <div id="venue_options"></div>
                    </div>
                </div>
                @error('venue_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Sessions (Draft and Modal) -->
        <div class="bg-purple-50 p-6 rounded-lg">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-purple-900">Sessions</h3>
                <button type="button" id="openSessionModalBtn" class="inline-flex items-center bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    + Add session
                </button>
            </div>

            <p class="text-sm text-purple-900/70 mb-3">Add one or more sessions. You can edit or remove before creating the conference. Sessions must be within the conference start and end dates. Overlaps are checked per venue.</p>

            <!-- Draft list -->
            <div id="sessionDraftsContainer" class="space-y-3" aria-live="polite"></div>

            <!-- Hidden field to carry drafts as JSON -->
            <input type="hidden" id="sessions_json" name="sessions_json" value='{{ old('sessions_json', '[]') }}'>
        </div>

        <div class="flex justify-end space-x-4 mt-4">
            <a href="{{ route('conferences.index') }}" 
               class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 font-semibold text-lg transition-all duration-200 shadow-sm hover:shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Cancel
            </a>
            <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-3 rounded-lg font-semibold text-lg transition-all duration-200 shadow-sm hover:shadow-md">
                <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create Conference
            </button>
        </div>
    </form>
</div>



<script>
// Venue data for dropdowns
const venues = @json($venues ?? []);

document.addEventListener('DOMContentLoaded', function() {
    const confStartInput = document.getElementById('start_date');
    const confEndInput = document.getElementById('end_date');

    // ===================== Sessions Modal and Drafts =====================
    const sessionsJsonInput = document.getElementById('sessions_json');
    let sessionDrafts = [];

    // Initialize from old input
    try {
        const initial = JSON.parse(sessionsJsonInput.value || '[]');
        if (Array.isArray(initial)) sessionDrafts = initial;
    } catch (e) {
        sessionDrafts = [];
    }

    const draftsContainer = document.getElementById('sessionDraftsContainer');
    const openModalBtn = document.getElementById('openSessionModalBtn');

    // Modal elements will be injected once for reuse
    const modal = document.createElement('div');
    modal.id = 'sessionModal';
    modal.className = 'fixed inset-0 z-50 hidden';
    modal.setAttribute('role', 'dialog');
    modal.setAttribute('aria-modal', 'true');
    modal.innerHTML = `
<div class="flex items-end justify-center min-h-screen text-center sm:block sm:p-0">
  <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" aria-hidden="true"></div>
  <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
  <div class="inline-block align-bottom bg-white rounded-lg px-6 pt-6 pb-5 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
    <div class="flex items-start justify-between mb-4">
      <h3 class="text-xl font-semibold text-gray-900" id="sessionModalTitle">Add session</h3>
      <button type="button" id="closeSessionModalBtn" class="text-gray-400 hover:text-gray-600 focus:outline-none" aria-label="Close">✕</button>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700">Title *</label>
        <input id="session_title" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-600 focus:ring-purple-600">
        <p id="err_title" class="text-red-600 text-xs mt-1 hidden"></p>
      </div>
      <div>
        <div class="flex items-center justify-between mb-1">
          <label class="block text-sm font-medium text-gray-700">Venue *</label>
          <button type="button" id="openSessionVenueModalBtn" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded text-xs font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            New
          </button>
        </div>
        <div class="relative">
          <div class="relative">
            <input 
              type="text" 
              id="session_venue_search" 
              placeholder="Search venues..." 
              class="w-full rounded-md border-gray-300 text-sm focus:ring-purple-600 focus:border-purple-600 pr-16"
              autocomplete="off"
            >
            <button 
              type="button" 
              id="clear_session_venue" 
              class="absolute right-8 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden"
              title="Clear selection"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
            <button 
              type="button" 
              id="session_dropdown_toggle" 
              class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
              title="Show all venues"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
              </svg>
            </button>
          </div>
          <div id="session_venue_dropdown" class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden">
            <div id="session_venue_options">
              <!-- Venue options will be populated here -->
            </div>
          </div>
        </div>
        <input type="hidden" id="session_venue_id" name="session_venue_id" value="">
        <p id="err_venue" class="text-red-600 text-xs mt-1 hidden"></p>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Start time *</label>
        <input id="session_start_time" type="datetime-local" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-600 focus:ring-purple-600">
        <p id="range_hint_start" class="text-xs text-gray-500 mt-1"></p>
        <p id="err_start" class="text-red-600 text-xs mt-1 hidden"></p>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">End time *</label>
        <input id="session_end_time" type="datetime-local" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-600 focus:ring-purple-600">
        <p id="range_hint_end" class="text-xs text-gray-500 mt-1"></p>
        <p id="err_end" class="text-red-600 text-xs mt-1 hidden"></p>
      </div>
      <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Description</label>
        <textarea id="session_description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-600 focus:ring-purple-600"></textarea>
      </div>
    </div>
    <div class="mt-6 flex items-center justify-end space-x-3">
      <button type="button" id="cancelSessionBtn" class="px-4 py-2 rounded-md border bg-white text-gray-700 hover:bg-gray-50">Cancel</button>
      <button type="button" id="saveSessionBtn" class="px-5 py-2 rounded-md bg-purple-600 text-white hover:bg-purple-700 font-medium">Save session</button>
    </div>
  </div>
`;
    document.body.appendChild(modal);

    let editingIndex = null;

    function getConferenceBounds() {
        const startDate = confStartInput.value;
        const endDate = confEndInput.value;
        if (!startDate || !endDate) return null;
        const start = new Date(startDate + 'T00:00');
        const end = new Date(endDate + 'T23:59');
        return { start, end, text: `${startDate} 00:00 → ${endDate} 23:59` };
    }

    function isSessionWithinBounds(session, bounds) {
        if (!bounds) return true;
        const s = session.start_time ? new Date(session.start_time) : null;
        const e = session.end_time ? new Date(session.end_time) : null;
        if (!s || !e) return false;
        return s >= bounds.start && e <= bounds.end;
    }

    function doIntervalsOverlap(aStart, aEnd, bStart, bEnd) {
        return aStart < bEnd && bStart < aEnd; // strict overlap; touching endpoints allowed
    }

    function getOverlapIndices(target, excludeIndex = null) {
        const overlaps = [];
        if (!target.start_time || !target.end_time) return overlaps;
        const tStart = new Date(target.start_time);
        const tEnd = new Date(target.end_time);
        sessionDrafts.forEach((s, idx) => {
            if (excludeIndex !== null && idx === excludeIndex) return;
            if (!s.start_time || !s.end_time) return;
            if (String(s.venue_id || '') !== String(target.venue_id || '')) return;
            const sStart = new Date(s.start_time);
            const sEnd = new Date(s.end_time);
            if (doIntervalsOverlap(tStart, tEnd, sStart, sEnd)) overlaps.push(idx);
        });
        return overlaps;
    }

    function renderDrafts() {
        draftsContainer.innerHTML = '';
        if (!sessionDrafts.length) {
            const empty = document.createElement('div');
            empty.className = 'text-sm text-purple-900/60';
            empty.textContent = 'No sessions added yet.';
            draftsContainer.appendChild(empty);
            sessionsJsonInput.value = JSON.stringify([]);
            return;
        }

        const bounds = getConferenceBounds();
        sessionDrafts.forEach((s, idx) => {
            const item = document.createElement('div');
            item.className = 'border border-purple-200 rounded-lg p-4 bg-white shadow-sm';
            const outOfRange = !isSessionWithinBounds(s, bounds);
            const conflicts = getOverlapIndices(s, idx);
            item.innerHTML = `
  <div class="flex items-start justify-between">
    <div>
      <div class="flex items-center gap-2">
        <div class="font-medium text-gray-900">${s.title || '(Untitled session)'}</div>
        ${outOfRange ? '<span class="text-xs px-2 py-0.5 rounded bg-yellow-100 text-yellow-800">Out of range</span>' : ''}
        ${conflicts.length ? `<span class=\"text-xs px-2 py-0.5 rounded bg-red-100 text-red-800\">Overlaps</span>` : ''}
      </div>
      <div class="text-sm text-gray-600">${s.start_time || ''} → ${s.end_time || ''}</div>
      ${bounds ? `<div class=\"text-xs text-gray-500\">Conference window: ${bounds.text}</div>` : ''}
      <div class="text-xs text-gray-500">Venue ID: ${s.venue_id || '-'}</div>
      ${s.description ? `<div class="text-sm text-gray-700 mt-1">${s.description}</div>` : ''}
    </div>
    <div class="flex items-center gap-2">
      <button type="button" data-action="edit" data-index="${idx}" class="px-3 py-1 text-sm rounded-md border text-gray-700 hover:bg-gray-50">Edit</button>
      <button type="button" data-action="remove" data-index="${idx}" class="px-3 py-1 text-sm rounded-md bg-red-600 text-white hover:bg-red-700">Remove</button>
    </div>
  </div>`;
            draftsContainer.appendChild(item);
        });

        sessionsJsonInput.value = JSON.stringify(sessionDrafts);
    }

    function openModal(editIndex = null) {
        editingIndex = editIndex;
        const isEditing = editIndex !== null;
        document.getElementById('sessionModalTitle').textContent = isEditing ? 'Edit session' : 'Add session';
        const data = isEditing ? sessionDrafts[editIndex] : {};
        document.getElementById('session_title').value = data.title || '';
        document.getElementById('session_venue_id').value = data.venue_id || '';
        const startInput = document.getElementById('session_start_time');
        const endInput = document.getElementById('session_end_time');
        const hintStart = document.getElementById('range_hint_start');
        const hintEnd = document.getElementById('range_hint_end');
        startInput.value = data.start_time || '';
        endInput.value = data.end_time || '';
        const bounds = getConferenceBounds();
        if (bounds) {
            const minStr = confStartInput.value + 'T00:00';
            const maxStr = confEndInput.value + 'T23:59';
            startInput.min = minStr; startInput.max = maxStr;
            endInput.min = minStr; endInput.max = maxStr;
            if (hintStart) hintStart.textContent = `Allowed: ${minStr} to ${maxStr}`;
            if (hintEnd) hintEnd.textContent = `Allowed: ${minStr} to ${maxStr}`;
        } else {
            startInput.removeAttribute('min'); startInput.removeAttribute('max');
            endInput.removeAttribute('min'); endInput.removeAttribute('max');
            if (hintStart) hintStart.textContent = '';
            if (hintEnd) hintEnd.textContent = '';
        }
        document.getElementById('session_description').value = data.description || '';
        clearErrors();
        modal.classList.remove('hidden');
        setTimeout(() => document.getElementById('session_title').focus(), 0);
    }

    function closeModal() {
        modal.classList.add('hidden');
    }

    function clearErrors() {
        ['err_title','err_venue','err_start','err_end'].forEach(id => {
            const el = document.getElementById(id);
            el.textContent = '';
            el.classList.add('hidden');
        });
    }

    function validateModal() {
        clearErrors();
        const title = document.getElementById('session_title').value.trim();
        const venueId = document.getElementById('session_venue_id').value;
        const start = document.getElementById('session_start_time').value;
        const end = document.getElementById('session_end_time').value;
        let ok = true;
        if (!title) { const e = document.getElementById('err_title'); e.textContent = 'Title is required.'; e.classList.remove('hidden'); ok = false; }
        if (!venueId) { const e = document.getElementById('err_venue'); e.textContent = 'Venue is required.'; e.classList.remove('hidden'); ok = false; }
        if (!start) { const e = document.getElementById('err_start'); e.textContent = 'Start time is required.'; e.classList.remove('hidden'); ok = false; }
        if (!end) { const e = document.getElementById('err_end'); e.textContent = 'End time is required.'; e.classList.remove('hidden'); ok = false; }
        if (start && end && new Date(end) <= new Date(start)) { const e = document.getElementById('err_end'); e.textContent = 'End time must be after start time.'; e.classList.remove('hidden'); ok = false; }
        const bounds = getConferenceBounds();
        if (bounds && start && end) {
            const s = new Date(start); const e = new Date(end);
            if (s < bounds.start || e > bounds.end) {
                const err = document.getElementById('err_end');
                err.textContent = `Session must be within conference dates (${confStartInput.value} 00:00 → ${confEndInput.value} 23:59).`;
                err.classList.remove('hidden');
                ok = false;
            }
        }
        // Overlap check
        if (start && end) {
            const venueId = document.getElementById('session_venue_id').value;
            const candidate = { start_time: start, end_time: end, venue_id: venueId };
            const overlaps = getOverlapIndices(candidate, editingIndex);
            if (overlaps.length) {
                const first = sessionDrafts[overlaps[0]];
                const label = first?.title || `Session ${overlaps[0] + 1}`;
                const err = document.getElementById('err_end');
                err.textContent = `Overlaps at the same venue with "${label}" (${first?.start_time || ''} → ${first?.end_time || ''})${overlaps.length > 1 ? ` and ${overlaps.length - 1} more` : ''}.`;
                err.classList.remove('hidden');
                ok = false;
            }
        }
        return ok;
    }

    function saveFromModal() {
        if (!validateModal()) return;
        const item = {
            title: document.getElementById('session_title').value.trim(),
            venue_id: document.getElementById('session_venue_id').value,
            start_time: document.getElementById('session_start_time').value,
            end_time: document.getElementById('session_end_time').value,
            description: document.getElementById('session_description').value.trim(),
        };
        if (editingIndex !== null) {
            sessionDrafts[editingIndex] = item;
        } else {
            sessionDrafts.push(item);
        }
        renderDrafts();
        closeModal();
    }

    // Event listeners
    openModalBtn.addEventListener('click', () => openModal(null));
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });
    document.getElementById('closeSessionModalBtn').addEventListener('click', closeModal);
    document.getElementById('cancelSessionBtn').addEventListener('click', closeModal);
    document.getElementById('saveSessionBtn').addEventListener('click', saveFromModal);

    draftsContainer.addEventListener('click', (e) => {
        const btn = e.target.closest('button');
        if (!btn) return;
        const idx = parseInt(btn.getAttribute('data-index'));
        const action = btn.getAttribute('data-action');
        if (Number.isNaN(idx)) return;
        if (action === 'edit') {
            openModal(idx);
        } else if (action === 'remove') {
            sessionDrafts.splice(idx, 1);
            renderDrafts();
        }
    });

    // Keyboard accessibility: ESC closes modal
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
    });

    // React to conference date changes
    [confStartInput, confEndInput].forEach(el => {
        el.addEventListener('change', () => { renderDrafts(); if (!modal.classList.contains('hidden')) openModal(editingIndex); });
        el.addEventListener('input', () => { renderDrafts(); if (!modal.classList.contains('hidden')) openModal(editingIndex); });
    });

    // Initial render
    renderDrafts();

    // ===================== Venue Creation Modal =====================
    const venueModal = document.createElement('div');
    venueModal.id = 'venueModal';
    venueModal.className = 'fixed inset-0 z-50 hidden';
    venueModal.setAttribute('role', 'dialog');
    venueModal.setAttribute('aria-modal', 'true');
    venueModal.innerHTML = `
<div class="flex items-end justify-center min-h-screen text-center sm:block sm:p-0">
  <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" aria-hidden="true"></div>
  <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
  <div class="inline-block align-bottom bg-white rounded-lg px-6 pt-6 pb-5 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
    <div class="flex items-start justify-between mb-4">
      <h3 class="text-xl font-semibold text-gray-900" id="venueModalTitle">Create New Venue</h3>
      <button type="button" id="closeVenueModalBtn" class="text-gray-400 hover:text-gray-600 focus:outline-none" aria-label="Close">✕</button>
    </div>
    <form id="venueForm">
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Venue Name *</label>
          <input id="modal_venue_name" type="text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="e.g., Convention Center">
          <p id="err_venue_name" class="text-red-600 text-xs mt-1 hidden"></p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Capacity *</label>
          <input id="modal_venue_capacity" type="number" required min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="e.g., 500">
          <p id="err_venue_capacity" class="text-red-600 text-xs mt-1 hidden"></p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Address *</label>
          <textarea id="modal_venue_address" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="Full address of the venue"></textarea>
          <p id="err_venue_address" class="text-red-600 text-xs mt-1 hidden"></p>
        </div>
      </div>
      <div class="mt-6 flex items-center justify-end space-x-3">
        <button type="button" id="cancelVenueBtn" class="px-4 py-2 rounded-md border bg-white text-gray-700 hover:bg-gray-50">Cancel</button>
        <button type="submit" id="saveVenueBtn" class="px-5 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700 font-medium">Create Venue</button>
      </div>
    </form>
  </div>
</div>
`;
    document.body.appendChild(venueModal);

    const openVenueModalBtn = document.getElementById('openVenueModalBtn');
    const venueIdSelect = document.getElementById('venue_id');

    function openVenueModal() {
        // Clear form
        document.getElementById('modal_venue_name').value = '';
        document.getElementById('modal_venue_capacity').value = '';
        document.getElementById('modal_venue_address').value = '';
        clearVenueErrors();
        venueModal.classList.remove('hidden');
        setTimeout(() => document.getElementById('modal_venue_name').focus(), 0);
    }

    function closeVenueModal() {
        venueModal.classList.add('hidden');
    }

    function clearVenueErrors() {
        ['err_venue_name', 'err_venue_capacity', 'err_venue_address'].forEach(id => {
            const el = document.getElementById(id);
            el.textContent = '';
            el.classList.add('hidden');
        });
    }

    function validateVenueForm() {
        clearVenueErrors();
        const name = document.getElementById('modal_venue_name').value.trim();
        const capacity = document.getElementById('modal_venue_capacity').value;
        const address = document.getElementById('modal_venue_address').value.trim();
        let ok = true;

        if (!name) {
            const e = document.getElementById('err_venue_name');
            e.textContent = 'Venue name is required.';
            e.classList.remove('hidden');
            ok = false;
        }

        if (!capacity || capacity < 1) {
            const e = document.getElementById('err_venue_capacity');
            e.textContent = 'Capacity must be at least 1.';
            e.classList.remove('hidden');
            ok = false;
        }

        if (!address) {
            const e = document.getElementById('err_venue_address');
            e.textContent = 'Address is required.';
            e.classList.remove('hidden');
            ok = false;
        }

        return ok;
    }

    function createVenue() {
        if (!validateVenueForm()) return;

        const formData = {
            name: document.getElementById('modal_venue_name').value.trim(),
            capacity: document.getElementById('modal_venue_capacity').value,
            address: document.getElementById('modal_venue_address').value.trim(),
            _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        };

        // Show loading state
        const saveBtn = document.getElementById('saveVenueBtn');
        const originalText = saveBtn.textContent;
        saveBtn.disabled = true;
        saveBtn.textContent = 'Creating...';

        // Send AJAX request to create venue
        fetch('{{ route("venues.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': formData._token,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(formData)
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            console.log('Content-Type:', contentType);
            
            if (!contentType || !contentType.includes('application/json')) {
                // Try to get the response text for debugging
                return response.text().then(text => {
                    console.log('Non-JSON response body:', text);
                    throw new Error('Server returned non-JSON response. Status: ' + response.status + '. Body: ' + text.substring(0, 200));
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Add new venue to main conference venue dropdown
                const venueSearch = document.getElementById('venue_search');
                const venueIdInput = document.getElementById('venue_id');
                if (venueSearch && venueIdInput) {
                    venueSearch.value = `${data.venue.name} - ${data.venue.address}`;
                    venueIdInput.value = data.venue.id;
                    document.getElementById('clear_venue').classList.remove('hidden');
                }

                // Add new venue to session venue dropdown
                const sessionVenueSearch = document.getElementById('session_venue_search');
                const sessionVenueIdInput = document.getElementById('session_venue_id');
                if (sessionVenueSearch && sessionVenueIdInput) {
                    sessionVenueSearch.value = `${data.venue.name} - ${data.venue.address}`;
                    sessionVenueIdInput.value = data.venue.id;
                }

                // Update venues array for both dropdowns
                venues.push(data.venue);

                // Close modal
                closeVenueModal();

                // Show success message
                alert('Venue created successfully and selected!');
            } else {
                throw new Error(data.message || 'Failed to create venue');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error creating venue: ' + error.message);
        })
        .finally(() => {
            // Reset button
            saveBtn.disabled = false;
            saveBtn.textContent = originalText;
        });
    }

    // Event listeners for venue modal
    openVenueModalBtn.addEventListener('click', openVenueModal);
    venueModal.addEventListener('click', (e) => {
        if (e.target === venueModal) closeVenueModal();
    });
    document.getElementById('closeVenueModalBtn').addEventListener('click', closeVenueModal);
    document.getElementById('cancelVenueBtn').addEventListener('click', closeVenueModal);
    document.getElementById('venueForm').addEventListener('submit', (e) => {
        e.preventDefault();
        createVenue();
    });

    // Keyboard accessibility: ESC closes modal
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !venueModal.classList.contains('hidden')) closeVenueModal();
    });

    // ===================== Main Conference Venue Searchable Dropdown =====================
    function initializeMainVenueDropdown() {
        const venueSearch = document.getElementById('venue_search');
        const venueDropdown = document.getElementById('venue_dropdown');
        const venueOptions = document.getElementById('venue_options');
        const venueIdInput = document.getElementById('venue_id');
        const clearVenueBtn = document.getElementById('clear_venue');
        const venueDropdownToggle = document.getElementById('venue_dropdown_toggle');
        
        let selectedVenue = null;
        let filteredVenues = [];
        let isDropdownOpen = false;
        
        function filterVenues(query) {
            if (!query.trim()) {
                return venues;
            }
            const lowerQuery = query.toLowerCase();
            return venues.filter(venue => 
                venue.name.toLowerCase().includes(lowerQuery) ||
                venue.address.toLowerCase().includes(lowerQuery)
            );
        }
        
        function renderVenueOptions(venues) {
            venueOptions.innerHTML = '';
            
            if (venues.length === 0) {
                venueOptions.innerHTML = `
                    <div class="px-4 py-2 text-sm text-gray-500">
                        No venues found
                    </div>
                `;
                return;
            }
            
            venues.forEach(venue => {
                const option = document.createElement('div');
                option.className = 'px-4 py-2 text-sm cursor-pointer hover:bg-yellow-50 transition-colors duration-150';
                option.textContent = `${venue.name} - ${venue.address}`;
                option.dataset.id = venue.id;
                option.dataset.name = venue.name;
                
                option.addEventListener('click', () => {
                    selectVenue(venue);
                });
                
                venueOptions.appendChild(option);
            });
        }
        
        function selectVenue(venue) {
            selectedVenue = venue;
            venueSearch.value = `${venue.name} - ${venue.address}`;
            venueIdInput.value = venue.id;
            clearVenueBtn.classList.remove('hidden');
            venueDropdown.classList.add('hidden');
            isDropdownOpen = false;
        }
        
        function clearVenueSelection() {
            selectedVenue = null;
            venueSearch.value = '';
            venueIdInput.value = '';
            clearVenueBtn.classList.add('hidden');
            venueDropdown.classList.add('hidden');
            isDropdownOpen = false;
        }
        
        function toggleVenueDropdown() {
            if (isDropdownOpen) {
                venueDropdown.classList.add('hidden');
                isDropdownOpen = false;
            } else {
                filteredVenues = filterVenues(venueSearch.value);
                renderVenueOptions(filteredVenues);
                venueDropdown.classList.remove('hidden');
                isDropdownOpen = true;
            }
        }
        
        // Event listeners
        venueSearch.addEventListener('input', (e) => {
            const query = e.target.value;
            filteredVenues = filterVenues(query);
            renderVenueOptions(filteredVenues);
            venueDropdown.classList.remove('hidden');
            isDropdownOpen = true;
        });
        
        venueSearch.addEventListener('focus', () => {
            if (!isDropdownOpen) {
                filteredVenues = filterVenues(venueSearch.value);
                renderVenueOptions(filteredVenues);
                venueDropdown.classList.remove('hidden');
                isDropdownOpen = true;
            }
        });
        
        clearVenueBtn.addEventListener('click', clearVenueSelection);
        venueDropdownToggle.addEventListener('click', toggleVenueDropdown);
        
        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!venueSearch.contains(e.target) && !venueDropdown.contains(e.target)) {
                venueDropdown.classList.add('hidden');
                isDropdownOpen = false;
            }
        });
        
        // Initialize with existing selection if any
        const existingVenueId = venueIdInput.value;
        if (existingVenueId) {
            const existingVenue = venues.find(v => v.id == existingVenueId);
            if (existingVenue) {
                selectVenue(existingVenue);
            }
        }
    }

    // ===================== Session Venue Searchable Dropdown =====================
    function initializeSessionVenueDropdown() {
        const sessionVenueSearch = document.getElementById('session_venue_search');
        const sessionVenueDropdown = document.getElementById('session_venue_dropdown');
        const sessionVenueOptions = document.getElementById('session_venue_options');
        const sessionVenueIdInput = document.getElementById('session_venue_id');
        const clearSessionVenueBtn = document.getElementById('clear_session_venue');
        const sessionDropdownToggle = document.getElementById('session_dropdown_toggle');
        const openSessionVenueModalBtn = document.getElementById('openSessionVenueModalBtn');
        
        let selectedSessionVenue = null;
        let filteredSessionVenues = [];
        let isSessionDropdownOpen = false;
        
        function filterSessionVenues(query) {
            if (!query.trim()) {
                return venues;
            }
            const lowerQuery = query.toLowerCase();
            return venues.filter(venue => 
                venue.name.toLowerCase().includes(lowerQuery)
            );
        }
        
        function renderSessionVenueOptions(venues) {
            sessionVenueOptions.innerHTML = '';
            
            if (venues.length === 0) {
                sessionVenueOptions.innerHTML = `
                    <div class="px-4 py-2 text-sm text-gray-500">
                        No venues found
                    </div>
                `;
                return;
            }
            
            venues.forEach(venue => {
                const option = document.createElement('div');
                option.className = 'px-4 py-2 text-sm cursor-pointer hover:bg-purple-50 transition-colors duration-150';
                option.textContent = `${venue.name} - ${venue.address}`;
                option.dataset.id = venue.id;
                option.dataset.name = venue.name;
                
                option.addEventListener('click', () => {
                    selectSessionVenue(venue);
                });
                
                sessionVenueOptions.appendChild(option);
            });
        }
        
        function selectSessionVenue(venue) {
            selectedSessionVenue = venue;
            sessionVenueSearch.value = `${venue.name} - ${venue.address}`;
            sessionVenueIdInput.value = venue.id;
            clearSessionVenueBtn.classList.remove('hidden');
            sessionVenueDropdown.classList.add('hidden');
            isSessionDropdownOpen = false;
        }
        
        function clearSessionVenueSelection() {
            selectedSessionVenue = null;
            sessionVenueSearch.value = '';
            sessionVenueIdInput.value = '';
            clearSessionVenueBtn.classList.add('hidden');
            sessionVenueDropdown.classList.add('hidden');
            isSessionDropdownOpen = false;
        }
        
        function toggleSessionVenueDropdown() {
            if (isSessionDropdownOpen) {
                sessionVenueDropdown.classList.add('hidden');
                isSessionDropdownOpen = false;
            } else {
                const query = sessionVenueSearch.value.trim();
                if (query) {
                    // Show filtered results
                    filteredSessionVenues = filterSessionVenues(query);
                    renderSessionVenueOptions(filteredSessionVenues);
                } else {
                    // Show all venues
                    renderSessionVenueOptions(venues);
                }
                sessionVenueDropdown.classList.remove('hidden');
                isSessionDropdownOpen = true;
            }
        }
        
        // Event listeners
        if (sessionVenueSearch) {
            sessionVenueSearch.addEventListener('input', function() {
                const query = this.value;
                filteredSessionVenues = filterSessionVenues(query);
                
                if (query.trim() && filteredSessionVenues.length > 0) {
                    renderSessionVenueOptions(filteredSessionVenues);
                    sessionVenueDropdown.classList.remove('hidden');
                    isSessionDropdownOpen = true;
                } else if (query.trim() && filteredSessionVenues.length === 0) {
                    renderSessionVenueOptions([]);
                    sessionVenueDropdown.classList.remove('hidden');
                    isSessionDropdownOpen = true;
                } else {
                    sessionVenueDropdown.classList.add('hidden');
                    isSessionDropdownOpen = false;
                }
            });
            
            sessionVenueSearch.addEventListener('focus', function() {
                if (this.value.trim()) {
                    filteredSessionVenues = filterSessionVenues(this.value);
                    renderSessionVenueOptions(filteredSessionVenues);
                    sessionVenueDropdown.classList.remove('hidden');
                    isSessionDropdownOpen = true;
                }
            });
            
            sessionVenueSearch.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    sessionVenueDropdown.classList.add('hidden');
                    isSessionDropdownOpen = false;
                }
            });
        }
        
        if (clearSessionVenueBtn) {
            clearSessionVenueBtn.addEventListener('click', clearSessionVenueSelection);
        }
        
        if (sessionDropdownToggle) {
            sessionDropdownToggle.addEventListener('click', toggleSessionVenueDropdown);
        }
        
        if (openSessionVenueModalBtn) {
            openSessionVenueModalBtn.addEventListener('click', function() {
                // Open the main venue creation modal
                openVenueModal();
            });
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.relative') || e.target.closest('#session_venue_dropdown')) {
                sessionVenueDropdown.classList.add('hidden');
                isSessionDropdownOpen = false;
            }
        });
    }

    // Initialize all dropdowns
    initializeMainVenueDropdown();
    initializeSessionVenueDropdown();
});
</script>
@endsection 