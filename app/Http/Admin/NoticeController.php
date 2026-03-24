<?php
namespace App\Http\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use Illuminate\Http\Request;


class NoticeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $notices = Notice::active()->get();
            return response()->json($notices);
        }

        // fallback - redirect back to dashboard
        return redirect()->route('admin.dashboard');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'priority' => 'required|in:Low,Medium,High',
        ]);

        $notice = Notice::create([
            'subject' => $validated['subject'],
            'content' => $validated['content'],
            'priority' => $validated['priority'],
            'posted_by' => auth()->id() ?? null,
            'posted_at' => now(),
            'is_active' => true,
        ]);

        // Removed: No longer sending individual notifications to each user
        // This was creating duplicate entries (one per user)

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'notice' => $notice]);
        }

        return back()->with('success', 'Announcement posted');
    }

    public function destroy($id, Request $request)
    {
        $notice = Notice::findOrFail($id);
        $notice->update(['is_active' => false]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Announcement deleted');
    }
}