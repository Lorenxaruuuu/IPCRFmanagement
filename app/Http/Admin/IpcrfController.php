<?php

namespace App\Http\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ipcrf;
use App\Models\IpcrfRecord;
use App\Models\Employee;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class IpcrfController extends Controller
{
    public function index()
    {
        $totalUploaded = Ipcrf::count();
        $pendingReview = Ipcrf::where('status', 'Pending')->count();
        $completedToday = Ipcrf::whereDate('created_at', today())
            ->where('status', '!=', 'Pending')
            ->count();

        $recentUploads = Ipcrf::latest()->take(10)->get();

        // Growth percentage
        $todayCount = Ipcrf::whereDate('created_at', today())->count();
        $yesterdayCount = Ipcrf::whereDate('created_at', today()->subDay())->count();

        $growthPercentage = $yesterdayCount > 0 
            ? (($todayCount - $yesterdayCount) / $yesterdayCount) * 100 
            : ($todayCount > 0 ? 100 : 0);

        return view('encoderDashboard', compact(
            'totalUploaded',
            'pendingReview',
            'completedToday',
            'recentUploads',
            'growthPercentage'
        ));
    }


    public function showList()
    {
        $ipcrfs = Ipcrf::latest()->paginate(10);

        return view('index', compact('ipcrfs'));
    }

    public function create()
    {
        // Provinces data for the dropdowns
        $provinces = [
            [
                'name' => "Davao de Oro",
                'municipalities' => ["Compostela", "Laak", "Mabini", "Maco", "Maragusan", "Mawab", "Monkayo", "Montevista", "Nabunturan", "New Bataan", "Pantukan"]
            ],
            [
                'name' => "Davao del Norte",
                'municipalities' => ["Asuncion", "Braulio E. Dujali", "Carmen", "Kapalong", "New Corella", "San Isidro", "Santo Tomas", "Talaingod"]
            ],
            [
                'name' => "Davao del Sur",
                'municipalities' => ["Bansalan", "Davao City", "Hagonoy", "Kiblawan", "Magsaysay", "Malalag", "Matanao", "Padada", "Santa Cruz", "Sulop"]
            ],
            [
                'name' => "Davao Occidental",
                'municipalities' => ["Don Marcelino", "Jose Abad Santos", "Malita", "Santa Maria", "Sarangani"]
            ],
            [
                'name' => "Davao Oriental",
                'municipalities' => ["Baganga", "Banaybanay", "Boston", "Caraga", "Cateel", "Governor Generoso", "Lupon", "Manay, San Isidro", "Tarragona"]
            ]
        ];

        return view('wizard', compact('provinces'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'province' => 'required|string',
            'municipality' => 'required|string',
            'name' => 'required|string|max:255',
            'scanned_file' => 'required|file|mimes:pdf,jpg,png|max:10240',
        ]);

        try {
            $scannedPath = $request->file('scanned_file')->store('ipcrfs/scanned');

            Ipcrf::create([
                'name' => $validated['name'],
                'province' => $validated['province'],
                'municipality' => $validated['municipality'],
                'scanned_file_path' => $scannedPath,
                'status' => 'Saved to Drive',
            ]);

            return redirect()->route('dashboards')->with('success', 'IPCRF uploaded successfully!');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Upload failed: ' . $e->getMessage())->withInput();
        }
    }
     public function dashboard()
    {
        $stats = [
            // total records (could be > employees if multiple uploads per person)
            'total_uploaded' => IpcrfRecord::count(),
            // unique employees who have uploaded at least once
            'uploaded_employees' => IpcrfRecord::distinct('employee_id')->count('employee_id'),
            'active_forms' => IpcrfRecord::where('status', 'Saved')->count(),
            'notices' => \App\Models\Notice::where('is_active', true)->count(),
            'total_employees' => \App\Models\Employee::count(),
        ];
        
        $recentSubmissions = IpcrfRecord::with('employee.school.municipality')
            ->latest('uploaded_at')
            ->take(10)
            ->get();

        // …existing extra employees logic unchanged…

        // if there aren't ten records yet, add some employees who have no record
        if ($recentSubmissions->count() < 10) {
            $needed = 10 - $recentSubmissions->count();
            $idsWith = $recentSubmissions->pluck('employee_id')->filter()->unique();
            $extras = \App\Models\Employee::whereNotIn('id', $idsWith)
                ->take($needed)
                ->get();
            foreach ($extras as $e) {
                $fake = new IpcrfRecord();
                $fake->setRelation('employee', $e);
                $fake->uploaded_at = null;
                $fake->status = 'No Record';
                $recentSubmissions->push($fake);
            }
        }
        
        $provinces = Province::where('region', 'Region 11')->get();

        $announcements = \App\Models\Notice::with('poster')
            ->where('is_active', true)
            ->latest('posted_at')
            ->take(5)
            ->get();
            
        return view('admin.dashboard', compact('stats', 'recentSubmissions', 'provinces', 'announcements'));
    }

    // Direct upload form - NO role selection step
    public function uploadForm()
    {
        // originally the dropdown was limited to Region 11 which in our
        // development database happens to contain *no* rows. as a result
        // the view would render an empty select and the javascript never
        // made a request to the API, leading to the impression that "no
        // provinces/municipalities are returned". load all provinces so
        // the UI has something to work with (or change the filter to a
        // valid region name).
        $provinces = Province::all();
        
        return view('admin.upload', compact('provinces'));
    }

    public function store2(Request $request)
    {
        try {
            // field names now match what the upload form sends. previous mismatches
            // were causing validation to silently fail and might make it seem like
            // "nothing is returned from the database" when attempting to store.
            $validated = $request->validate([
                'employee_id' => 'required|string|max:255',
                'employee_name' => 'required|string|max:255',
                'province_id' => 'required|exists:provinces,id',
                'municipality_id' => 'required|exists:municipalities,id',
                'school_id' => 'required|exists:schools,id',
                'file' => 'required|file|mimes:pdf,xlsx,xls,doc,docx|max:10240',
                'semester' => 'required|in:1st,2nd',
                'school_year' => 'required|string',
                'role' => 'required|in:Teacher,Master Teacher,Principal,Supervisor',
            ]);

            // we already have a school id from the dropdown; just load it.
            $school = \App\Models\School::findOrFail($validated['school_id']);

            // look up or create employee by employee_id (assumed unique identifier)
            $employee = \App\Models\Employee::where('employee_id', $validated['employee_id'])->first();
            if (!$employee) {
                // parse simple first/last from name
                $parts = explode(' ', $validated['employee_name'], 2);
                $first = $parts[0];
                $last = $parts[1] ?? '';
                $employee = \App\Models\Employee::create([
                    'employee_id' => $validated['employee_id'],
                    'first_name' => $first,
                    'last_name' => $last,
                    'school_id' => $school->id,
                    'role' => $validated['role'],
                    'email' => $validated['employee_id'] . '@example.com',
                ]);
            } else {
                // if user supplied a name but the id already exists, we'll return a warning
                if (trim($validated['employee_name']) !== '' && ($employee->first_name . ' ' . $employee->last_name) !== $validated['employee_name']) {
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json(['success' => true, 'message' => 'Employee ID already exists, using existing record.']);
                    }
                    // continue after warning; employee record will still be used
                    session()->flash('warning', 'Employee ID already exists, using existing record.');
                }

                // update school if changed
                if ($employee->school_id !== $school->id) {
                    $employee->school_id = $school->id;
                    $employee->save();
                }
            }

            $file = $request->file('file');
            $path = $file->store('ipcrf_records', 'private');
            
            IpcrfRecord::create([
                'employee_id' => $employee->id,
                // allow null when not authenticated
                'uploaded_by' => auth()->id(),
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'semester' => $validated['semester'],
                'school_year' => $validated['school_year'],
                'role' => $validated['role'], 
                'status' => 'Saved',
                'uploaded_at' => now(),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true]);
            }

            return redirect()->route('admin.records')->with('success', 'IPCRF uploaded successfully');
        } catch (\Throwable $e) {
            // log for diagnostics
            \Log::error('IPCRF upload error: '.$e->getMessage(), ['exception' => $e]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage(), 'errors' => $e instanceof \Illuminate\Validation\ValidationException ? $e->errors() : []], 422);
            }

            // return to form with warning so user sees message in UI
            return redirect()->back()->with('warning', $e->getMessage())->withInput();
        }
    }

    public function records(Request $request)
    {
        // base query for existing records
        $recordQuery = IpcrfRecord::with(['employee.school.municipality.province']);
        
        if ($request->filled('province')) {
            $recordQuery->whereHas('employee.school.municipality.province', function($q) use ($request) {
                $q->where('id', $request->province);
            });
        }
        
        if ($request->filled('municipality')) {
            $recordQuery->whereHas('employee.school.municipality', function($q) use ($request) {
                $q->where('id', $request->municipality);
            });
        }

        if ($request->filled('semester')) {
            $recordQuery->where('semester', $request->semester);
        }

        if ($request->filled('year')) {
            $recordQuery->where('school_year', $request->year);
        }

        if ($request->filled('employee_id')) {
            $recordQuery->whereHas('employee', function($q) use ($request) {
                $q->where('employee_id', 'like', '%' . $request->employee_id . '%');
            });
        }

        // get records first
        $recordsCollection = $recordQuery->latest('uploaded_at')->get();

        // now append employees without any record (subject to same province/municipality filters)
        $needed = 20 - $recordsCollection->count();
        if ($needed > 0) {
            $empQuery = \App\Models\Employee::with('school.municipality.province')
                ->whereDoesntHave('ipcrfRecords');

            if ($request->filled('province')) {
                $empQuery->whereHas('school.municipality.province', function($q) use ($request) {
                    $q->where('id', $request->province);
                });
            }
            if ($request->filled('municipality')) {
                $empQuery->whereHas('school.municipality', function($q) use ($request) {
                    $q->where('id', $request->municipality);
                });
            }

            if ($request->filled('employee_id')) {
                $empQuery->where('employee_id', 'like', '%' . $request->employee_id . '%');
            }

            $extras = $empQuery->take($needed)->get();
            foreach ($extras as $e) {
                $fake = new IpcrfRecord();
                $fake->setRelation('employee', $e);
                $fake->uploaded_at = null;
                $fake->status = 'No Record';
                $recordsCollection->push($fake);
            }
        }

        // create paginator manually (ensures filters preserved)
        $page = $request->input('page', 1);
        $perPage = 20;
        $slice = $recordsCollection->slice(($page - 1) * $perPage, $perPage);
        $records = new \Illuminate\Pagination\LengthAwarePaginator(
            $slice->values(),
            $recordsCollection->count(),
            $perPage,
            $page,
            ['path' => url()->current(), 'query' => $request->query()]
        );

        $provinces = Province::where('region', 'Region 11')->get();
        
        // return JSON for AJAX requests (dashboard filtering)
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'records' => $records->items(),
                'pagination' => [
                    'total' => $records->total(),
                    'per_page' => $records->perPage(),
                    'current_page' => $records->currentPage(),
                    'last_page' => $records->lastPage(),
                ],
            ]);
        }
        
        return view('admin.records', compact('records', 'provinces'));
    }

    public function download($id)
    {
        $record = IpcrfRecord::findOrFail($id);
        return Storage::disk('private')->download($record->file_path, $record->file_name);
    }

    // use implicit/explicit model binding for clarity; variable names now
    // match the route parameters and we return related collections directly
    public function getMunicipalities(\App\Models\Province $province)
    {
        // eager-load relation if needed, but a simple query is fine too
        return response()->json($province->municipalities()->get());
    }

    public function getSchools(\App\Models\Municipality $municipality)
    {
        return response()->json($municipality->schools()->get());
    }
}
