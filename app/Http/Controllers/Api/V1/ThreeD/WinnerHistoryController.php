<?php

namespace App\Http\Controllers\Api\V1\ThreeD;

use App\Http\Controllers\Controller;
use App\Models\ThreeDigit\FirstPrizeWinner;
use App\Models\ThreeDigit\SecondPrizeWinner;
use App\Models\ThreeDigit\ThirdPrizeWinner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WinnerHistoryController extends Controller
{
    public function firstPrizeWinner()
    {
        try {
            $winners = FirstPrizeWinner::with('user')->orderBy('id', 'desc')->get();
            $totalPrizeAmount = FirstPrizeWinner::sum('prize_amount');

            return response()->json([
                'success' => true,
                'message' => 'Prize winners fetched successfully.',
                'winners' => $winners,
                'totalPrizeAmount' => $totalPrizeAmount,
            ], 200);
        } catch (\Exception $e) {
            // Log the error or handle it appropriately
            return response()->json(['error' => 'Something went wrong while fetching prize winners.'], 500);
        }
    }

    public function secondPrizeWinner()
    {
        try {
            $winners = SecondPrizeWinner::with('user')->orderBy('id', 'desc')->get();
            $totalPrizeAmount = SecondPrizeWinner::sum('prize_amount');

            return response()->json([
                'success' => true,
                'message' => 'Prize winners fetched successfully.',
                'winners' => $winners,
                'totalPrizeAmount' => $totalPrizeAmount,
            ], 200);
        } catch (\Exception $e) {
            // Log the error or handle it appropriately
            return response()->json(['error' => 'Something went wrong while fetching prize winners.'], 500);
        }
    }

    public function thirdPrizeWinner()
    {
        try {
            $winners = ThirdPrizeWinner::with('user')->orderBy('id', 'desc')->get();
            $totalPrizeAmount = ThirdPrizeWinner::sum('prize_amount');

            return response()->json([
                'success' => true,
                'message' => 'Prize winners fetched successfully.',
                'winners' => $winners,
                'totalPrizeAmount' => $totalPrizeAmount,
            ], 200);
        } catch (\Exception $e) {
            // Log the error or handle it appropriately
            return response()->json(['error' => 'Something went wrong while fetching prize winners.'], 500);
        }
    }

    // auth winner data
    public function firstPrizeWinnerForApk()
    {
        try {
            $userId = Auth::id(); // Retrieve the authenticated user's ID

            $winners = FirstPrizeWinner::where('user_id', $userId)
                ->with('user') // Eager load the user relationship
                ->orderBy('id', 'desc')
                ->get();

            $totalPrizeAmount = FirstPrizeWinner::where('user_id', $userId)
                ->sum('prize_amount');

            return response()->json([
                'success' => true,
                'message' => 'Prize winners fetched successfully.',
                'winners' => $winners,
                'totalPrizeAmount' => $totalPrizeAmount,
            ], 200);
        } catch (\Exception $e) {
            // Log the error or handle it appropriately
            return response()->json(['error' => 'Something went wrong while fetching prize winners.'], 500);
        }
    }

    public function secondPrizeWinnerForApk()
    {
        try {
            $userId = Auth::id(); // Retrieve the authenticated user's ID

            $winners = SecondPrizeWinner::where('user_id', $userId)
                ->with('user') // Eager load the user relationship
                ->orderBy('id', 'desc')
                ->get();

            $totalPrizeAmount = SecondPrizeWinner::where('user_id', $userId)
                ->sum('prize_amount');

            return response()->json([
                'success' => true,
                'message' => 'Prize winners fetched successfully.',
                'winners' => $winners,
                'totalPrizeAmount' => $totalPrizeAmount,
            ], 200);
        } catch (\Exception $e) {
            // Log the error or handle it appropriately
            return response()->json(['error' => 'Something went wrong while fetching prize winners.'], 500);
        }
    }

    public function thirdPrizeWinnerForApk()
    {
        try {
            $userId = Auth::id(); // Retrieve the authenticated user's ID

            $winners = ThirdPrizeWinner::where('user_id', $userId)
                ->with('user') // Eager load the user relationship
                ->orderBy('id', 'desc')
                ->get();

            $totalPrizeAmount = ThirdPrizeWinner::where('user_id', $userId)
                ->sum('prize_amount');

            return response()->json([
                'success' => true,
                'message' => 'Prize winners fetched successfully.',
                'winners' => $winners,
                'totalPrizeAmount' => $totalPrizeAmount,
            ], 200);
        } catch (\Exception $e) {
            // Log the error or handle it appropriately
            return response()->json(['error' => 'Something went wrong while fetching prize winners.'], 500);
        }
    }
}
