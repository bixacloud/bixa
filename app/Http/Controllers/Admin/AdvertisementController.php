<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\AdSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdvertisementController extends Controller
{
    /**
     * Display a listing of advertisements
     */
    public function index()
    {
        $advertisements = Advertisement::with('slot')->get();
        return view('admin.advertisements.index', compact('advertisements'));
    }

    /**
     * Show the form for creating a new advertisement
     */
    public function create()
    {
        $slots = AdSlot::where('is_active', true)->get();
        return view('admin.advertisements.create', compact('slots'));
    }

    /**
     * Store a newly created advertisement
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'html_content' => 'required|string',
            'slot_position' => 'required|string|exists:ad_slots,code',
            'is_active' => 'sometimes|boolean'
        ]);

        // Set default values if not provided
        $validated['is_active'] = $request->has('is_active') ? true : false;

        Advertisement::create($validated);

        return redirect()->route('admin.advertisements.index')
            ->with('success', 'Advertisement created successfully.');
    }

    /**
     * Show the form for editing the advertisement
     */
    public function edit(Advertisement $advertisement)
    {
        $slots = AdSlot::where('is_active', true)->get();
        return view('admin.advertisements.edit', compact('advertisement', 'slots'));
    }

    /**
     * Update the advertisement
     */
    public function update(Request $request, Advertisement $advertisement)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'html_content' => 'required|string',
            'slot_position' => 'required|string|exists:ad_slots,code',
            'is_active' => 'sometimes|boolean'
        ]);

        // Set default values if not provided
        $validated['is_active'] = $request->has('is_active') ? true : false;

        $advertisement->update($validated);

        return redirect()->route('admin.advertisements.index')
            ->with('success', 'Advertisement updated successfully.');
    }

    /**
     * Remove the advertisement
     */
    public function destroy(Advertisement $advertisement)
    {
        $advertisement->delete();

        return redirect()->route('admin.advertisements.index')
            ->with('success', 'Advertisement deleted successfully.');
    }

    /**
     * Record an impression
     */
    public function recordImpression(Advertisement $advertisement)
    {
        $advertisement->recordImpression();
        return response()->json(['success' => true]);
    }

    /**
     * Record a click
     */
    public function recordClick(Advertisement $advertisement)
    {
        $advertisement->recordClick();
        return response()->json(['success' => true]);
    }

    /**
     * Display advertisement statistics overview
     * 
     * @return \Illuminate\View\View
     */
    public function statistics()
    {
        // Get total impressions and clicks for all advertisements
        $totalStats = Advertisement::select(
            DB::raw('SUM(impressions) as total_impressions'),
            DB::raw('SUM(clicks) as total_clicks'),
            DB::raw('CASE WHEN SUM(impressions) > 0 THEN (SUM(clicks) / SUM(impressions)) * 100 ELSE 0 END as ctr')
        )->first();

        // Get statistics for each advertisement
        $advertisements = Advertisement::select(
            'id', 
            'name', 
            'slot_position', 
            'impressions', 
            'clicks',
            DB::raw('CASE WHEN impressions > 0 THEN (clicks / impressions) * 100 ELSE 0 END as ctr')
        )
        ->with('slot')
        ->orderBy('impressions', 'desc')
        ->get();

        // Get statistics for each ad slot
        $slotStats = Advertisement::select(
            'slot_position',
            DB::raw('SUM(impressions) as impressions'),
            DB::raw('SUM(clicks) as clicks'),
            DB::raw('CASE WHEN SUM(impressions) > 0 THEN (SUM(clicks) / SUM(impressions)) * 100 ELSE 0 END as ctr')
        )
        ->groupBy('slot_position')
        ->with('slot')
        ->get();

        return view('admin.advertisements.statistics', compact(
            'totalStats',
            'advertisements',
            'slotStats'
        ));
    }

    /**
     * Display statistics for a specific advertisement
     * 
     * @param Advertisement $advertisement
     * @return \Illuminate\View\View
     */
    public function advertisementStatistics(Advertisement $advertisement)
    {
        // Calculate CTR
        $ctr = $advertisement->impressions > 0 
            ? ($advertisement->clicks / $advertisement->impressions) * 100 
            : 0;

        // Try to get daily statistics if ad_statistics table exists
        $dailyStats = [];
        
        try {
            $dailyStats = DB::table('ad_statistics')
                ->where('advertisement_id', $advertisement->id)
                ->select(
                    'date',
                    'impressions',
                    'clicks',
                    DB::raw('CASE WHEN impressions > 0 THEN (clicks / impressions) * 100 ELSE 0 END as ctr')
                )
                ->orderBy('date', 'desc')
                ->limit(30)
                ->get();
        } catch (\Exception $e) {
            // Table doesn't exist, create empty collection
            $dailyStats = collect();
        }

        return view('admin.advertisements.statistics_detail', compact(
            'advertisement',
            'ctr',
            'dailyStats'
        ));
    }

    /**
     * Export statistics data to CSV file
     * 
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportStatistics()
    {
        $advertisements = Advertisement::with('slot')->get();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="advertisements_statistics.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];
        
        $callback = function() use ($advertisements) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'ID', 
                'Name', 
                'Slot', 
                'Impressions', 
                'Clicks', 
                'CTR (%)',
                'Status'
            ]);
            
            // Add data rows
            foreach ($advertisements as $ad) {
                $ctr = $ad->impressions > 0 ? ($ad->clicks / $ad->impressions) * 100 : 0;
                
                fputcsv($file, [
                    $ad->id,
                    $ad->name,
                    $ad->slot ? $ad->slot->name : $ad->slot_position,
                    $ad->impressions,
                    $ad->clicks,
                    number_format($ctr, 2),
                    $ad->is_active ? 'Active' : 'Inactive'
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}