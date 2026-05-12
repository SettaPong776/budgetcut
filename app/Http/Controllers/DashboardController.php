<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Transaction;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBudget = Project::sum('total_budget');
        $usedBudget = Transaction::where('status', 'approved')->sum('amount');
        $remainingBudget = $totalBudget - $usedBudget;
        
        $recentTransactions = Transaction::with('project')->latest()->take(5)->get();
        $projectsCount = Project::count();

        // --- Chart Data Preparation ---
        
        // 1. Monthly Spending (Line Chart)
        $monthlySpending = Transaction::where('status', 'approved')
            ->selectRaw('DATE_FORMAT(transaction_date, "%Y-%m") as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
            
        $thaiMonths = ['', 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
        $chartMonths = [];
        $chartSpending = [];
        foreach ($monthlySpending as $spending) {
            $parts = explode('-', $spending->month);
            $year = (int)$parts[0] + 543;
            $monthStr = $thaiMonths[(int)$parts[1]];
            $chartMonths[] = "$monthStr $year";
            $chartSpending[] = (float) $spending->total;
        }

        // 2. Project Balances (Bar Chart) - Top 5 active projects by budget
        $projectsChart = Project::withSum(['transactions as used_budget' => function($q) {
            $q->where('status', 'approved');
        }], 'amount')
        ->orderByDesc('total_budget')
        ->take(5)
        ->get();

        $chartProjectNames = [];
        $chartProjectUsed = [];
        $chartProjectRemaining = [];

        foreach ($projectsChart as $proj) {
            $used = $proj->used_budget ?? 0;
            $rem = $proj->total_budget - $used;
            $chartProjectNames[] = \Illuminate\Support\Str::limit($proj->project_name, 20);
            $chartProjectUsed[] = (float) $used;
            $chartProjectRemaining[] = (float) $rem;
        }

        return view('dashboard', compact(
            'totalBudget', 'usedBudget', 'remainingBudget', 
            'recentTransactions', 'projectsCount',
            'chartMonths', 'chartSpending',
            'chartProjectNames', 'chartProjectUsed', 'chartProjectRemaining'
        ));
    }
}
