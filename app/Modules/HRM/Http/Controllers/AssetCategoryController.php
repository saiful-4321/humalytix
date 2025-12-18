<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\AssetCategory;
use Illuminate\Http\Request;

class AssetCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:hrm.assets.settings');
    }

    public function index()
    {
        $categories = AssetCategory::orderBy('name')->paginate(10);
        return view('HRM::pages.settings.assets.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:hrm_asset_categories,name',
            'depreciation_rate' => 'required|numeric|min:0|max:100',
            'useful_life_years' => 'required|integer|min:0',
        ]);

        AssetCategory::create([
            'name' => $validated['name'],
            'description' => $request->description,
            'depreciation_rate' => $validated['depreciation_rate'],
            'useful_life_years' => $validated['useful_life_years'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->back()->with('success', 'Asset Category created successfully!');
    }

    public function update(Request $request, AssetCategory $assetCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:hrm_asset_categories,name,' . $assetCategory->id,
            'depreciation_rate' => 'required|numeric|min:0|max:100',
            'useful_life_years' => 'required|integer|min:0',
        ]);

        $assetCategory->update([
            'name' => $validated['name'],
            'description' => $request->description,
            'depreciation_rate' => $validated['depreciation_rate'],
            'useful_life_years' => $validated['useful_life_years'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->back()->with('success', 'Asset Category updated successfully!');
    }

    public function destroy(AssetCategory $assetCategory)
    {
        if ($assetCategory->assets()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete category with associated assets.');
        }

        $assetCategory->delete();
        return redirect()->back()->with('success', 'Asset Category deleted successfully!');
    }
}
