<x-layout>
    @section('title', 'Ticket #' . str_pad($complaint->id, 5, '0', STR_PAD_LEFT) . ' - ' . $complaint->title)
    @php
$hasCompletion = false;
foreach ($complaint->jobOrders as $job) {
    if ($job->completionReport) {
        $hasCompletion = true;
    }
}

$latestStage = 1;
if ($complaint->status === 'in_progress')
    $latestStage = 2;
if ($complaint->status === 'under_review')
    $latestStage = 3;
if ($complaint->status === 'under_review' && $hasCompletion)
    $latestStage = 4;
if ($complaint->status === 'approved')
    $latestStage = 5;
if ($complaint->status === 'resolved')
    $latestStage = 6;
if ($complaint->status === 'reopened')
    $latestStage = 7;
    @endphp
    <div class="max-w-[95%] mx-auto px-6 lg:px-8 py-8 w-full">
        <a href="{{ route('admin.users.complaints.index', $complaint->user_id) }}"
            class="inline-flex items-center gap-2 text-gray-500 hover:text-brand-blue font-medium text-sm mb-6 transition">
            <i class="fa-solid fa-arrow-left"></i> Back to Complaints
        </a>

        <div
            class="bg-linear-to-r from-brand-dark to-brand-blue rounded-xl shadow-lg border border-gray-800 overflow-hidden mb-6 flex flex-col sm:flex-row items-center justify-between p-6 gap-4">
            <div class="flex items-center gap-4 text-white w-full sm:w-auto">
                <div
                    class="w-12 h-12 rounded-full bg-brand-orange flex items-center justify-center text-xl font-bold shadow-inner shrink-0">
                    {{ substr($complaint->user?->name, 0, 1) }}
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wider font-bold mb-0.5">Submitted By</p>
                    <h3 class="text-lg font-bold">{{ $complaint->user?->name }} <span
                            class="text-[10px] ml-2 px-2 py-0.5 bg-gray-700 rounded text-gray-300">{{ $complaint->user?->role }}</span>
                    </h3>
                    <p class="text-sm text-gray-300 mt-0.5 flex items-center flex-wrap gap-x-2">
                        <span><i class="fa-solid fa-envelope text-gray-500 mr-1"></i>{{ $complaint->user->email }}</span>
                        <span class="hidden sm:inline text-gray-600">|</span>
                        <span><i
                                class="fa-solid fa-phone text-gray-500 mr-1"></i>{{ $complaint->user->phone ?? 'N/A' }}</span>
                    </p>
                </div>
            </div>
            <div class="shrink-0 w-full sm:w-auto">
                <a href="{{ route('admin.users.showProfile', $complaint->user_id) }}"
                    class="block text-center px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white text-sm font-bold rounded-lg transition">
                    View Full Profile
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                    <div
                        class="p-6 border-b border-gray-200 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <div class="mb-2">
                                <span
                                    class="text-xs font-mono bg-gray-100 text-gray-600 px-2 py-1 rounded border border-gray-200">
                                    Ticket #{{ str_pad($complaint->id, 5, '0', STR_PAD_LEFT) }}
                                </span>
                            </div>
                            <h2 class="text-2xl font-bold text-brand-dark">{{ $complaint->title }}</h2>
                            <p class="text-sm text-gray-500 mt-1">
                                Reported on {{ $complaint->created_at->format('F j, Y g:i A') }}
                            </p>
                        </div>
                        @php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                'in_progress' => 'bg-blue-100 text-blue-800 border-blue-200',
                                'under_review' => 'bg-purple-100 text-purple-800 border-purple-200',
                                'approved' => 'bg-green-100 text-green-800 border-green-200',
                                'resolved' => 'bg-gray-100 text-gray-800 border-gray-200',
                                'reopened' => 'bg-red-100 text-red-800 border-red-200',
                            ];

                            $ColorClass = $statusColors[$complaint->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                        @endphp
                        <div>
                            <span class="px-4 py-2 rounded-full text-sm font-bold border shadow-sm {{ $ColorClass }}">
                                Status: {{ str_replace('_', ' ', strtoupper($complaint->status)) }}
                            </span>
                            <p class="text-sm text-gray-500 mt-6">
                                Approved at
                                {{ $complaint->approved_at ? Carbon\Carbon::parse($complaint->approved_at)->format('F j, Y \a\t g:i A') : 'N/A' }}
                            </p>
                            <p class="text-sm text-gray-500 mt-6">
                                Resolved at
                                {{ $complaint->resolved_at ? Carbon\Carbon::parse($complaint->resolved_at)->format('F j, Y \a\t g:i A') : 'N/A' }}
                            </p>
                        </div>

                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-6 flex flex-col">
                            <div>
                                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Category</h4>
                                <p
                                    class="text-gray-800 font-medium bg-gray-50 inline-block px-4 py-2 rounded-lg border border-gray-200 shadow-sm">
                                    <i class="fa-solid fa-tag text-brand-blue"></i>
                                    {{ $complaint->category->name ?? 'N/A' }}
                                </p>
                            </div>
                            <div class="grow flex flex-col">
                                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Citizen's Report
                                </h4>
                                <div class="relative bg-blue-50/50 p-6 rounded-xl border border-blue-100 shadow-sm grow">
                                    <i
                                        class="fa-solid fa-quote-left absolute top-4 left-4 text-blue-200 text-4xl z-0 opacity-50"></i>
                                    <p
                                        class="text-gray-700 leading-relaxed relative z-10 pl-8 pt-2 text-sm md:text-base italic">
                                        {{ $complaint->description }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col">
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Reported Location</h4>
                            <div id="readonly-map"
                                class="w-full h-64 bg-gray-200 border-2 border-gray-200 rounded-lg shadow-inner"></div>

                            <a href="https://maps.google.com/?q={{ $complaint->latitude }},{{ $complaint->longitude }}"
                                target="_blank"
                                class="w-full bg-gray-50 hover:bg-gray-100 text-brand-blue font-bold py-3 px-4 rounded-b-xl transition flex items-center justify-center gap-2 text-sm border-2 border-gray-200 pointer shadow-sm group">
                                <i
                                    class="fa-solid fa-route text-brand-orange group-hover:scale-110 transition-transform"></i>
                                View
                                on Google Maps
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="sticky top-6" x-data="{activeStage: {{ $latestStage }}}">
                <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2 border-b pb-3">
                    <i class="fa-solid fa-list-ol text-brand-blue pl-2"></i> Complaint Lifecycle Stages
                </h3>
                <div class="space-y-4">
                    @if($latestStage >= 1)
                        <div class="border border-gray-200 rounded-xl bg-white overflow-hidden shadow-sm">
                            <button type="button" onclick="toggleStage(1)"
                                class="w-full flex items-center justify-between p-4 hover:bg-gray-50 transition text-left">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-8 h-8 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center font-bold border border-gray-200">
                                        1
                                    </div>
                                    <h4 class="font-bold text-gray-800">Submission Details</h4>
                                </div>
                                <i id="chevron-1" class="fa-solid fa-chevron-down text-gray-400 transition-transform pointer duration-200 {{ $latestStage === 1 ? 'rotate-180':'' }}"></i>
                            </button>
                            <div id="stage-1" class="p-6 border-t border-gray-200 bg-gray-50/50 {{ $latestStage === 1 ? 'block' : 'hidden' }}">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Submitted
                                            By</span>
                                        <span class="block text-gray-800 font-medium">{{ $complaint->user?->name }} </span>
                                    </div>
                                    <div>
                                        <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Submitted
                                            At</span>
                                        <span
                                            class="block text-gray-800 font-medium">{{ $complaint->created_at->format('F j, Y g:i A') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if($latestStage >= 2)
                        <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm transition-all duration-200">
                            <button type="button" onclick="toggleStage(2)"
                                class="w-full flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100 transition text-left">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-7 h-7 rounded-full bg-yellow-400 text-yellow-900 flex items-center justify-center text-sm font-bold shadow-sm">
                                        2</div>
                                    <h4 class="font-bold text-gray-800 text-sm">Dispatched / In Progress</h4>
                                </div>
                                <i id="chevron-2" class="fa-solid fa-chevron-down text-gray-400 text-sm transition-transform duration-200 {{ $latestStage === 2 ? 'rotate-180':'' }}"></i>
                            </button>
                            <div id="stage-2" class="p-4 border-t border-gray-200 bg-white {{ $latestStage === 2 ? 'block' : 'hidden' }}">
                                <div class="space-y-4">
                                    @foreach ($complaint->jobOrders as $job)
                                        <div class="border border-gray-100 rounded-lg p-3 bg-gray-50 shadow-sm">
                                            <h5 class="text-xs font-bold text-gray-700 mb-2 border-b pb-1">Job #{{ $job->id }}</h5>
                                            <div class="space-y-2">
                                                <div>
                                                    <span
                                                        class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Dispatcher</span>
                                                    <span class="block text-gray-800 font-sm"><i
                                                            class="fa-solid fa-headset text-brand-orange mr-1"></i>{{ $job->assignedBy->user->name ?? 'System' }}</span>
                                                </div>
                                                <div>
                                                    <span
                                                        class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Assigned
                                                        Team</span>
                                                    <div class="flex flex-wrap gap-1 mt-1">
                                                        @foreach ($job->workers as $worker)
                                                            <span
                                                                class="px-2 py-0.5 bg-brand-blue/10 text-brand-blue rounded text-[10px] font-bold border border-brand-blue/20">
                                                                <i class="fa-solid fa-user-helmet mr-1"></i> {{ $worker->user->name }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                    @if($latestStage >= 3)
                        <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm transition-all duration-200">
                            <button type="button" onclick="toggleStage(3)"
                                class="w-full flex items-center justify-between p-4 bg-gray-50 hover:bg-ragy-100 transition text-left">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-7 h-7 rounded-full bg-purple-500 text-white flex items-center justify-center text-sm font-bold shadow-sm">
                                        3</div>
                                    <h4 class="font-bold text-gray-800 text-sm">Under Review</h4>
                                </div>
                                <i id="chevron-3" class="fa-solid fa-chevron-down text-gray-400 text-sm transition-transform duration-200 {{ $latestStage === 3 ? 'rotate-180' : '' }}"></i>
                            </button>
                            <div id="stage-3" class="p-4 border-t border-gray-200 bg-white {{ $latestStage === 3 ? 'block' : 'hidden' }}">
                                <div
                                    class="flex items-center gap-3 text-gray-600 bg-purple-50 p-3 rounded-lg border border-purple-100">
                                    <i class="fa-solid fa-magnifying-glass text-purple-400"></i>
                                    <p class="text-sm">The execution of this complaint is currently under administrative review.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if ($latestStage >= 4)
                        <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm transition-all duration-200">
                            <button type="button" onclick="toggleStage(4)"
                                class="w-full flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100 transition text-left">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-7 h-7 rounded-full bg-blue-500 text-white flex items-center justify-center text-sm font-bold shadow-sm">
                                        4</div>
                                    <h4 class="font-bold text-gray-800 text-sm">Execution Reports</h4>
                                </div>
                                <i id="chevron-4" class="fa-solid fa-chevron-down text-gray-400 text-sm transition-transform duration-200 {{ $latestStage === 4 ? 'rotate-180' : '' }}"></i>
                            </button>
                            <div id="stage-4" class="p-4 border-t border-gray-200 bg-white {{ $latestStage === 4 ? 'block' : 'hidden' }}">
                                <div class="space-y-4">
                                    @foreach ($complaint->jobOrders as $job)
                                        @if($job->completionReport)
                                            <div class="border border-blue-100 rounded-lg p-3 bg-blue-50/30 shadow-sm">
                                                <h5 class="text-xs font-bold text-gray-700 mb-2 border-b border-blue-500 pb-1">Report
                                                    for Job #{{ $job->id }}</h5>
                                                <div class="space-y-2">
                                                    <div>
                                                        <span
                                                            class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Supervisor</span>
                                                        <span class="block text-gray-800 font-sm"><i
                                                                class="fa-solid fa-user-tie text-blue-500 mr-1"></i>{{ $job->completionReport->reportedBy->user->name ?? 'System' }}</span>
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Submitted
                                                            At</span>
                                                        <span
                                                            class="block text-gray-800 font-sm">{{ $job->completionReport->created_at->format('M j, Y g:i A') }}</span>
                                                    </div>
                                                    @if ($job->completionReport->supervisor_comments)
                                                        <div class="bg-white p-2 rounded border border-blue-100 mt-2">
                                                            <span
                                                                class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Supervisor
                                                                Comments</span>
                                                            <p class="text-xs text-gray-700 italic">
                                                                {{ $job->completionReport->supervisor_comments }}</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                    @if($latestStage >= 5)
                        <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm transition-all duration-200">
                            <button type="button" onclick="toggleStage(5)"
                                class="w-full flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100 transition text-left">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-7 h-7 rounded-full bg-teal-500 text-white flex items-center justify-center text-sm font-bold shadow-sm">
                                        5</div>
                                    <h4 class="font-bold text-gray-800 text-sm">Approved</h4>
                                </div>
                                <i class="fa-solid fa-chevron-down text-gray-400 text-sm transition-transform duration-200 {{ $latestStage === 5 ? 'rotate-180':'' }}"></i>
                            </button>
                            <div id="stage-5" class="p-4 border-t border-gray-200 bg-white {{ $latestStage === 5 ? 'block' : 'hidden' }}">
                                <div class="space-y-3">
                                    <div>
                                        <span
                                            class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Approved
                                            By</span>
                                        <span class="block text-gray-800 font-sm"><i
                                                class="fa-solid fa-check-circle mr-1"></i>{{ $complaint->approvedBy->user->name ?? 'System' }}</span>
                                    </div>
                                    <div>
                                        <span
                                            class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Approved
                                            At</span>
                                        <span
                                            class="block text-gray-800 font-sm">{{ $complaint->approved_at ? \Carbon\Carbon::parse($complaint->approved_at)->format('M j, Y  g:i A') : 'Pending' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if ($latestStage >= 6)
                        <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm transition-all duration-200">
                            <button type="button" onclick="toggleStage(6)"
                                class="w-full flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100 transition text-left">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-7 h-7 rounded-full bg-green-500 text-white flex items-center justify-center text-sm font-bold shadow-sm">
                                        6</div>
                                    <h4 class="font-bold text-gray-800 text-sm">Resolved & Feedback</h4>
                                </div>
                                <i class="fa-solid fa-chevron-down text-gray-400 text-sm transition-transform duration-200 {{ $latestStage === 6 ? 'rotate-180':'' }}"></i>
                            </button>
                            <div id="stage-6" class="p-4 border-t border-gray-200 bg-white {{ $latestStage === 6 ? 'block' : 'hidden' }}">
                                <div class="space-y-3">
                                    <div>
                                        <span
                                            class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Resolved
                                            By</span>
                                        <span class="block text-gray-800 font-sm"><i
                                                class="fa-solid fa-check-double mr-1"></i>{{ $complaint->resolvedBy->user->name ?? 'System' }}</span>
                                    </div>
                                    <div>
                                        <span
                                            class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Resolved
                                            At</span>
                                        <span
                                            class="block text-gray-800 font-sm">{{ $complaint->resolved_at ? \Carbon\Carbon::parse($complaint->resolved_at)->format('M j, Y  g:i A') : 'Pending' }}</span>
                                    </div>

                                    <div class="pt-2 border-t border-gray-100">
                                        <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Citizen
                                            Feedback</span>
                                        @if ($complaint->feedback)
                                            <div class="bg-gray-50 p-3 rounded-lg border border-yellow-200 mt-1">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <div class="flex text-yellow-400 text-sm">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i
                                                                class="fa-{{ $i <= $complaint->feedback->rating ? 'solid' : 'regular' }} fa-star"></i>
                                                        @endfor
                                                    </div>
                                                    <span
                                                        class="text-xs text-gray-800 font-bold">{{ $complaint->feedback->rating }}/5</span>
                                                </div>
                                                <p class="text-xs text-gray-600 italic">
                                                    {{ $complaint->feedback->quality_comments ?? 'No comment provided.' }}</p>
                                            </div>
                                        @else
                                            <span class="block text-sm text-gray-500 italic mt-1">No feedback provided yet.</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if ($latestStage === 7)
                        <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm transition-all duration-200">
                            <button type="button" onclick="toggleStage(7)"
                                class="w-full flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100 transition text-left">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-7 h-7 rounded-full bg-red-500 text-white flex items-center justify-center text-sm font-bold shadow-sm">
                                        7</div>
                                    <h4 class="font-bold text-gray-800 text-sm">Reopened</h4>
                                </div>
                                <i class="fa-solid fa-chevron-down text-gray-400 text-sm transition-transform duration-200 {{ $latestStage === 7 ? 'rotate-180' : '' }}"></i>
                            </button>
                            <div id="stage-7" class="p-4 border-t border-gray-200 bg-white {{ $latestStage === 7 ? 'block' : 'hidden' }}">
                                <div class="flex items-center gap-3 text-gray-600 bg-red-50 p-3 rounded-lg border-red-100">
                                    <i class="fa-solid fa-rotate-left text-red-400 text-lg"></i>
                                    <p class="text-sm">This complaint has been reopened for further investigation or action.</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
        <script
            src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps_key') }}&callback=initReadonlyMap&loading=async"
            async defer></script>
        <script>
            function toggleStage(stage){
                const content = document.getElementById('stage-' + stage)
                const chevron = document.getElementById('chevron-' + stage)

                if(content.classList.contains('hidden')){
                    content.classList.remove('hidden')
                    content.classList.add('block')
                    chevron.classList.add('rotate-180')
                }else{
                    content.classList.remove('block')
                    content.classList.add('hidden')
                    chevron.classList.remove('rotate-180')
                }
            }
            function initReadonlyMap() {
                const location = {
                    lat: {{ $complaint->latitude }},
                    lng: {{ $complaint->longitude }}
                    };

                const map = new google.maps.Map(document.getElementById("readonly-map"), {
                    zoom: 16,
                    center: location,
                    streetViewControl: false,
                    mapTypeControl: false,
                    gestureHandling: "none",
                    zoomControl: false
                });

                new google.maps.Marker({
                    position: location,
                    map: map,
                    animation: google.maps.Animation.DROP,
                });
            }

            document.addEventListener("DOMContentLoaded", initReadonlyMap);
        </script>
</x-layout>
