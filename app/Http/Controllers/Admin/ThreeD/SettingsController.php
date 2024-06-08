<?php

namespace App\Http\Controllers\Admin\ThreeD;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\ThreeDigit\Prize;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\ThreeDigit\Permutation;
use App\Models\ThreeDigit\ThreedSetting;

class SettingsController extends Controller
{
    public function index()
    {
        // Get the current year and month
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        // Get result dates for the current month
        $currentMonthResultDates = ThreedSetting::whereYear('result_date', $currentYear)
            ->whereMonth('result_date', $currentMonth)
            ->get();

        // Determine the next month and year
        $nextMonth = ($currentMonth % 12) + 1; // Calculate the next month (1-12, looping back to 1 after 12)
        $nextMonthYear = ($nextMonth == 1) ? $currentYear + 1 : $currentYear; // Increment year if it's January

        // Get the first result date of the next month
        $firstResultDateNextMonth = ThreedSetting::whereYear('result_date', $nextMonthYear)
            ->whereMonth('result_date', $nextMonth)
            ->orderBy('result_date', 'asc')
            ->first();

        // Merge the current month results with the first result date of the next month
        $results = $currentMonthResultDates->merge(collect([$firstResultDateNextMonth]));

        // Log the result dates for debugging
        //Log::info('Result dates including the current month and the first game of the next month:', ['resultDates' => $results]);

        //Get the latest prize where status is open
        $lasted_prizes = ThreedSetting::where('status', 'open')
            ->orderBy('result_date', 'desc') // Ensure to get the latest result date
            ->first();
        // Retrieve permutation digits and the latest prize
        $permutation_digits = Permutation::all();
        $three_digits_prize = Prize::orderBy('id', 'desc')->first();

        // Return the view with the required data
        return view('admin.three_d.setting.index', compact('results', 'lasted_prizes', 'permutation_digits', 'three_digits_prize'));
    }

    public function getCurrentMonthResultsSetting()
    {
        $currentMonthStart = Carbon::now()->startOfMonth();

        // Get the end of the next month
        $nextMonthEnd = Carbon::now()->addMonth()->endOfMonth();

        // Retrieve all records within the current month and next month
        $results = ThreedSetting::whereBetween('result_date', [$currentMonthStart, $nextMonthEnd])
            ->orderBy('result_date', 'asc') // Optional: order by date
            ->get();

        // Return the data to the view or as a JSON response
        return view('admin.three_d.setting.more_setting', ['results' => $results]);
    }

    public function updateStatus(Request $request, $id)
    {
        // Get the new status with a fallback default
        $newStatus = $request->input('status', 'closed'); // Default to 'closed' if not provided

        // Find the existing record and update the status
        $result = ThreedSetting::findOrFail($id);

        // Ensure the status is not NULL before updating
        if (is_null($newStatus)) {
            return redirect()->back()->with('error', 'Status cannot be null');
        }

        $result->status = $newStatus;
        $result->save();

        return redirect()->back()->with('success', "Status changed to '{$newStatus}' successfully.");
    }

    public function updateResultNumber(Request $request, $id)
    {
        $result_number = $request->input('result_number'); // The new status

        // Find the result by ID
        $result = ThreedSetting::findOrFail($id);

        // Update the status
        $result->result_number = $result_number;
        $result->save();

        // Return a response (like a JSON object)
        return redirect()->back()->with('success', 'Result number updated successfully.'); // Redirect back with success message
    }

    public function PermutationStore(Request $request)
    {
        // Logic to store permutations in the database
        if ($request->has('permutations')) {
            foreach ($request->permutations as $permutation) {
                Permutation::create(['digit' => $permutation]);
            }

            return redirect()->back()->with('success', 'Permutations stored successfully.');
        } else {
            return redirect()->back()->with('error', 'No permutations to store.');
        }
    }

    // deletePermutation
    public function deletePermutation($id)
    {
        $permutation = Permutation::find($id);
        if ($permutation) {
            $permutation->delete();

            return redirect()->back()->with('success', 'Permutation deleted successfully.');
        } else {
            return redirect()->back()->with('error', 'Permutation not found.');
        }
    }

    public function PermutationReset()
    {
        Permutation::truncate();
        session()->flash('SuccessRequest', 'Successfully 3D Permutation Reset.');

        return redirect()->back()->with('message', 'Data reset successfully!');
    }

    public function store(Request $request)
    {
        //
        //$currentSession = date('H') < 12 ? 'morning' : 'evening';  // before 1 pm is morning

        Prize::create([
            'prize_one' => $request->prize_one,
            'prize_two' => $request->prize_two,
            //'session' => $currentSession,
        ]);
        session()->flash('SuccessRequest', 'Three Digit Lottery Prize Number Created Successfully');

        return redirect()->back()->with('success', 'Three Digit Lottery Prize Number Created Successfully');
    }

    public function destroy(string $id)
    {
        $digit = Prize::find($id);
        $digit->delete();
        session()->flash('SuccessRequest', 'Three Digit Lottery Prize Number Deleted Successfully');

        return redirect()->back()->with('success', 'Three Digit Lottery Prize Number Deleted Successfully');
    }
}
