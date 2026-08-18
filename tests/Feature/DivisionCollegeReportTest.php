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
    ] as $index => [$code, $name, $district, $hasLab, $courseType, $collegeType]) {
        Teacher::query()->create([
            'college_code' => $code,
            'college_name' => $name,
            'name' => fake()->name(),
            'subject' => $code === '1001' ? 'Accounting' : 'Bangla',
            'designation' => $code === '1001' ? 'Assistant Professor' : 'Lecturer',
            'mobile_number' => $code === '1001' ? '01700123456' : null,
            'email' => $code === '1001' ? 'teacher@example.com' : null,
            'div_name' => 'Dhaka',
            'districts_name' => $district,
            'has_computer_lab' => $hasLab,
            'course_type' => $courseType,
            'col_type' => $collegeType,
            'upazilla' => 'Test Upazilla',
            'computer_count' => $hasLab === 'no' ? null : 20,
            'has_training' => $index % 2 === 0 ? 'yes' : 'no',
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
                && $gazipur->private_colleges === 1
                && $dhaka->colleges->pluck('college_name')->all() === ['Dhaka College', 'Private College']
                && (int) $dhaka->colleges->firstWhere('college_code', '1001')->trained_teachers === 1
                && (int) $dhaka->colleges->firstWhere('college_code', '1001')->untrained_teachers === 1
                && $dhaka->colleges->firstWhere('college_code', '1001')->collegeTeachers->count() === 2;
        })
        ->assertSee('Dhaka College')
        ->assertSee('Private College')
        ->assertSee('Test Upazilla')
        ->assertSee('ট্রেনিং করেছেন')
        ->assertSee('ট্রেনিং করেননি')
        ->assertSee('শিক্ষকের নাম')
        ->assertSee('সাবজেক্ট')
        ->assertSee('পদবী')
        ->assertSee('মোবাইল')
        ->assertSee('ইমেইল')
        ->assertSee('Accounting')
        ->assertSee('Assistant Professor')
        ->assertSee('01700123456')
        ->assertSee('teacher@example.com')
        ->assertSee('tel:01700123456', false)
        ->assertSee('mailto:teacher@example.com', false)
        ->assertSee('ট্রেনিং করেছেন কি না')
        ->assertSee('Gazipur')
        ->assertDontSee('Cumilla');
});
