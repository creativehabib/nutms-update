<?php

namespace App\Livewire;

use App\Models\Teacher;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DivisionCollegeReport extends Component
{
    public string $selectedDivision = '';

    public function updatedSelectedDivision(): void
    {
        $this->selectedDivision = trim($this->selectedDivision);
    }

    public function render(): View
    {
        return view('livewire.division-college-report', [
            'divisions' => Teacher::query()
                ->whereNotNull('div_name')
                ->where('div_name', '!=', '')
                ->distinct()
                ->orderBy('div_name')
                ->pluck('div_name'),
            'districtReports' => $this->districtReports(),
        ]);
    }

    /**
     * @return Collection<int, object{district_name: string, total_colleges: int, with_lab: int, without_lab: int, honours_colleges: int, degree_colleges: int, government_colleges: int, private_colleges: int, colleges: Collection<int, Teacher>}>
     */
    private function districtReports(): Collection
    {
        if ($this->selectedDivision === '') {
            return collect();
        }

        return $this->collegesQuery()
            ->get()
            ->groupBy(fn (Teacher $college): string => $college->district_name ?: 'জেলা উল্লেখ নেই')
            ->map(function (Collection $colleges, string $district): object {
                return (object) [
                    'district_name' => $district,
                    'total_colleges' => $colleges->count(),
                    'with_lab' => $colleges->where('has_lab', 1)->count(),
                    'without_lab' => $colleges->where('has_lab', 0)->count(),
                    'honours_colleges' => $colleges->filter(fn (Teacher $college): bool => $this->contains($college->college_course_type, ['honours', 'honors', 'অনার্স']))->count(),
                    'degree_colleges' => $colleges->filter(fn (Teacher $college): bool => $this->contains($college->college_course_type, ['degree', 'ডিগ্রি', 'ডিগ্রী']))->count(),
                    'government_colleges' => $colleges->filter(fn (Teacher $college): bool => $this->isGovernmentCollege($college->college_type))->count(),
                    'private_colleges' => $colleges->filter(fn (Teacher $college): bool => $this->contains($college->college_type, ['private', 'non-government', 'nongovernment', 'বেসরকারি']))->count(),
                    'colleges' => $colleges
                        ->sortBy([
                            ['college_name', 'asc'],
                            ['college_code', 'asc'],
                        ])
                        ->values(),
                ];
            })
            ->sortBy('district_name', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();
    }

    private function collegesQuery(): Builder
    {
        $labCondition = "MAX(CASE WHEN LOWER(TRIM(has_computer_lab)) IN ('yes', 'হ্যাঁ') THEN 1 ELSE 0 END)";

        return Teacher::query()
            ->select(
                'college_code',
                DB::raw('MAX(college_name) as college_name'),
                DB::raw("MAX(districts_name) as district_name"),
                DB::raw('MAX(upazilla) as college_upazilla'),
                DB::raw("{$labCondition} as has_lab"),
                DB::raw('MAX(computer_count) as computer_count'),
                DB::raw('MAX(course_type) as college_course_type'),
                DB::raw('MAX(col_type) as college_type'),
                DB::raw("SUM(CASE WHEN LOWER(TRIM(has_training)) IN ('yes', 'হ্যাঁ') THEN 1 ELSE 0 END) as trained_teachers"),
                DB::raw("SUM(CASE WHEN LOWER(TRIM(COALESCE(has_training, ''))) NOT IN ('yes', 'হ্যাঁ') THEN 1 ELSE 0 END) as untrained_teachers"),
            )
            ->where('div_name', $this->selectedDivision)
            ->whereNotNull('college_code')
            ->where('college_code', '!=', '')
            ->groupBy('college_code')
            ->orderBy('college_code');
    }

    /**
     * @param  array<int, string>  $needles
     */
    private function contains(?string $value, array $needles): bool
    {
        $normalizedValue = mb_strtolower(trim((string) $value));

        return collect($needles)->contains(
            fn (string $needle): bool => str_contains($normalizedValue, mb_strtolower($needle)),
        );
    }

    private function isGovernmentCollege(?string $collegeType): bool
    {
        if ($this->contains($collegeType, ['private', 'non-government', 'nongovernment', 'বেসরকারি'])) {
            return false;
        }

        return $this->contains($collegeType, ['government', 'govt', 'সরকারি']);
    }
}
