<x-layout>
    @section('title','Pending QA Review')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
        <div class="mb-6 flex justify-between items-end">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Quality Assurance Dashboard</h1>
                <p class="text-sm text-gray-500 mt-1">Review and approve completed Job Orders before closing the citizen's complaint.</p>
            </div>
            <span class="bg-brand-orange text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-sm">
                {{ $pendingReviews->count() }} Pending {{ Str::plural('Review',$pendingReviews->count()) }}
            </span>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow=hidden">
            @if($pendingReviews->isEmpty())
                <div class="p-12 text-center text-gray-500 flex flex-col items-center">
                    <i class="fa-solid fa-clipboard-check text-5xl text-gray-300 mb-4"></i>
                    <p class="text-lg font-bold text-gray-700">All caught up!</p>
                    <p class="text-sm">There are no completion reports waiting for administrative review.</p>
                </div>
            @else
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider border-b border-gray-200">
                            <th class="p-4 font-bold">Job Id</th>
                            <th class="p-4 font-bold">Category</th>
                            <th class="p-4 font-bold">Supervisor</th>
                            <th class="p-4 font-bold">Completed On</th>
                            <th class="p-4 font-bold text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($pendingReviews as $review)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-4">
                                    <span class="font-bold text-gray-800">#JO-{{ $review->id }}</span>
                                </td>
                                <td class="text-sm font-bold text-brand-blue">
                                    {{ $review->complaint->category->name }}
                                </td>
                                <td class="p-4 text-sm text-gray-700">
                                    @php
                                    $supervisor = $review->workers->firstWhere('user.role','supervisor')
                                    @endphp
                                    <i class="fa-solid fa-hard-hat text-brand-orange mr-1"></i>
                                    {{ $supervisor->user?->name ?? 'None' }}
                                </td>
                                <td class="p-4 text-sm text-gray-500">
                                    {{ $review->completionReport?->completed_at->format('M d, Y h:i A') ?? 'Unknown time' }}
                                </td>
                                <td class="p-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.reviews.export_pdf', $review->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 text-brand-blue hover:bg-brand-blue hover:text-white text-xs font-bold rounded transition border border-blue-200 hover:border-brand-blue">
                                            <i class="fa-solid fa-file-pdf"></i> PDF
                                        </a>

                                        <a class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 text-brand-blue hover:bg-brand-blue hover:text-white text-xs font-bold rounded transition border border-blue-200 hover:border-brand-blue" href="{{ route('admin.reviews.show',$review->id) }}">
                                            Make Decision <i class="fa-solid fa-arrow-right"></i>
                                        </a>
                                    </div>

                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</x-layout>
