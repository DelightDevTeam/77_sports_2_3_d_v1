<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\TwoDLimit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TwoDLimitController extends Controller
{
    public function index()
    {
        $limits = TwoDLimit::all();

        return view('admin.two_limit.index', compact('limits'));
    }

    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'two_d_limit' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        // Store the new two_d_limit
        TwoDLimit::create([
            'two_d_limit' => $request->two_d_limit,
        ]);

        // Retrieve all users and update their limit
        $users = User::all();
        foreach ($users as $user) {
            $user->limit = $request->two_d_limit;
            $user->save(); // Save each user after updating the limit
        }

        // Redirect with a success message
        return redirect()->route('admin.two-digit-limit.index')->with('toast_success', 'two_d_limit created successfully.');
    }

    public function destroy($id)
    {
        $limit = TwoDLimit::findOrFail($id);
        $limit->delete();

        return redirect()->route('admin.two-digit-limit.index')->with('toast_success', 'Permission deleted successfully.');
    }
}
