<?php

use App\Livewire\CollegeLabSummary;
use App\Models\Teacher;
use Livewire\Livewire;
use Maatwebsite\Excel\Facades\Excel;

test('college lab summary paginates colleges and only loads the active tab', function () {
    foreach (range(1, 51) as $index) {
        Teacher::query()->create([
            'name' => "Lab Teacher {$index}",
            'college_code' => (string) (1000 + $index),
            'has_computer_lab' => 'yes',
        ]);
    }

    Teacher::query()->create([
        'name' => 'Teacher Without Lab',
        'college_code' => '2000',
        'college_name' => 'College Without Lab',
        'has_computer_lab' => 'no',
    ]);

    Livewire::test(CollegeLabSummary::class)
        ->assertViewHas('colleges', fn ($colleges): bool => $colleges->count() === 50 && $colleges->total() === 51)
        ->assertDontSee('College Without Lab')
        ->call('showTab', 'without_lab')
        ->assertSet('activeTab', 'without_lab')
        ->assertViewHas('colleges', fn ($colleges): bool => $colleges->count() === 1 && $colleges->total() === 1)
        ->assertSee('College Without Lab');
});

test('each college lab tab can be exported to its own spreadsheet', function (string $tab, string $filename, string $hasComputerLab) {
    Excel::fake();

    Teacher::query()->create([
        'name' => 'Lab Teacher',
        'college_code' => '1001',
        'college_name' => 'Export College',
        'has_computer_lab' => $hasComputerLab,
        'computer_count' => $hasComputerLab === 'yes' ? 20 : null,
    ]);

    Livewire::test(CollegeLabSummary::class)->call('export', $tab);

    Excel::assertDownloaded($filename);
})->with([
    ['with_lab', 'colleges-with-computer-lab.xlsx', 'yes'],
    ['without_lab', 'colleges-without-computer-lab.xlsx', 'no'],
]);

test('lab summary normalizes college codes names and affirmative lab values', function () {
    Teacher::query()->create([
        'name' => 'First Teacher',
        'college_code' => ' 1001 ',
        'college_name' => 'Older College Name',
        'has_computer_lab' => ' হ্যাঁ ',
        'computer_count' => 20,
    ]);
    Teacher::query()->create([
        'name' => 'Second Teacher',
        'college_code' => '1001',
        'college_name' => 'Updated College Name',
        'has_computer_lab' => 'no',
        'computer_count' => 10,
    ]);

    Livewire::test(CollegeLabSummary::class)
        ->assertViewHas('colleges', fn ($colleges): bool => $colleges->total() === 1
            && $colleges->first()->college_code === '1001'
            && (int) $colleges->first()->total_computers === 20)
        ->call('showTab', 'without_lab')
        ->assertViewHas('colleges', fn ($colleges): bool => $colleges->total() === 0);
});
