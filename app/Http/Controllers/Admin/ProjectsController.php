<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Functions\Helper as Help;

class ProjectsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (isset($_GET['toSearch'])) {
            $projects = Project::where('title', 'like', '%' . $_GET['toSearch'] . '%')->get();
        } else {
            $projects = Project::all();
        }

        return view('admin.projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.projects.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $exists = Project::where('title', $request->title)->first();
        if ($exists) {
            return redirect()->route('admin.projects.index')->with('error', 'Project already exists');
        } else {
            $project = new Project();
            $project->title = $request->title;
            $project->slug = Help::generateSlug($project->title, Project::class);
            $project->save();
            return redirect()->route('admin.projects.index')->with('success', 'Project created');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        return view('admin.projects.edit', compact('project'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $data = $request->validate(
            [
                'title' => 'required|string',
            ],
            [
                'title.required' => 'Title is required',
                'title.string' => 'Title must be a string',
            ]
        );
        $exists = Project::where('title', $request->title)->first();
        if ($exists) {
            return redirect()->route('admin.projects.index')->with('error', 'Project already exists');
        } else {
            $data['slug'] = Help::generateSlug($request->title, Project::class);
            $project->update($data);
            return redirect()->route('admin.projects.index')->with('success', 'Project modified');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $project = Project::find($id);
        if ($project) {
            $project->delete();
            return redirect()->route('admin.projects.index')->with('success', 'Project deleted successfully');
        } else {
            return redirect()->route('admin.projects.index')->with('error', 'Project not found');
        }
    }
}
