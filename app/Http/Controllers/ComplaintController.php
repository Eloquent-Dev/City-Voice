<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Complaint;
use App\Models\User;
use App\Notifications\newComplaintSubmitted;
use App\Notifications\ComplaintRecieved;
use Illuminate\Support\Facades\Notification;
use App\Models\JobOrder;
use Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ComplaintController extends Controller
{

    public function index(){
        $complaints = Complaint::where('user_id', auth()->id())
                    ->with('category')
                    ->latest()
                    ->get();

                    return view('complaints.index', compact('complaints'));
    }

    public function show(Complaint $complaint){
        if($complaint->user_id !== auth()->id()){
            abort(403,'Unauthorized action. You can only view your own complaints.');
        }
        return view('complaints.show', compact('complaint'));
    }
    public function create(){
        $categories = Category::all();

        return view('complaints.create',compact('categories'));
    }

    public function store(Request $request){
        $rules = [
            'title' => ['required'
            ,'string'
            ,'max:255',
            function($attribute, $value,$fail) use ($request){
                $duplicateExists = Complaint::where('title', $value)
                ->where('category_id', $request->input('category_id'))
                ->whereIn('status', ['pending', 'in_progress'])
                ->exists();

                if($duplicateExists){
                    $fail('Similar active complaint has been found. Please use a more specific title.');
                }
            }
            ],
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,heic,heif|max:20480'
        ];

        if(!auth()->check()){
            $rules['complainant_name'] = 'required|string|max:255';
            $rules['guest_national_no'] = 'required_without:passport_no|nullable|string|max:20';
            $rules['passport_no'] = 'required_without:guest_national_no|nullable|string|max:20';
            $rules['email'] = 'required|email|max:255';
        }

        $validated = $request->validate($rules,[],[
            'complainant_name' => 'Full Name',
            'title' => 'Title',
            'email' => 'Email Address',
            'guest_national_no' => 'National Number',
            'passport_no' => 'Passport Number',
            'description' => 'Description'
        ]);

        $userId = auth()->id();

        if (!auth()->check()) {
            $user = User::where('email', $validated['email'])->first();
            if (!$user) {
                $user = User::create([
                    'name' => $validated['complainant_name'],
                    'email' => $validated['email'],
                    'national_no' => $validated['guest_national_no'] ?? null,
                    'passport_no' => $validated['passport_no'] ?? null,
                    'password' => Hash::make(Str::random(10)),
                ]);
            }
            $userId = $user->id;
        }

        $imagePath = null;
        if($request->hasFile('image')){
            $imagePath = $request->file('image')->store('complaints','public');
        }

        $complaint = Complaint::Create([
            'title' => $validated['title'],
            'category_id' => $validated['category_id'],
            'description' => $validated['description'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'user_id' => auth()->id() ?? $userId,
            'image_path' => $imagePath,
            'status' => 'pending'
        ]);

        JobOrder::create([
            'complaint_id' => $complaint->id
        ]);

        $dispatchers = User::where('role' , 'dispatcher')->get();
        Notification::send($dispatchers, new newComplaintSubmitted($complaint));

        if(auth()->check())
            {
                auth()->user()->notify(new ComplaintRecieved($complaint));
            };

        return redirect()->route('home')->with('success','Your complaint has been submitted and is pending');
    }
}
