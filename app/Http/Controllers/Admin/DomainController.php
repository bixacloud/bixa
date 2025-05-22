<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AllowedDomain;
use Illuminate\Http\Request;

class DomainController extends Controller
{
    public function index()
    {
        $domains = AllowedDomain::orderBy('domain_name')->get();
        return view('admin.domains.index', compact('domains'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'domain' => 'required|string|max:255'
        ]);

        if (empty($request->domain) || strpos($request->domain, '.') === false) {
            return back()->withErrors(['domain' => 'Invalid domain format']);
        }

        try {
            AllowedDomain::create([
                'domain_name' => $request->domain
            ]);

            return back()->with('success', 'Domain extension added successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['domain' => 'Error adding domain extension']);
        }
    }

    public function destroy($domain)
    {
        try {
            $domainModel = AllowedDomain::where('domain_name', $domain)->firstOrFail();
            $domainModel->delete();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Domain deleted successfully'
                ]);
            }

            return back()->with('success', 'Domain extension removed successfully');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting domain'
                ], 500);
            }

            return back()->withErrors(['error' => 'Error removing domain extension']);
        }
    }
}