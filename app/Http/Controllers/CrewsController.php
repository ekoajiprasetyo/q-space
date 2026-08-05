<?php

namespace App\Http\Controllers;

use App\Models\CoreClassRoom;
use App\Models\CoreStudent;
use Illuminate\Http\Request;

class CrewsController extends Controller
{
    public function index()
    {
        $classes = CoreClassRoom::query()
            ->where('is_active', true)
            ->orderBy('grade')
            ->orderBy('name')
            ->get(['id', 'name', 'grade', 'academic_year']);

        return view('crews.index', compact('classes'));
    }

    public function importStudents(Request $request)
    {
        $validated = $request->validate([
            'class_ids' => ['required', 'array', 'min:1'],
            'class_ids.*' => ['integer', 'distinct'],
        ]);

        $classes = CoreClassRoom::query()
            ->where('is_active', true)
            ->whereIn('id', $validated['class_ids'])
            ->orderBy('grade')
            ->orderBy('name')
            ->get(['id', 'name']);

        abort_unless($classes->count() === count($validated['class_ids']), 404);

        $students = CoreStudent::query()
            ->whereIn('class_id', $classes->pluck('id'))
            ->where('is_active', true)
            ->whereNotNull('nickname')
            ->where('nickname', '<>', '')
            ->orderBy('class_id')
            ->orderBy('nickname')
            ->get(['nickname', 'gender'])
            ->map(fn (CoreStudent $student) => [
                'name' => trim($student->nickname),
                'gender' => $this->normalizeGender($student->gender),
            ])
            ->filter(fn (array $student) => $student['name'] !== '')
            ->unique(fn (array $student) => mb_strtolower($student['name']))
            ->values();

        return response()->json([
            'class_names' => $classes->pluck('name')->values(),
            'students' => $students,
        ]);
    }

    private function normalizeGender(?string $gender): string
    {
        return match (mb_strtolower(trim((string) $gender))) {
            'l', 'laki-laki', 'laki laki', 'male' => 'L',
            'p', 'perempuan', 'female' => 'P',
            default => '',
        };
    }
}
