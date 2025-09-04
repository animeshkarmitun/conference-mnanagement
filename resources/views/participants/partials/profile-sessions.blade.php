<div>
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-semibold">My Sessions</h3>
        <button type="button" id="openAssignSessions" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-sm font-semibold">Assign Sessions</button>
    </div>

    @php
        $sessionCount = count($sessions ?? []);
        echo "<script>console.log('Sessions count: $sessionCount');</script>";
    @endphp
    @if($sessionCount)
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <ul class="divide-y divide-gray-200">
                @foreach($sessions as $session)
                    <li class="p-4 hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex justify-between items-center">
                            <div class="flex-1">
                                <div class="font-semibold text-gray-800">{{ $session->title }}</div>
                                <div class="text-sm text-gray-500 mt-1">
                                    <span class="inline-flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        {{ \Carbon\Carbon::parse($session->start_time)->format('M d, Y H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                                    </span>
                                </div>
                                @if($session->description)
                                    <div class="text-sm text-gray-600 mt-2">{{ Str::limit($session->description, 100) }}</div>
                                @endif
                                @if($session->room)
                                    <div class="text-sm text-gray-500 mt-1">
                                        <span class="inline-flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                            {{ $session->room }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex items-center space-x-3">
                                <span class="text-xs px-3 py-1 rounded-full bg-blue-100 text-blue-700 font-semibold">
                                    {{ $session->pivot->role ?? 'Participant' }}
                                </span>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @else
        <div class="text-center py-12 bg-gray-50 rounded-lg">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No sessions assigned</h3>
            <p class="mt-1 text-sm text-gray-500">You haven't been assigned to any sessions yet. Contact the conference organizers for session assignments.</p>
        </div>
    @endif

    <!-- Assign Sessions Modal -->
    <div id="assignSessionsModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50"></div>
        <div class="relative mx-auto mt-14 w-full max-w-5xl">
            <div class="bg-white rounded-2xl shadow-2xl p-6 lg:p-8">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <h4 class="text-xl font-semibold text-gray-900">Assign Sessions</h4>
                        <span id="selectedCount" class="hidden text-sm text-gray-500">(0 selected)</span>
                    </div>
                    <button type="button" id="closeAssignSessions" class="rounded-full p-2 hover:bg-gray-100 text-gray-500 hover:text-gray-700" aria-label="Close">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-5">
                    <div class="md:col-span-2">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input type="text" id="sess_search" placeholder="Search by title..." class="w-full rounded-lg border-gray-300 pl-10 pr-3 py-2 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Venue</label>
                        <select id="sess_venue" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                            <option value="">All</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <label class="inline-flex items-center text-sm text-gray-700">
                            <input id="only_available" type="checkbox" class="mr-2 rounded border-gray-300 text-yellow-600 focus:ring-yellow-500" checked> Only available (no overlaps)
                        </label>
                    </div>
                </div>

                <div class="border rounded-xl overflow-hidden">
                    <div class="overflow-y-auto max-h-80">
                        <table class="min-w-full">
                            <thead class="bg-gray-50 sticky top-0 z-10">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Select</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Title</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Date/Time</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Venue</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Role</th>
                                </tr>
                            </thead>
                            <tbody id="sess_results" class="divide-y"></tbody>
                        </table>
                    </div>
                </div>

                <div class="flex items-center justify-between mt-5">
                    <span id="resultsMeta" class="text-sm text-gray-500"></span>
                    <div class="flex gap-2">
                        <button type="button" id="cancelAssignBtn" class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50">Cancel</button>
                        <button type="button" id="confirmAssignBtn" class="px-5 py-2 rounded-lg bg-yellow-600 text-white font-semibold disabled:opacity-50" disabled>Assign selected</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const openBtn = document.getElementById('openAssignSessions');
        const modal = document.getElementById('assignSessionsModal');
        const closeBtn = document.getElementById('closeAssignSessions');
        const cancelBtn = document.getElementById('cancelAssignBtn');
        const resultsBody = document.getElementById('sess_results');
        const confirmBtn = document.getElementById('confirmAssignBtn');
        const searchInput = document.getElementById('sess_search');
        const venueSelect = document.getElementById('sess_venue');
        const onlyAvailable = document.getElementById('only_available');

        const assignedRaw = @json($sessions ?? []);
        const assigned = (assignedRaw || []).map(s => ({ id: s.id, start: s.start_time, end: s.end_time, venue_id: s.venue_id }));
        const available = @json(($availableSessions ?? collect())->values());

        // Populate venue filter
        const venues = Array.from(new Set((available||[]).map(s=>s.venue_id).filter(Boolean)));
        venues.forEach(v=>{ const opt=document.createElement('option'); opt.value=v; opt.textContent='Venue '+v; venueSelect.appendChild(opt); });

        function overlaps(a,b){ return new Date(a.start_time||a.start) < new Date(b.end_time||b.end) && new Date(b.start_time||b.start) < new Date(a.end_time||a.end); }
        function render(){
            const q = (searchInput.value||'').toLowerCase();
            const v = venueSelect.value;
            resultsBody.innerHTML='';
            const rows = (available||[]).filter(s=>{
                const matchQ = (s.title||'').toLowerCase().includes(q);
                const matchV = !v || String(s.venue_id)===String(v);
                let ok = matchQ && matchV;
                if (ok && onlyAvailable.checked){ ok = !assigned.some(a=>overlaps(a,s) && (!v || String(a.venue_id)===String(v))); }
                return ok;
            });
            rows.forEach(s=>{
                const tr=document.createElement('tr');
                const hasConflict = assigned.some(a=>overlaps(a,s));
                tr.className='hover:bg-yellow-50';
                tr.innerHTML = `<td class=\"px-4 py-2\"><input type=\"checkbox\" data-id=\"${s.id}\" class=\"rounded border-gray-300 text-yellow-600 focus:ring-yellow-500\"></td>
                                <td class=\"px-4 py-2\"><div class=\"flex items-center gap-2\">${s.title}${hasConflict?'<span class=\\"text-xs px-2 py-0.5 rounded bg-red-100 text-red-700\\">(Overlaps)</span>':''}</div></td>
                                <td class=\"px-4 py-2 whitespace-nowrap\">${s.start_time} → ${s.end_time}</td>
                                <td class="px-4 py-2">${s.venue_id ?? '-'}</td>
                                <td class="px-4 py-2"><select data-role="${s.id}" class="border rounded px-2 py-1 text-sm">
                                    <option value="participant">Participant</option>
                                    <option value="speaker">Speaker</option>
                                    <option value="panelist">Panelist</option>
                                </select></td>`;
                resultsBody.appendChild(tr);
            });
            updateState();
            const meta = document.getElementById('resultsMeta');
            if (meta) meta.textContent = `${rows.length} result${rows.length===1?'':'s'}`;
        }

        function updateState(){
            const any = resultsBody.querySelectorAll('input[type="checkbox"]:checked').length>0;
            confirmBtn.disabled = !any;
            const sc=document.getElementById('selectedCount');
            if(sc){ sc.classList.toggle('hidden', !any); sc.textContent = `(${any} selected)`; }
        }

        openBtn?.addEventListener('click', ()=>{ modal.classList.remove('hidden'); render(); });
        closeBtn?.addEventListener('click', ()=> modal.classList.add('hidden'));
        cancelBtn?.addEventListener('click', ()=> modal.classList.add('hidden'));
        searchInput?.addEventListener('input', render);
        venueSelect?.addEventListener('change', render);
        onlyAvailable?.addEventListener('change', render);
        resultsBody?.addEventListener('change', (e)=>{ if(e.target.matches('input[type="checkbox"]')) updateState(); });

        confirmBtn?.addEventListener('click', async ()=>{
            const checked=[...resultsBody.querySelectorAll('input[type="checkbox"]:checked')].map(cb=>parseInt(cb.dataset.id));
            const roles={}; checked.forEach(id=>{ const sel=resultsBody.querySelector(`select[data-role="${id}"]`); roles[id]=sel?.value||'participant'; });
            try{
                const res = await fetch(`{{ route('participants.assign-session', $participant) }}`, { method:'POST', headers:{ 'Content-Type':'application/json', 'X-CSRF-TOKEN':'{{ csrf_token() }}' }, body: JSON.stringify({ session_ids: checked, roles }) });
                if(res.ok){ location.reload(); } else { alert('Failed to assign sessions'); }
            }catch(err){ alert('Network error'); }
        });
    });
    </script>
</div> 