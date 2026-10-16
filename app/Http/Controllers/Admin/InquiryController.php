<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;

class InquiryController extends Controller
{
    public function index()
    {
        return view('admin.inquiries.index', ['items' => Inquiry::latest()->paginate(20)]);
    }

    public function show(Inquiry $item)
    {
        if (! $item->is_read) {
            $item->update(['is_read' => true]);
        }

        return view('admin.inquiries.show', ['item' => $item]);
    }

    public function toggleRead(Inquiry $item)
    {
        $item->update(['is_read' => ! $item->is_read]);

        return back();
    }

    public function destroy(Inquiry $item)
    {
        $item->delete();

        return redirect()->route('admin.inquiries.index')->with('status', 'Message deleted.');
    }
}