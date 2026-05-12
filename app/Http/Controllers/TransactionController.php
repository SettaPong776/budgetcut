<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Transaction;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $status = $request->input('status');
        
        $query = Transaction::with('project')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($subQ) use ($search) {
                    $subQ->where('description', 'like', "%{$search}%")
                      ->orWhere('requester', 'like', "%{$search}%")
                      ->orWhere('doc_ref', 'like', "%{$search}%")
                      ->orWhereHas('project', function ($pq) use ($search) {
                          $pq->where('project_name', 'like', "%{$search}%")
                             ->orWhere('project_no', 'like', "%{$search}%");
                      });
                });
            })
            ->when($startDate, function ($q) use ($startDate) {
                if (str_contains($startDate, '/')) {
                    $parts = explode('/', $startDate);
                    if (count($parts) == 3) {
                        $date = ($parts[2] - 543) . '-' . $parts[1] . '-' . $parts[0];
                        $q->whereDate('transaction_date', '>=', $date);
                    }
                } else if (str_contains($startDate, '-')) {
                    $q->whereDate('transaction_date', '>=', $startDate);
                }
            })
            ->when($endDate, function ($q) use ($endDate) {
                if (str_contains($endDate, '/')) {
                    $parts = explode('/', $endDate);
                    if (count($parts) == 3) {
                        $date = ($parts[2] - 543) . '-' . $parts[1] . '-' . $parts[0];
                        $q->whereDate('transaction_date', '<=', $date);
                    }
                } else if (str_contains($endDate, '-')) {
                    $q->whereDate('transaction_date', '<=', $endDate);
                }
            })
            ->when($status, function ($q) use ($status) {
                if ($status !== 'all') {
                    $q->where('status', $status);
                }
            });
            
        $transactions = $query->latest()->get();

        return view('transactions.index', compact('transactions', 'search', 'startDate', 'endDate', 'status'));
    }

    public function export(Request $request)
    {
        $search = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $status = $request->input('status');
        
        $query = Transaction::with('project')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($subQ) use ($search) {
                    $subQ->where('description', 'like', "%{$search}%")
                      ->orWhere('requester', 'like', "%{$search}%")
                      ->orWhere('doc_ref', 'like', "%{$search}%")
                      ->orWhereHas('project', function ($pq) use ($search) {
                          $pq->where('project_name', 'like', "%{$search}%")
                             ->orWhere('project_no', 'like', "%{$search}%");
                      });
                });
            })
            ->when($startDate, function ($q) use ($startDate) {
                if (str_contains($startDate, '/')) {
                    $parts = explode('/', $startDate);
                    if (count($parts) == 3) {
                        $date = ($parts[2] - 543) . '-' . $parts[1] . '-' . $parts[0];
                        $q->whereDate('transaction_date', '>=', $date);
                    }
                } else if (str_contains($startDate, '-')) {
                    $q->whereDate('transaction_date', '>=', $startDate);
                }
            })
            ->when($endDate, function ($q) use ($endDate) {
                if (str_contains($endDate, '/')) {
                    $parts = explode('/', $endDate);
                    if (count($parts) == 3) {
                        $date = ($parts[2] - 543) . '-' . $parts[1] . '-' . $parts[0];
                        $q->whereDate('transaction_date', '<=', $date);
                    }
                } else if (str_contains($endDate, '-')) {
                    $q->whereDate('transaction_date', '<=', $endDate);
                }
            })
            ->when($status, function ($q) use ($status) {
                if ($status !== 'all') {
                    $q->where('status', $status);
                }
            });
            
        $transactions = $query->latest()->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=transactions_export_" . date('Ymd_His') . ".csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($transactions) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel compatibility with Thai characters
            fputs($file, "\xEF\xBB\xBF");
            
            // CSV Headers
            fputcsv($file, ['ลำดับ', 'วันที่', 'เลขโครงการ', 'ชื่อโครงการ', 'เลขเบิกจ่าย', 'รายการ', 'ผู้ขอกัน', 'ยอดเงิน', 'สถานะ']);
            
            // CSV Rows
            $count = 1;
            foreach ($transactions as $tx) {
                $statusTh = $tx->status == 'approved' ? 'สำเร็จ' : ($tx->status == 'cancelled' ? 'ยกเลิก' : 'รอดำเนินการ');
                fputcsv($file, [
                    $count++,
                    \Carbon\Carbon::parse($tx->transaction_date)->format('d/m/') . (\Carbon\Carbon::parse($tx->transaction_date)->format('Y') + 543),
                    $tx->project->project_no,
                    $tx->project->project_name,
                    $tx->doc_ref,
                    $tx->description,
                    $tx->requester,
                    $tx->amount,
                    $statusTh
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function create()
    {
        $projects = Project::all();
        return view('transactions.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'requester' => 'required|string',
            'description' => 'required',
            'amount' => 'required|numeric|min:1',
            'transaction_date' => 'required|date',
        ]);

        $latestTx = Transaction::orderBy('id', 'desc')->first();
        $nextNumber = 1;
        if ($latestTx && is_numeric($latestTx->doc_ref)) {
            $nextNumber = intval($latestTx->doc_ref) + 1;
        } elseif ($latestTx) {
            $nextNumber = Transaction::count() + 1;
        }
        $data['doc_ref'] = sprintf('%04d', $nextNumber);

        $project = Project::findOrFail($data['project_id']);
        
        if ($project->status === 'closed') {
            return back()->with('error', 'ไม่สามารถทำรายการได้เนื่องจากโครงการนี้ปิดบัญชีแล้ว!')->withInput();
        }
        
        $used = $project->transactions()->where('status', 'approved')->sum('amount');
        $remaining = $project->total_budget - $used;

        if ($data['amount'] > $remaining) {
            return back()->with('error', 'ยอดเงินคงเหลือไม่เพียงพอสำหรับการตัดยอดนี้! (คงเหลือ: '.number_format($remaining, 2).' บาท)')->withInput();
        }

        $data['status'] = 'approved';
        Transaction::create($data);
        return redirect()->route('projects.show', $project->id)->with('success', 'ตัดยอดงบประมาณสำเร็จ!');
    }

    public function edit(Transaction $transaction)
    {
        $projects = Project::all();
        return view('transactions.edit', compact('transaction', 'projects'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'requester' => 'required|string',
            'description' => 'required',
            'amount' => 'required|numeric|min:1',
            'transaction_date' => 'required|date',
        ]);

        $project = Project::findOrFail($data['project_id']);
        
        // If the project changed, or amount changed, check budget
        if ($transaction->project_id != $project->id || $transaction->amount != $data['amount']) {
            $used = $project->transactions()->where('status', 'approved')->where('id', '!=', $transaction->id)->sum('amount');
            $remaining = $project->total_budget - $used;

            if ($data['amount'] > $remaining) {
                return back()->with('error', 'ยอดเงินคงเหลือไม่เพียงพอสำหรับการแก้ไขนี้! (คงเหลือ: '.number_format($remaining, 2).' บาท)')->withInput();
            }
        }

        $transaction->update($data);
        return redirect()->route('projects.show', $project->id)->with('success', 'อัปเดตรายการเบิกจ่ายสำเร็จ!');
    }

    public function destroy(Transaction $transaction)
    {
        // We do a soft cancel instead of hard delete
        $transaction->update(['status' => 'cancelled']);
        return back()->with('success', 'ยกเลิกรายการตัดยอดสำเร็จ! ยอดเงินได้ถูกคืนกลับโครงการแล้ว');
    }

    /**
     * Quick store — accepts project_no instead of project_id
     */
    public function quickStore(Request $request)
    {
        $data = $request->validate([
            'project_no' => 'required|string',
            'requester' => 'required|string',
            'description' => 'required',
            'amount' => 'required|numeric|min:1',
            'transaction_date' => 'required|date',
        ]);

        $project = Project::where('project_no', $data['project_no'])->first();

        if (!$project) {
            return back()->with('error', 'ไม่พบเลขโครงการ "' . $data['project_no'] . '" ในระบบ กรุณาตรวจสอบอีกครั้ง')->withInput();
        }

        if ($project->status === 'closed') {
            return back()->with('error', 'ไม่สามารถทำรายการได้เนื่องจากโครงการ "' . $project->project_no . '" ปิดบัญชีแล้ว!')->withInput();
        }

        $used = $project->transactions()->where('status', 'approved')->sum('amount');
        $remaining = $project->total_budget - $used;

        if ($data['amount'] > $remaining) {
            return back()->with('error', 'ยอดเงินคงเหลือในโครงการ "' . $project->project_no . '" ไม่เพียงพอ! (คงเหลือ: ' . number_format($remaining, 2) . ' บาท)')->withInput();
        }

        // Generate doc_ref
        $latestTx = Transaction::orderBy('id', 'desc')->first();
        $nextNumber = 1;
        if ($latestTx && is_numeric($latestTx->doc_ref)) {
            $nextNumber = intval($latestTx->doc_ref) + 1;
        } elseif ($latestTx) {
            $nextNumber = Transaction::count() + 1;
        }

        Transaction::create([
            'project_id' => $project->id,
            'requester' => $data['requester'],
            'description' => $data['description'],
            'amount' => $data['amount'],
            'transaction_date' => $data['transaction_date'],
            'doc_ref' => sprintf('%04d', $nextNumber),
            'status' => 'approved',
        ]);

        return redirect()->route('projects.index')->with('success', 'กันเงินสำเร็จ! รายการถูกเพิ่มในโครงการ "' . $project->project_name . '" (' . $project->project_no . ') เรียบร้อยแล้ว');
    }
}
