<?php

use App\Livewire\DivisionCollegeReport;
use App\Models\Teacher;
use App\Models\User;
use Livewire\Livewire;

test('division college report route requires authentication', function () {
    $this->get(route('reports.division-colleges'))->assertRedirect(route('login'));
});

test('division selection shows district wise unique college statistics', function () {
    foreach ([
        ['1001', 'Dhaka College', 'Dhaka', 'yes', 'Honours and Degree', 'Government'],
        ['1001', 'Dhaka College', 'Dhaka', 'yes', 'Honours and Degree', 'Government'],
        ['1002', 'Private College', 'Dhaka', 'no', 'Degree', 'Private'],
        ['1003', 'Gazipur College', 'Gazipur', 'হ্যাঁ', 'অনার্স', 'বেসরকারি'],
    ] as [$code, $name, $district, $hasLab, $courseType, $collegeType]) {
        Teacher::query()->create([
            'college_code' => $code,
            'college_name' => $name,
            'name' => fake()->name(),
            'div_name' => 'Dhaka',
            'districts_name' => $district,
            'has_computer_lab' => $hasLab,
            'course_type' => $courseType,
            'col_type' => $collegeType,
        ]);
    }

    Teacher::query()->create([
        'college_code' => '2001',
        'college_name' => 'Other Division College',
        'name' => 'Other Teacher',
        'div_name' => 'Chattogram',
        'districts_name' => 'Cumilla',
    ]);

    Livewire::actingAs(User::factory()->create())
        ->test(DivisionCollegeReport::class)
        ->assertSee('Dhaka')
        ->set('selectedDivision', 'Dhaka')
        ->assertViewHas('districtReports', function ($reports): bool {
            $dhaka = $reports->firstWhere('district_name', 'Dhaka');
            $gazipur = $reports->firstWhere('district_name', 'Gazipur');

            return $reports->count() === 2
                && $dhaka->total_colleges === 2
                && $dhaka->with_lab === 1
                && $dhaka->without_lab === 1
                && $dhaka->honours_colleges === 1
                && $dhaka->degree_colleges === 2
                && $dhaka->government_colleges === 1
                && $dhaka->private_colleges === 1
                && $gazipur->total_colleges === 1
                && $gazipur->with_lab === 1
                && $gazipur->honours_colleges === 1
                && $gazipur->private_colleges === 1;
        })
        ->assertSee('Gazipur')
        ->assertDontSee('Cumilla');
});
