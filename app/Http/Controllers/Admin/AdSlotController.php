<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdSlotController extends Controller
{
    /**
     * Display a listing of ad slots
     */
    public function index()
    {
        $slots = AdSlot::withCount('advertisements')->get();
        return view('admin.ad_slots.index', compact('slots'));
    }

    /**
     * Show the form for creating a new ad slot
     */
    public function create()
    {
        return view('admin.ad_slots.create');
    }

    /**
     * Store a newly created ad slot
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:ad_slots',
            'page' => 'required|string|max:255',
            'type' => 'required|in:predefined,dynamic',
            'selector' => 'required_if:type,dynamic|nullable|string',
            'position' => 'required_if:type,dynamic|nullable|in:before,after,prepend,append',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'sometimes|boolean'
        ]);

        // Set default values if not provided
        $validated['is_active'] = $request->has('is_active') ? true : false;

        // Upload position image if provided
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('ad-slots', 'public');
        }

        AdSlot::create($validated);

        return redirect()->route('admin.ad_slots.index')
            ->with('success', 'Ad slot created successfully.');
    }

    /**
     * Show the form for editing the ad slot
     */
    public function edit(AdSlot $adSlot)
    {
        return view('admin.ad_slots.edit', compact('adSlot'));
    }

    /**
     * Update the ad slot
     */
    public function update(Request $request, AdSlot $adSlot)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:ad_slots,code,' . $adSlot->id,
            'page' => 'required|string|max:255',
            'type' => 'required|in:predefined,dynamic',
            'selector' => 'required_if:type,dynamic|nullable|string',
            'position' => 'required_if:type,dynamic|nullable|in:before,after,prepend,append',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'sometimes|boolean'
        ]);

        // Set default values if not provided
        $validated['is_active'] = $request->has('is_active') ? true : false;

        // Upload new position image if provided
        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($adSlot->image) {
                Storage::disk('public')->delete($adSlot->image);
            }
            
            $validated['image'] = $request->file('image')->store('ad-slots', 'public');
        }

        $adSlot->update($validated);

        return redirect()->route('admin.ad_slots.index')
            ->with('success', 'Ad slot updated successfully.');
    }

    /**
     * Remove the ad slot
     */
    public function destroy(AdSlot $adSlot)
    {
        // Check if slot has any advertisements
        if ($adSlot->advertisements()->count() > 0) {
            return redirect()->route('admin.ad_slots.index')
                ->with('error', 'Cannot delete this slot because it has advertisements in use.');
        }

        // Delete image if it exists
        if ($adSlot->image) {
            Storage::disk('public')->delete($adSlot->image);
        }

        $adSlot->delete();

        return redirect()->route('admin.ad_slots.index')
            ->with('success', 'Ad slot deleted successfully.');
    }
}