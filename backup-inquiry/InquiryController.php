<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter') === 'unread' ? 'unread' : 'all';

        $inquiries = Inquiry::query()
            ->when($filter === 'unread', fn ($q) => $q->unread())
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.inquiries.index', [
            'inquiries' => $inquiries,
            'filter'    => $filter,
            'counts'    => [
                'all'    => Inquiry::count(),
                'unread' => Inquiry::unread()->count(),
            ],
        ]);
    }

    public function show(Inquiry $inquiry)
    {
        if (! $inquiry->is_read) {
            $inquiry->update(['is_read' => true]);
        }

        return view('admin.inquiries.show', ['inquiry' => $inquiry]);
    }

    public function toggleRead(Inquiry $inquiry)
    {
        $inquiry->update(['is_read' => ! $inquiry->is_read]);

        return back()->with('status', $inquiry->is_read ? 'Marked as read.' : 'Marked as unread.');
    }

    public function destroy(Inquiry $inquiry)
    {
        $inquiry->delete();

        return redirect()->route('admin.inquiries.index')->with('status', 'Message deleted.');
    }
}