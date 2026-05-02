<?php

namespace App\Http\Controllers;

use App\Models\TrendAnalysis;
use Illuminate\Http\JsonResponse;

class TrendAnalysisController extends Controller
{
    /**
     * Get latest trend analysis data for all funds
     * Only returns data for the latest analysis date across all funds
     * Filters out funds without the latest date analysis
     * 
     * @return JsonResponse
     */
    public function getLatestTrends(): JsonResponse
    {
        // Get the maximum (latest) analysis date from the entire dataset
        $latestDate = TrendAnalysis::max('analysis_date');
        
        if (!$latestDate) {
            return response()->json([
                'success' => false,
                'message' => 'No trend analysis data available',
                'data' => []
            ], 404);
        }
        
        // Get all trends for the latest date only
        // This automatically filters out funds that don't have analysis on the latest date
        $trends = TrendAnalysis::where('analysis_date', $latestDate)
            ->with(['fund' => function($q) {
                $q->select('code', 'name', 'category_id')->with(['category' => function($q2) {
                    $q2->select('id', 'name');
                }]);
            }])
            ->orderBy('fund_code', 'asc')
            ->select('fund_code', 'up_streak', 'down_streak', 'change_percent', 'last_price')
            ->get()
            ->map(function($trend) {
                // Determine streak direction: positive for up, negative for down
                $streak = $trend->up_streak > 0 ? $trend->up_streak : -$trend->down_streak;
                
                return [
                    'fund_code' => $trend->fund_code,
                    'fund_name' => $trend->fund ? $trend->fund->name : null,
                    'category_id' => $trend->fund ? $trend->fund->category_id : null,
                    'category_name' => ($trend->fund && $trend->fund->category) ? $trend->fund->category->name : 'Diğer',
                    'streak_days' => $streak,  // Positive = up days, Negative = down days
                    'change_percent' => (float) $trend->change_percent,
                    'last_price' => (float) $trend->last_price
                ];
            });
        
        return response()->json([
            'success' => true,
            'analysis_date' => $latestDate,  // Already a string from max()
            'total_funds' => $trends->count(),
            'data' => $trends->values()
        ]);
    }
}
