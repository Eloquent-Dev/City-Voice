<?php

namespace App\Http\Controllers;
use App\Models\Complaint;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()-> user();
        $complaints= Complaint::where('user_id', $user->id)->get();
        $stats = [
            'total'=> $complaints->count(),
            'pending'=> $complaints->where('status','pending')->count(),
            'resolved' => $complaints->whereIn('status',['resolved','approved'])->count(),
            'approved' => $complaints->where('status','approved')->count(),
            'in_progress' => $complaints->where('status','in_progress')->count(),
            'under_review' => $complaints->where('status','under_review')->count(),
            'reopened' => $complaints->where('status','reopened')->count(),
            'rejected' => $complaints->where('status','rejected')->count(),
            ];
        $groupedComplaints = $complaints->groupBy('status');
        $chartLabels =[];
        $chartData =[];

        foreach ($groupedComplaints as $status => $items )
        {
            $totalHours = 0;
            $count =0;

            foreach ($items as $item)
            {
                if($status === 'pending'){
                    $hours = $item->created_at->diffInHours(now());
                }
                else{
                    $hours = $item->created_at->diffInHours($item->updated_at);
                }
                $totalHours += $hours;
                $count++;

            }
            $averageHours = $count > 0 ? round($totalHours / $count, 1) : 0;

            $chartLabels[] = ucwords(str_replace('_',' ',$status));
            $chartData[] = $averageHours;
            }
        return view('dashboard',compact('user','stats','chartLabels','chartData'));
    }

}
