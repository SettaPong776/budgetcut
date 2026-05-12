<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Requester;

class RequesterController extends Controller
{
    public function index()
    {
        $requesters = Requester::orderBy('name')->get();
        return view('requesters.index', compact('requesters'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:requesters,name',
        ]);

        Requester::create($data);
        return back()->with('success', 'เพิ่มชื่อผู้ขอกันสำเร็จ!');
    }

    public function destroy(Requester $requester)
    {
        $requester->delete();
        return back()->with('success', 'ลบชื่อผู้ขอกันสำเร็จ!');
    }

    // API endpoint for autocomplete
    public function api()
    {
        return response()->json(Requester::orderBy('name')->pluck('name'));
    }
}
