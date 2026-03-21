<?php

namespace App\Http\Controllers;

use App\Models\Ipcrf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IpcrfController extends Controller
{
    public function index(Request $request)
    {
        $totalUploaded = Ipcrf::count();
        $pendingReview = Ipcrf::where('status', 'Pending')->count();
        $completedToday = Ipcrf::whereDate('created_at', today())
            ->where('status', '!=', 'Pending')
            ->count();

        $query = Ipcrf::latest();
        
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('province', 'like', $searchTerm)
                  ->orWhere('municipality', 'like', $searchTerm)
                  ->orWhere('id', 'like', $searchTerm);
            });
        }
        
        $recentUploads = $query->take(10)->get();

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


    public function showList(Request $request)
    {
        $query = Ipcrf::latest();

        if ($request->filled('province')) {
            $query->where('province', $request->province);
        }

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('municipality', 'like', $searchTerm)
                  ->orWhere('id', 'like', $searchTerm);
            });
        }

        $ipcrfs = $query->paginate(10)->withQueryString();

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

            return redirect()->route('dashboard')->with('success', 'IPCRF uploaded successfully!');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Upload failed: ' . $e->getMessage())->withInput();
        }
    }
    public function show($id)
    {
        $ipcrf = Ipcrf::findOrFail($id);
        return view('ipcrfShow', compact('ipcrf'));
    }

    public function document($id)
    {
        $ipcrf = Ipcrf::findOrFail($id);
        
        if (!Storage::exists($ipcrf->scanned_file_path)) {
            abort(404);
        }

        return Storage::response($ipcrf->scanned_file_path);
    }
}
