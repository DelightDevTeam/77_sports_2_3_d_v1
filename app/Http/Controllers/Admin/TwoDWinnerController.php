<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Admin\TwodWiner;
use App\Http\Controllers\Controller;

class TwoDWinnerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        date_default_timezone_set('Asia/Yangon');
    }

    public function index()
    {

        $morningData = TwodWiner::where('session', 'morning')->orderBy('id', 'desc')->first();
        $eveningData = TwodWiner::where('session', 'evening')->orderBy('id', 'desc')->first();

        return view('admin.two_d.prize_index', compact('morningData', 'eveningData'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     //
    //     $currentSession = date('H') < 12 ? 'morning' : 'evening';  // before 1 pm is morning

    //     TwodWiner::create([
    //         'prize_no' => $request->prize_no,
    //         'session' => $currentSession,
    //     ]);

    //     return redirect()->back()->with('success', 'Two Digit Lottery Winner Added Successfully');
    // }
    public function store(Request $request)
{
    // Get the current time in the 'Asia/Yangon' time zone
    $currentTime = Carbon::now('Asia/Yangon')->format('H:i:s');

    // Determine the current session based on the current time
    $currentSession = Carbon::now('Asia/Yangon')->format('H:i') < '12:30' ? 'morning' : 'evening';

    // Create a new TwodWiner entry
    TwodWiner::create([
        'prize_no' => $request->prize_no,
        'session' => $currentSession,
    ]);

    // Redirect back with a success message
    return redirect()->back()->with('success', 'ထွက်ဂဏန်းထဲ့သွင်းမှု့အောင်မြင်ပါသည်။');
}


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
