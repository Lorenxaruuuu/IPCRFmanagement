<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PerformanceController extends Controller
{
    /**
     * Display performance history page
     */
    public function index(Request $request)
    {
        // Sample data - replace with actual database query
        $performances = [
            (object)[
                'id' => 1,
                'date' => '02/25/2026',
                'semester' => 'First',
                'year' => '2026'
            ],
            (object)[
                'id' => 2,
                'date' => '11/25/2026',
                'semester' => 'Second',
                'year' => '2026'
            ]
        ];

        // Filter logic
        $selectedSemester = $request->get('semester', 'all');
        $selectedYear = $request->get('year', '2026');

        return view('performance', compact(
            'performances', 
            'selectedSemester', 
            'selectedYear'
        ));
    }

    /**
     * View specific performance record
     */
    public function show($id)
    {
        // Fetch and display specific performance record
        return view('performance', compact('id'));
    }

    /**
     * Download performance report
     */
    public function downloadReport(Request $request)
    {
        $semester = $request->get('semester', 'all');
        $year = $request->get('year', date('Y'));

        // Generate and return PDF/Excel report
        // Implementation depends on your reporting package (e.g., Laravel Excel, DomPDF)
        
        return response()->download(
            storage_path('app/reports/performance-report.pdf'),
            "Performance_Report_{$semester}_{$year}.pdf"
        );
    }
}