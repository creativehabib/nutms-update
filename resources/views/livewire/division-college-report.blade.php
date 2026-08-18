<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-lg dark:border-zinc-700 dark:bg-zinc-900">
        <div class="border-b border-zinc-200 bg-zinc-50 px-6 py-5 dark:border-zinc-700 dark:bg-zinc-800">
            <h1 class="text-xl font-bold text-zinc-900 dark:text-white">বিভাগভিত্তিক কলেজ রিপোর্ট</h1>
            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">বিভাগ নির্বাচন করে জেলাভিত্তিক কলেজের ধরন ও কম্পিউটার ল্যাবের তথ্য দেখুন।</p>

            <div class="mt-5 max-w-md">
                <label for="division" class="mb-2 block text-sm font-semibold text-zinc-800 dark:text-zinc-200">বিভাগ নির্বাচন করুন</label>
                <select id="division" wire:model.live="selectedDivision" class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2.5 text-zinc-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-zinc-600 dark:bg-zinc-900 dark:text-white">
                    <option value="">-- বিভাগ নির্বাচন করুন --</option>
                    @foreach ($divisions as $division)
                        <option value="{{ $division }}">{{ $division }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="p-6" wire:loading.class="opacity-50" wire:target="selectedDivision">
            @if ($selectedDivision === '')
                <div class="rounded-lg border border-dashed border-zinc-300 px-6 py-12 text-center text-zinc-500 dark:border-zinc-600 dark:text-zinc-400">রিপোর্ট দেখতে একটি বিভাগ নির্বাচন করুন।</div>
            @else
                <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                    <h2 class="text-lg font-bold text-zinc-900 dark:text-white">{{ $selectedDivision }} বিভাগের জেলাভিত্তিক রিপোর্ট</h2>
                    <span class="text-sm text-zinc-500 dark:text-zinc-400">মোট জেলা: {{ $districtReports->count() }}</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full border-collapse text-sm">
                        <thead class="bg-zinc-800 text-white dark:bg-zinc-950">
                            <tr>
                                <th class="border border-zinc-300 px-4 py-3 text-left dark:border-zinc-700">জেলা</th>
                                <th class="border border-zinc-300 px-4 py-3 text-center dark:border-zinc-700">মোট কলেজ</th>
                                <th class="border border-zinc-300 px-4 py-3 text-center dark:border-zinc-700">ল্যাব আছে</th>
                                <th class="border border-zinc-300 px-4 py-3 text-center dark:border-zinc-700">ল্যাব নেই</th>
                                <th class="border border-zinc-300 px-4 py-3 text-center dark:border-zinc-700">অনার্স কলেজ</th>
                                <th class="border border-zinc-300 px-4 py-3 text-center dark:border-zinc-700">ডিগ্রি কলেজ</th>
                                <th class="border border-zinc-300 px-4 py-3 text-center dark:border-zinc-700">সরকারি কলেজ</th>
                                <th class="border border-zinc-300 px-4 py-3 text-center dark:border-zinc-700">বেসরকারি কলেজ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @forelse ($districtReports as $report)
                                <tr class="hover:bg-indigo-50 dark:hover:bg-indigo-950/30">
                                    <td class="border border-zinc-200 px-4 py-3 font-semibold text-zinc-900 dark:border-zinc-700 dark:text-white">{{ $report->district_name }}</td>
                                    <td class="border border-zinc-200 px-4 py-3 text-center dark:border-zinc-700">{{ $report->total_colleges }}</td>
                                    <td class="border border-zinc-200 px-4 py-3 text-center font-semibold text-green-700 dark:border-zinc-700 dark:text-green-300">{{ $report->with_lab }}</td>
                                    <td class="border border-zinc-200 px-4 py-3 text-center font-semibold text-red-600 dark:border-zinc-700 dark:text-red-300">{{ $report->without_lab }}</td>
                                    <td class="border border-zinc-200 px-4 py-3 text-center dark:border-zinc-700">{{ $report->honours_colleges }}</td>
                                    <td class="border border-zinc-200 px-4 py-3 text-center dark:border-zinc-700">{{ $report->degree_colleges }}</td>
                                    <td class="border border-zinc-200 px-4 py-3 text-center dark:border-zinc-700">{{ $report->government_colleges }}</td>
                                    <td class="border border-zinc-200 px-4 py-3 text-center dark:border-zinc-700">{{ $report->private_colleges }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="border border-zinc-200 px-6 py-10 text-center text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">এই বিভাগের কোনো কলেজের তথ্য পাওয়া যায়নি।</td></tr>
                            @endforelse
                        </tbody>
                        @if ($districtReports->isNotEmpty())
                            <tfoot class="bg-zinc-100 font-bold text-zinc-900 dark:bg-zinc-800 dark:text-white">
                                <tr>
                                    <td class="border border-zinc-300 px-4 py-3 dark:border-zinc-700">সর্বমোট</td>
                                    @foreach (['total_colleges', 'with_lab', 'without_lab', 'honours_colleges', 'degree_colleges', 'government_colleges', 'private_colleges'] as $column)
                                        <td class="border border-zinc-300 px-4 py-3 text-center dark:border-zinc-700">{{ $districtReports->sum($column) }}</td>
                                    @endforeach
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
