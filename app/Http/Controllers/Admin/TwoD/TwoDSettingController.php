<?php

namespace App\Http\Controllers\Admin\TwoD;

use App\Helpers\SessionHelper;
use App\Http\Controllers\Controller;
use App\Models\Admin\CloseTwoDigit;
use App\Models\Admin\HeadDigit;
use App\Models\Admin\TwoDLimit;
use App\Models\TwoD\LotteryTwoDigitPivot;
use App\Models\TwoD\TwodSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TwoDSettingController extends Controller
{
    public function index()
    {
        // Get today's date
        $today = Carbon::now()->format('Y-m-d');

        // Retrieve the latest result for today's morning session
        $morningSession = TwodSetting::where('result_date', $today)
            ->where('session', 'morning')
            ->first();

        // Retrieve the latest result for today's evening session
        $eveningSession = TwodSetting::where('result_date', $today)
            ->where('session', 'evening')
            ->first();

        return view('admin.two_d.setting.index', [
            'morningSession' => $morningSession,
            'eveningSession' => $eveningSession,
        ]);
    }

    public function getCurrentMonthSettings()
    {
        // Get the start and end of the current month
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        // Retrieve all records within the current month
        $results = TwodSetting::whereBetween('result_date', [$currentMonthStart, $currentMonthEnd])
            ->orderBy('result_date', 'asc') // Optional: order by date
            ->get();

        // Return the data to the view or as a JSON response
        return view('admin.two_d.setting.more_setting', ['results' => $results]);
    }

    public function updateStatus(Request $request, $id)
    {
        //dd($request->all());
        $status = $request->input('status'); // The new status
        //dd($status);
        // Find the result by ID
        $result = TwodSetting::findOrFail($id);

        // Update the status
        $result->status = $status;
        $result->save();
        session()->flash('SuccessRequest', '2D Open/Close Status updated successfully');

        return redirect()->back()->with('success', '2D Open/Close Status updated successfully.'); // Redirect back with success message

    }

    public function updateStatusEvening(Request $request, $id)
    {
        //dd($request->all());
        $status = $request->input('status'); // The new status
        //dd($status);
        // Find the result by ID
        $result = TwodSetting::findOrFail($id);

        // Update the status
        $result->status = $status;
        $result->save();
        session()->flash('SuccessRequestEvening', '2D Open/Close Status updated successfully');

        return redirect()->back()->with('success', '2D Open/Close Status updated successfully.'); // Redirect back with success message

    }

    public function updateResultNumber(Request $request, $id)
    {
        $result_number = $request->input('result_number'); // The new status

        // Find the result by ID
        $result = TwodSetting::findOrFail($id);

        // Update the status
        $result->result_number = $result_number;
        $result->save();

        $today = Carbon::today();
        $session = SessionHelper::getCurrentSession();
        $twod_data = LotteryTwoDigitPivot::where('res_date', $today)
            ->where('session', $session)->get();

        foreach ($twod_data as $twod) {
            $twod->update(['win_lose' => 1]);
        }

        // Return a response (like a JSON object)
        return redirect()->back()->with('success', 'Result number updated successfully.'); // Redirect back with success message
    }

    public function closetwoDigitindex()
    {
        $digits = CloseTwoDigit::all();

        return view('admin.two_d.two_digit_close.index', compact('digits'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'digit' => 'required|numeric',
            ]);

        if ($validator->fails()) {
            return back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        // store
        CloseTwoDigit::create([
            'digit' => $request->digit,
        ]);

        return redirect()->route('admin.two-digit-close')->with('toast_success', 'CloseTwoDigit created successfully.');

    }

    public function destroy($id)
    {
        $limit = CloseTwoDigit::findOrFail($id);
        $limit->delete();

        return redirect()->route('admin.two-digit-close')->with('toast_success', 'CloseTwoDigit deleted successfully.');
    }

    public function HeadDigitindex()
    {
        $digits = HeadDigit::all();

        return view('admin.two_d.head_digit.index', compact('digits'));
    }

    public function HeadDigitstore(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'digit_one' => 'required|numeric',
                'digit_two' => 'required|numeric',
                'digit_three' => 'required|numeric',

                //'body' => 'required|min:3'
            ]);

        if ($validator->fails()) {
            return back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        // store
        HeadDigit::create([
            'digit_one' => $request->digit_one,
            'digit_two' => $request->digit_two,
            'digit_three' => $request->digit_three,
        ]);

        return redirect()->route('admin.two-digit-close-head')->with('toast_success', 'HeadDigit created successfully.');

    }

    public function HeadDigitdestroy($id)
    {
        $limit = HeadDigit::findOrFail($id);
        $limit->delete();

        return redirect()->route('admin.two-digit-close-head')->with('toast_success', 'HeadDigit deleted successfully.');
    }

    public function Limitindex()
    {
        $limits = TwoDLimit::all();

        return view('admin.two_d.default_limit.index', compact('limits'));
    }

    public function Limitstore(Request $request)
    {
        //dd($request->all());
        $validator = Validator::make($request->all(), [
            'two_d_limit' => 'required',

            //'body' => 'required|min:3'
        ]);

        if ($validator->fails()) {
            return back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        // store
        TwoDLimit::create([
            'two_d_limit' => $request->two_d_limit,
        ]);

        // redirect
        return redirect()->route('admin.default2dLimit')->with('toast_success', 'two_d_limit created successfully.');
    }

    public function Limitdestroy($id)
    {
        $limit = TwoDLimit::findOrFail($id);
        $limit->delete();

        return redirect()->route('admin.default2dLimit')->with('toast_success', 'Permission deleted successfully.');
    }
}
