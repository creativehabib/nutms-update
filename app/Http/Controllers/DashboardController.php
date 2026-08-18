<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $colleges = Teacher::query()
            ->selectRaw("college_code, MAX(CASE WHEN LOWER(has_computer_lab) = 'yes' THEN 1 ELSE 0 END) as has_lab")
            ->selectRaw('MAX(COALESCE(computer_count, 0)) as computer_count')
            ->selectRaw("MAX(CASE WHEN LOWER(TRIM(COALESCE(course_type, ''))) LIKE '%honours%' OR LOWER(TRIM(COALESCE(course_type, ''))) LIKE '%honors%' OR course_type LIKE '%অনার্স%' THEN 1 ELSE 0 END) as has_honours")
            ->selectRaw("MAX(CASE WHEN LOWER(TRIM(COALESCE(course_type, ''))) LIKE '%degree%' OR course_type LIKE '%ডিগ্রি%' OR course_type LIKE '%ডিগ্রী%' THEN 1 ELSE 0 END) as has_degree")
            ->selectRaw("MAX(CASE WHEN LOWER(TRIM(COALESCE(col_type, ''))) LIKE '%government%' OR LOWER(TRIM(COALESCE(col_type, ''))) LIKE '%govt%' OR col_type LIKE '%সরকারি%' THEN 1 ELSE 0 END) as is_government")
            ->selectRaw("MAX(CASE WHEN LOWER(TRIM(COALESCE(col_type, ''))) LIKE '%private%' OR LOWER(TRIM(COALESCE(col_type, ''))) LIKE '%non-government%' OR LOWER(TRIM(COALESCE(col_type, ''))) LIKE '%nongovernment%' OR col_type LIKE '%বেসরকারি%' THEN 1 ELSE 0 END) as is_private")
            ->whereNotNull('college_code')
            ->where('college_code', '!=', '')
            ->groupBy('college_code');

        $collegeReport = DB::query()
            ->fromSub($colleges, 'colleges')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN has_lab = 1 THEN 1 ELSE 0 END) as with_lab')
            ->selectRaw('SUM(CASE WHEN has_lab = 0 THEN 1 ELSE 0 END) as without_lab')
            ->selectRaw('SUM(CASE WHEN has_lab = 1 THEN computer_count ELSE 0 END) as total_computers')
            ->selectRaw('SUM(has_honours) as honours_colleges')
            ->selectRaw('SUM(has_degree) as degree_colleges')
            ->selectRaw('SUM(CASE WHEN is_government = 1 AND is_private = 0 THEN 1 ELSE 0 END) as government_colleges')
            ->selectRaw('SUM(is_private) as private_colleges')
            ->first();

        $teacherReport = Teacher::query()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN ict_training_name IS NOT NULL AND ict_training_name != '' THEN 1 ELSE 0 END) as with_ict_training")
            ->selectRaw("SUM(CASE WHEN ict_training_name IS NULL OR ict_training_name = '' THEN 1 ELSE 0 END) as without_ict_training")
            ->selectRaw('MAX(updated_at) as last_updated_at')
            ->first();

        $totalColleges = (int) ($collegeReport?->total ?? 0);
        $collegesWithLab = (int) ($collegeReport?->with_lab ?? 0);
        $totalTeachers = (int) ($teacherReport?->total ?? 0);
        $teachersWithIctTraining = (int) ($teacherReport?->with_ict_training ?? 0);

        return view('dashboard', [
            'report' => [
                'collegesWithLab' => $collegesWithLab,
                'collegesWithoutLab' => (int) ($collegeReport?->without_lab ?? 0),
                'totalColleges' => $totalColleges,
                'honoursColleges' => (int) ($collegeReport?->honours_colleges ?? 0),
                'degreeColleges' => (int) ($collegeReport?->degree_colleges ?? 0),
                'governmentColleges' => (int) ($collegeReport?->government_colleges ?? 0),
                'privateColleges' => (int) ($collegeReport?->private_colleges ?? 0),
                'totalComputers' => (int) ($collegeReport?->total_computers ?? 0),
                'labCoverage' => $this->percentage($collegesWithLab, $totalColleges),
                'teachersWithIctTraining' => $teachersWithIctTraining,
                'teachersWithoutIctTraining' => (int) ($teacherReport?->without_ict_training ?? 0),
                'totalTeachers' => $totalTeachers,
                'ictTrainingCoverage' => $this->percentage($teachersWithIctTraining, $totalTeachers),
                'lastUpdatedAt' => $teacherReport?->last_updated_at
                    ? Carbon::parse($teacherReport->last_updated_at)->format('d M Y, h:i A')
                    : null,
            ],
        ]);
    }

    private function percentage(int $value, int $total): float
    {
        return $total > 0 ? round(($value / $total) * 100, 1) : 0;
    }
}
