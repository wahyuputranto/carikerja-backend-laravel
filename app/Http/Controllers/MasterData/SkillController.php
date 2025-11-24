<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class SkillController extends Controller
{
    public function index()
    {
        return Inertia::render('MasterData/Skills/Index', [
            'skills' => Skill::latest()->paginate(10),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:master_skills',
            'category' => 'nullable|string|max:255',
        ]);

        Skill::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'category' => $validated['category'],
        ]);

        return redirect()->back()->with('success', 'Skill created successfully.');
    }

    public function update(Request $request, Skill $skill)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:master_skills,name,' . $skill->id,
            'category' => 'nullable|string|max:255',
        ]);

        $skill->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'category' => $validated['category'],
        ]);

        return redirect()->back()->with('success', 'Skill updated successfully.');
    }

    public function destroy(Skill $skill)
    {
        $skill->delete();

        return redirect()->back()->with('success', 'Skill deleted successfully.');
    }
}
