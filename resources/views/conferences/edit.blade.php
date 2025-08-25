@extends('layouts.app')

@section('title', 'Edit Conference')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl shadow p-8">
    <h2 class="text-2xl font-bold mb-6">Edit Conference</h2>
    <form method="POST" action="{{ route('conferences.update', $conference) }}" class="space-y-6">
        @csrf
        @method('PUT')
        
        <!-- Basic Conference Information -->
        <div class="bg-gray-50 p-6 rounded-lg">
            <h3 class="text-lg font-semibold mb-4 text-gray-800 border-b border-gray-200 pb-2">Conference Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Title *</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $conference->name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                    @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700">Location *</label>
                    <input type="text" id="location" name="location" value="{{ old('location', $conference->location) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" placeholder="e.g., New York, NY">
                    @error('location')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date *</label>
                    <input type="date" id="start_date" name="start_date" value="{{ old('start_date', $conference->start_date) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                    @error('start_date')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">End Date *</label>
                    <input type="date" id="end_date" name="end_date" value="{{ old('end_date', $conference->end_date) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                    @error('end_date')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Venue Selection/Creation -->
        <div class="bg-blue-50 p-6 rounded-lg">
            <h3 class="text-lg font-semibold mb-4 text-blue-800 border-b border-blue-200 pb-2">Venue Information</h3>
            
            <!-- Venue Type Selection -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Venue Selection *</label>
                <div class="flex items-center space-x-6">
                    <label class="inline-flex items-center">
                        <input type="radio" name="venue_type" value="existing" id="venue_existing" class="form-radio text-yellow-600" {{ old('venue_type', 'existing') == 'existing' ? 'checked' : '' }}>
                        <span class="ml-2">Select Existing Venue</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="venue_type" value="new" id="venue_new" class="form-radio text-yellow-600" {{ old('venue_type') == 'new' ? 'checked' : '' }}>
                        <span class="ml-2">Create New Venue</span>
                    </label>
                </div>
                @error('venue_type')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Existing Venue Selection -->
            <div id="existing_venue_section" class="venue-section">
                <div>
                    <label for="venue_id" class="block text-sm font-medium text-gray-700">Select Venue *</label>
                    <select id="venue_id" name="venue_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                        <option value="">Select Venue</option>
                        @foreach($venues as $venue)
                            <option value="{{ $venue->id }}" {{ old('venue_id', $conference->venue_id) == $venue->id ? 'selected' : '' }}>{{ $venue->name }} - {{ $venue->address }}</option>
                        @endforeach
                    </select>
                    @error('venue_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- New Venue Creation -->
            <div id="new_venue_section" class="venue-section" style="display: none;">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="venue_name" class="block text-sm font-medium text-gray-700">Venue Name *</label>
                        <input type="text" id="venue_name" name="venue_name" value="{{ old('venue_name') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" placeholder="e.g., Convention Center">
                        @error('venue_name')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="venue_capacity" class="block text-sm font-medium text-gray-700">Capacity *</label>
                        <input type="number" id="venue_capacity" name="venue_capacity" value="{{ old('venue_capacity') }}" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" placeholder="e.g., 500">
                        @error('venue_capacity')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="mt-4">
                    <label for="venue_address" class="block text-sm font-medium text-gray-700">Address *</label>
                    <textarea id="venue_address" name="venue_address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" placeholder="Full address of the venue">{{ old('venue_address') }}</textarea>
                    @error('venue_address')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
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

            <p class="text-sm text-purple-900/70 mb-3">Manage this conference's sessions. Sessions must be within the conference start and end dates. Overlaps are checked per venue.</p>

            <!-- Draft list -->
            <div id="sessionDraftsContainer" class="space-y-3" aria-live="polite"></div>

            <!-- Hidden field to carry drafts as JSON -->
            <input type="hidden" id="sessions_json" name="sessions_json" value='{{ old('sessions_json') ?? '' }}'>
        </div>

        <div class="flex justify-end">
            <a href="{{ route('conferences.index') }}" class="mr-4 text-gray-600 hover:text-gray-900">Cancel</a>
            <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-3 rounded-lg font-semibold text-lg">Update Conference</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const venueExisting = document.getElementById('venue_existing');
    const venueNew = document.getElementById('venue_new');
    const existingSection = document.getElementById('existing_venue_section');
    const newSection = document.getElementById('new_venue_section');
    const venueIdSelect = document.getElementById('venue_id');
    const venueNameInput = document.getElementById('venue_name');
    const venueCapacityInput = document.getElementById('venue_capacity');
    const venueAddressInput = document.getElementById('venue_address');

    function toggleVenueSections() {
        if (venueExisting.checked) {
            existingSection.style.display = 'block';
            newSection.style.display = 'none';
            // Clear new venue fields
            venueNameInput.value = '';
            venueCapacityInput.value = '';
            venueAddressInput.value = '';
            // Make existing venue required
            venueIdSelect.required = true;
            venueNameInput.required = false;
            venueCapacityInput.required = false;
            venueAddressInput.required = false;
        } else if (venueNew.checked) {
            existingSection.style.display = 'none';
            newSection.style.display = 'block';
            // Clear existing venue selection
            venueIdSelect.value = '';
            // Make new venue fields required
            venueIdSelect.required = false;
            venueNameInput.required = true;
            venueCapacityInput.required = true;
            venueAddressInput.required = true;
        }
    }

    // Initial state
    toggleVenueSections();

    // Listen for changes
    venueExisting.addEventListener('change', toggleVenueSections);
    venueNew.addEventListener('change', toggleVenueSections);

    // ===================== Sessions Modal and Drafts (Edit) =====================
    const sessionsJsonInput = document.getElementById('sessions_json');
    let sessionDrafts = [];

    // Preload from old input or from conference sessions
    (function preloadDrafts() {
        const oldVal = sessionsJsonInput.value;
        if (oldVal) {
            try { const arr = JSON.parse(oldVal); if (Array.isArray(arr)) sessionDrafts = arr; } catch (e) {}
        }
        if (!sessionDrafts.length) {
            sessionDrafts = [
                @foreach(($conference->sessions ?? []) as $s)
                    {
                        id: {{ $s->id }},
                        title: @json($s->title),
                        description: @json($s->description),
                        start_time: @json($s->start_time),
                        end_time: @json($s->end_time),
                        venue_id: @json($s->venue_id),
                        seating_arrangement: @json($s->seating_arrangement),
                        participants_count: {{ $s->participants_count ?? 0 }},
                        deleted: false,
                    },
                @endforeach
            ];
        }
    })();

    const draftsContainer = document.getElementById('sessionDraftsContainer');
    const openModalBtn = document.getElementById('openSessionModalBtn');

    // Modal setup
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
        <label class="block text-sm font-medium text-gray-700">Venue *</label>
        <select id="session_venue_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-600 focus:ring-purple-600">
          <option value="">Select Venue</option>
          @foreach($venues as $venue)
            <option value="{{ $venue->id }}">{{ $venue->name }} - {{ $venue->address }}</option>
          @endforeach
        </select>
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
      <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Seating arrangement</label>
        <textarea id="session_seating" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-600 focus:ring-purple-600" placeholder="Optional JSON or notes"></textarea>
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
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
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

    function doIntervalsOverlap(aStart, aEnd, bStart, bEnd) { return aStart < bEnd && bStart < aEnd; }
    function getOverlapIndices(target, excludeIndex = null) {
        const overlaps = [];
        if (!target.start_time || !target.end_time) return overlaps;
        const tStart = new Date(target.start_time);
        const tEnd = new Date(target.end_time);
        sessionDrafts.forEach((s, idx) => {
            if (excludeIndex !== null && idx === excludeIndex) return;
            if (s.deleted) return;
            if (!s.start_time || !s.end_time) return;
            if (String(s.venue_id || '') !== String(target.venue_id || '')) return;
            const sStart = new Date(s.start_time);
            const sEnd = new Date(s.end_time);
            if (doIntervalsOverlap(tStart, tEnd, sStart, sEnd)) overlaps.push(idx);
        });
        return overlaps;
    }

    function buildPayload() {
        // Convert drafts to sessions_json items
        const items = [];
        sessionDrafts.forEach(d => {
            if (d.id) {
                if (d.deleted) {
                    items.push({ id: d.id, _action: 'delete' });
                } else {
                    items.push({ id: d.id, title: d.title, description: d.description || null, start_time: d.start_time, end_time: d.end_time, venue_id: d.venue_id, seating_arrangement: d.seating_arrangement || null, _action: 'update' });
                }
            } else if (!d.deleted) {
                items.push({ title: d.title, description: d.description || null, start_time: d.start_time, end_time: d.end_time, venue_id: d.venue_id, seating_arrangement: d.seating_arrangement || null, _action: 'create' });
            }
        });
        sessionsJsonInput.value = JSON.stringify(items);
    }

    function renderDrafts() {
        draftsContainer.innerHTML = '';
        if (!sessionDrafts.length) {
            const empty = document.createElement('div');
            empty.className = 'text-sm text-purple-900/60';
            empty.textContent = 'No sessions added yet.';
            draftsContainer.appendChild(empty);
            buildPayload();
            return;
        }

        const bounds = getConferenceBounds();
        sessionDrafts.forEach((s, idx) => {
            const item = document.createElement('div');
            item.className = 'border border-purple-200 rounded-lg p-4 bg-white shadow-sm';
            const outOfRange = !s.deleted && !isSessionWithinBounds(s, bounds);
            const conflicts = !s.deleted ? getOverlapIndices(s, idx) : [];
            item.innerHTML = `
  <div class="flex items-start justify-between">
    <div>
      <div class="flex items-center gap-2">
        <div class="font-medium text-gray-900">${s.title || '(Untitled session)'}</div>
        ${s.deleted ? '<span class="text-xs px-2 py-0.5 rounded bg-gray-200 text-gray-700">Will delete</span>' : ''}
        ${outOfRange ? '<span class="text-xs px-2 py-0.5 rounded bg-yellow-100 text-yellow-800">Out of range</span>' : ''}
        ${conflicts.length ? `<span class=\"text-xs px-2 py-0.5 rounded bg-red-100 text-red-800\" title=\"This session overlaps with another at the same venue.\">Overlaps</span>` : ''}
      </div>
      <div class="text-sm text-gray-600">${s.start_time || ''} → ${s.end_time || ''}</div>
      ${bounds ? `<div class=\"text-xs text-gray-500\">Conference window: ${bounds.text}</div>` : ''}
      <div class="text-xs text-gray-500">Venue ID: ${s.venue_id || '-'}</div>
      ${s.description ? `<div class="text-sm text-gray-700 mt-1">${s.description}</div>` : ''}
      ${s.participants_count ? `<div class=\"text-xs text-gray-500\">Participants: ${s.participants_count}</div>` : ''}
    </div>
    <div class="flex items-center gap-2">
      ${s.deleted ? `<button type="button" data-action="undo" data-index="${idx}" class="px-3 py-1 text-sm rounded-md border text-gray-700 hover:bg-gray-50">Undo</button>` : `<button type=\"button\" data-action=\"edit\" data-index=\"${idx}\" class=\"px-3 py-1 text-sm rounded-md border text-gray-700 hover:bg-gray-50\">Edit</button>`}
      <button type="button" data-action="remove" data-index="${idx}" class="px-3 py-1 text-sm rounded-md ${s.deleted ? 'bg-gray-400' : 'bg-red-600'} text-white hover:${s.deleted ? 'bg-gray-500' : 'bg-red-700'}">${s.deleted ? 'Remove' : 'Delete'}</button>
    </div>
  </div>`;
            draftsContainer.appendChild(item);
        });

        buildPayload();
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
            const minStr = document.getElementById('start_date').value + 'T00:00';
            const maxStr = document.getElementById('end_date').value + 'T23:59';
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
        document.getElementById('session_seating').value = data.seating_arrangement || '';
        clearErrors();
        modal.classList.remove('hidden');
        setTimeout(() => document.getElementById('session_title').focus(), 0);
    }

    function closeModal() { modal.classList.add('hidden'); }
    function clearErrors() { ['err_title','err_venue','err_start','err_end'].forEach(id => { const el = document.getElementById(id); el.textContent=''; el.classList.add('hidden'); }); }

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
                err.textContent = `Session must be within conference dates (${document.getElementById('start_date').value} 00:00 → ${document.getElementById('end_date').value} 23:59).`;
                err.classList.remove('hidden');
                ok = false;
            }
        }
        if (start && end) {
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
            seating_arrangement: document.getElementById('session_seating').value.trim(),
        };
        if (editingIndex !== null) {
            const preserved = sessionDrafts[editingIndex];
            sessionDrafts[editingIndex] = Object.assign({}, preserved, item, { deleted: false });
        } else {
            sessionDrafts.push(item);
        }
        renderDrafts();
        closeModal();
    }

    // Event listeners
    openModalBtn.addEventListener('click', () => openModal(null));
    modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
    document.getElementById('closeSessionModalBtn').addEventListener('click', closeModal);
    document.getElementById('cancelSessionBtn').addEventListener('click', closeModal);
    document.getElementById('saveSessionBtn').addEventListener('click', saveFromModal);

    draftsContainer.addEventListener('click', (e) => {
        const btn = e.target.closest('button');
        if (!btn) return;
        const idx = parseInt(btn.getAttribute('data-index'));
        const action = btn.getAttribute('data-action');
        if (Number.isNaN(idx)) return;
        const draft = sessionDrafts[idx];
        if (action === 'edit' && !draft.deleted) {
            openModal(idx);
        } else if (action === 'undo') {
            sessionDrafts[idx].deleted = false;
            renderDrafts();
        } else if (action === 'remove') {
            if (draft.id && !draft.deleted && (draft.participants_count || 0) > 0) {
                const confirmed = window.confirm('This session has participants. Deleting it will remove those links. Do you want to proceed?');
                if (!confirmed) return;
            }
            if (draft.id) {
                sessionDrafts[idx].deleted = !sessionDrafts[idx].deleted;
            } else {
                sessionDrafts.splice(idx, 1);
            }
            renderDrafts();
        }
    });

    // Keep payload up to date on date changes (re-renders warnings too)
    ['start_date','end_date'].forEach(id => {
        const el = document.getElementById(id);
        el.addEventListener('change', renderDrafts);
        el.addEventListener('input', renderDrafts);
    });

    // Initial render
    renderDrafts();
});
</script>
@endsection 