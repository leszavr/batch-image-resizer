<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Preset;
use Illuminate\Http\Request;

class PresetController extends Controller
{
    public function index()
    {
        $presets = Preset::forUser(auth()->id())->get();
        return view('presets', compact('presets'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:100',
            'pipeline'       => 'required',
            'output_format'  => 'nullable|in:jpg,png,webp,avif,gif,tiff',
            'output_quality' => 'nullable|integer|min:1|max:100',
            'rename_prefix'  => 'nullable|string|max:50',
            'rename_suffix'  => 'nullable|string|max:50',
        ]);

        if (is_string($data['pipeline'])) {
            $decoded = json_decode($data['pipeline'], true);
            if (! is_array($decoded)) {
                return back()->withErrors(['pipeline' => dbt('presets.messages.pipeline_json')])->withInput();
            }
            $data['pipeline'] = $decoded;
        }

        if (! is_array($data['pipeline'])) {
            return back()->withErrors(['pipeline' => dbt('presets.messages.pipeline_array')])->withInput();
        }

        auth()->user()->presets()->create($data);

        return back()->with('success', dbt('presets.messages.saved'));
    }

    public function destroy(Preset $preset)
    {
        abort_unless($preset->user_id === auth()->id(), 403);
        $preset->delete();
        return back()->with('success', dbt('presets.messages.deleted'));
    }
}
