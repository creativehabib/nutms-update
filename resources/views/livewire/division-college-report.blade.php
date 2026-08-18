<div class="w-full">
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

                <div class="mt-8 space-y-8">
                    @foreach ($districtReports as $report)
                        <section>
                            <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                                <h3 class="text-lg font-bold text-zinc-900 dark:text-white">{{ $report->district_name }} জেলার কলেজসমূহ</h3>
                                <span class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">{{ $report->total_colleges }}টি কলেজ</span>
                            </div>

                            <div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700" x-data="{ expandedCollege: null }">
                                <table class="min-w-full border-collapse text-sm">
                                    <thead class="bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200">
                                        <tr>
                                            <th class="border-b border-zinc-200 px-4 py-3 text-left dark:border-zinc-700">ক্র. নং</th>
                                            <th class="border-b border-zinc-200 px-4 py-3 text-left dark:border-zinc-700">কলেজের নাম ও কোড</th>
                                            <th class="border-b border-zinc-200 px-4 py-3 text-left dark:border-zinc-700">উপজেলা</th>
                                            <th class="border-b border-zinc-200 px-4 py-3 text-left dark:border-zinc-700">কোর্সের ধরন</th>
                                            <th class="border-b border-zinc-200 px-4 py-3 text-left dark:border-zinc-700">কলেজের ধরন</th>
                                            <th class="border-b border-zinc-200 px-4 py-3 text-center dark:border-zinc-700">কম্পিউটার ল্যাব</th>
                                            <th class="border-b border-zinc-200 px-4 py-3 text-center dark:border-zinc-700">কম্পিউটার</th>
                                            <th class="border-b border-zinc-200 px-4 py-3 text-center dark:border-zinc-700">ট্রেনিং করেছেন</th>
                                            <th class="border-b border-zinc-200 px-4 py-3 text-center dark:border-zinc-700">ট্রেনিং করেননি</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-zinc-200 bg-white dark:divide-zinc-700 dark:bg-zinc-900">
                                        @foreach ($report->colleges as $college)
                                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/70">
                                                <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $loop->iteration }}</td>
                                                <td class="px-4 py-3 text-zinc-900 dark:text-white">
                                                    <button
                                                        type="button"
                                                        class="group flex w-full items-center gap-2 rounded-md text-left outline-none transition-colors duration-200 focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-zinc-900"
                                                        x-on:click="expandedCollege = expandedCollege === @js($college->college_code) ? null : @js($college->college_code)"
                                                        x-bind:aria-expanded="expandedCollege === @js($college->college_code)"
                                                        aria-controls="college-teachers-{{ $college->college_code }}"
                                                    >
                                                        <flux:icon.chevron-down class="size-4 shrink-0 text-zinc-400 transition-transform duration-300 ease-in-out group-hover:text-indigo-600 dark:group-hover:text-indigo-300" x-bind:class="expandedCollege === @js($college->college_code) && 'rotate-180'" />
                                                        <span>
                                                            <span class="block font-semibold text-indigo-700 underline-offset-2 group-hover:underline dark:text-indigo-300">{{ $college->college_name ?: 'উল্লেখ নেই' }}</span>
                                                            <span class="text-xs text-zinc-500 dark:text-zinc-400">কোড: {{ $college->college_code }}</span>
                                                        </span>
                                                    </button>
                                                </td>
                                                <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $college->college_upazilla ?: 'উল্লেখ নেই' }}</td>
                                                <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $college->college_course_type ?: 'উল্লেখ নেই' }}</td>
                                                <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $college->college_type ?: 'উল্লেখ নেই' }}</td>
                                                <td class="px-4 py-3 text-center">
                                                    @if ((int) $college->has_lab === 1)
                                                        <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-bold text-green-700 dark:bg-green-950 dark:text-green-300">আছে</span>
                                                    @else
                                                        <span class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-bold text-red-700 dark:bg-red-950 dark:text-red-300">নেই</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-center font-semibold text-zinc-700 dark:text-zinc-200">{{ (int) ($college->computer_count ?? 0) }}</td>
                                                <td class="px-4 py-3 text-center font-bold text-green-700 dark:text-green-300">{{ (int) $college->trained_teachers }}</td>
                                                <td class="px-4 py-3 text-center font-bold text-red-600 dark:text-red-300">{{ (int) $college->untrained_teachers }}</td>
                                            </tr>
                                            <tr class="bg-zinc-50/70 dark:bg-zinc-950/40">
                                                <td colspan="9" class="p-0">
                                                    <div
                                                        id="college-teachers-{{ $college->college_code }}"
                                                        x-cloak
                                                        x-show="expandedCollege === @js($college->college_code)"
                                                        x-transition:enter="transition ease-out duration-300"
                                                        x-transition:enter-start="opacity-0 -translate-y-2 scale-[0.98]"
                                                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                                        x-transition:leave="transition ease-in duration-200"
                                                        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                                        x-transition:leave-end="opacity-0 -translate-y-2 scale-[0.98]"
                                                        class="origin-top px-6 py-5 sm:px-10"
                                                    >
                                                        <div class="mb-3 flex items-center justify-between gap-3">
                                                            <h4 class="font-semibold text-zinc-900 dark:text-white">{{ $college->college_name ?: 'এই কলেজ' }}-এর শিক্ষকসমূহ</h4>
                                                            <span class="rounded-full bg-indigo-100 px-2.5 py-1 text-xs font-semibold text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">{{ $college->collegeTeachers->count() }} জন</span>
                                                        </div>

                                                        <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                                                            <table class="min-w-full text-sm">
                                                                <thead class="bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                                                                    <tr>
                                                                        <th class="px-4 py-3 text-left font-semibold">শিক্ষকের নাম</th>
                                                                        <th class="px-4 py-3 text-left font-semibold">সাবজেক্ট</th>
                                                                        <th class="px-4 py-3 text-left font-semibold">পদবী</th>
                                                                        <th class="px-4 py-3 text-left font-semibold">মোবাইল</th>
                                                                        <th class="px-4 py-3 text-left font-semibold">ইমেইল</th>
                                                                        <th class="px-4 py-3 text-center font-semibold">ট্রেনিং করেছেন কি না</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                                                    @forelse ($college->collegeTeachers as $teacher)
                                                                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/60">
                                                                            <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">{{ $teacher->name ?: 'উল্লেখ নেই' }}</td>
                                                                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $teacher->subject ?: 'উল্লেখ নেই' }}</td>
                                                                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $teacher->designation ?: 'উল্লেখ নেই' }}</td>
                                                                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">
                                                                                @if ($teacher->mobile_number)
                                                                                    <a href="tel:{{ $teacher->mobile_number }}" class="text-indigo-700 hover:underline dark:text-indigo-300">{{ $teacher->mobile_number }}</a>
                                                                                @else
                                                                                    উল্লেখ নেই
                                                                                @endif
                                                                            </td>
                                                                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">
                                                                                @if ($teacher->email)
                                                                                    <a href="mailto:{{ $teacher->email }}" class="text-indigo-700 hover:underline dark:text-indigo-300">{{ $teacher->email }}</a>
                                                                                @else
                                                                                    উল্লেখ নেই
                                                                                @endif
                                                                            </td>
                                                                            <td class="px-4 py-3 text-center">
                                                                                @if (in_array(mb_strtolower(trim((string) $teacher->has_training)), ['yes', 'হ্যাঁ'], true))
                                                                                    <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-bold text-green-700 dark:bg-green-950 dark:text-green-300">হ্যাঁ</span>
                                                                                @else
                                                                                    <span class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-bold text-red-700 dark:bg-red-950 dark:text-red-300">না</span>
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    @empty
                                                                        <tr>
                                                                            <td colspan="6" class="px-4 py-8 text-center text-zinc-500 dark:text-zinc-400">এই কলেজে কোনো শিক্ষকের তথ্য পাওয়া যায়নি।</td>
                                                                        </tr>
                                                                    @endforelse
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
