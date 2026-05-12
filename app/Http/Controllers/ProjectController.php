<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Project;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::withSum(['transactions as used_budget' => function($query) {
            $query->where('status', 'approved');
        }], 'amount')->latest()->get();

        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'project_no' => 'required|unique:projects',
            'project_name' => 'required',
            'fiscal_year' => 'required',
            'budget_type' => 'required',
            'responsible_person' => 'nullable',
            'start_date' => 'nullable|date',
            'total_budget' => 'required|numeric|min:0',
        ]);

        Project::create($data);
        return redirect()->route('projects.index')->with('success', 'เพิ่มโครงการสำเร็จ');
    }

    public function show(Project $project)
    {
        $usedBudget = $project->transactions()->where('status', 'approved')->sum('amount');
        $remainingBudget = $project->total_budget - $usedBudget;
        $transactions = $project->transactions()->latest()->get();

        return view('projects.show', compact('project', 'usedBudget', 'remainingBudget', 'transactions'));
    }

    public function report(Project $project)
    {
        $usedBudget = $project->transactions()->where('status', 'approved')->sum('amount');
        $remainingBudget = $project->total_budget - $usedBudget;
        $percentUsed = $project->total_budget > 0 ? round(($usedBudget / $project->total_budget) * 100, 2) : 0;
        $transactions = $project->transactions()->where('status', 'approved')->oldest('transaction_date')->get();

        return view('projects.report', compact('project', 'usedBudget', 'remainingBudget', 'percentUsed', 'transactions'));
    }

    public function close(Project $project)
    {
        $project->update(['status' => 'closed']);
        return back()->with('success', 'ปิดบัญชีโครงการเรียบร้อยแล้ว');
    }

    public function reopen(Project $project)
    {
        $project->update(['status' => 'active']);
        return back()->with('success', 'เปิดบัญชีโครงการอีกครั้งเรียบร้อยแล้ว');
    }

    /**
     * API: Search projects by project_no for autocomplete
     */
    public function apiSearch(Request $request)
    {
        $q = $request->input('q', '');
        $projects = Project::where('project_no', 'like', "%{$q}%")
            ->where('status', 'active')
            ->select('id', 'project_no', 'project_name', 'total_budget')
            ->limit(10)
            ->get()
            ->map(function ($p) {
                $used = $p->transactions()->where('status', 'approved')->sum('amount');
                return [
                    'id' => $p->id,
                    'project_no' => $p->project_no,
                    'project_name' => $p->project_name,
                    'remaining' => $p->total_budget - $used,
                ];
            });

        return response()->json($projects);
    }
}
