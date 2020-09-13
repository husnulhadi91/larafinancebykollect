<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use DB;
use Carbon\Carbon;
use PDF;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\MyTestMail;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        // dd($user);
        
        if ($user->user_type == 1) {
            return $this->admin();
        }
        return $this->customer();
    }
    public function customer()
    {
        
        $user = Auth::user();
        // dd($user);
        $avail=[];
        $financed = DB::table('financing')
        ->where('financing.user_id',$user->id)->where('financing.status',1)->pluck('company_id');
        // dd($financed);
        foreach ($financed as $v) {
            // dd($v);
            $avail[]=$v;
        }
        // var_dump($avail);
        // die();
        $companies = DB::table('company')->whereNotIn('id',$avail)->get();
        // dd($companies);
        $clear = DB::table('financing')
        ->leftjoin('company', 'financing.company_id', '=', 'company.id')
        ->where('financing.user_id',$user->id)->where('financing.status',0)
        ->select('financing.*','company.name')->orderby('financing.created_at','ASC')->get();
        $finance = DB::table('financing')
        ->leftjoin('company', 'financing.company_id', '=', 'company.id')
        ->where('financing.user_id',$user->id)->where('financing.status',1)
        ->select('financing.*','company.name')->orderby('financing.created_at','ASC')->get();
        $out=[];
        $okay=[];
        $today = Carbon::now();
        foreach ($finance as $fin) {
            // var_dump($fin);
            // check outstanding
            $due_date=$fin->created_at;
            $pay=$fin->amount/$fin->tenure;
            $diff = $today->diffInDays($due_date);
            // var_dump($diff);
            // var_dump($pay);
            $payment=$pay*$diff;

            $pment = DB::table('payment')->where('user_id',$user->id)->where('status',1)->where('f_id',$fin->id)->groupBy('f_id')->select(DB::raw('SUM(amount) as sub_payment'))->first();
            // dd($pment);

            if(isset($pment->sub_payment)){
                $total_outstanding=$payment-$pment->sub_payment;
                if($total_outstanding>0){
                    $fin->paid=$pment->sub_payment;
                    $fin->outstanding=$total_outstanding;
                    $fin->balance=$fin->amount-$pment->sub_payment ; 
                }
                else{
                    $fin->paid=$pment->sub_payment;
                    $fin->outstanding=$total_outstanding;
                    $fin->balance=$fin->amount-$pment->sub_payment;
                }
            }else{
                $fin->paid=0;
                    $fin->outstanding=$payment;
                    $fin->balance=$fin->amount;
            }
            // $de[]=$fin;

            if($fin->outstanding<=0){
                array_push($okay,$fin);
            }else{
                array_push($out,$fin);
            }
            // if($diff == 0)

        }
        // dd($okay);
        // die();
        // dd($finance);
        return view('customer.index', [
            'companies' => $companies,
            'finances' => $finance,
            'okay' => $okay,
            'out' => $out,
            'clears' => $clear
        ]);
    }
    public function apply_finance(Request $request)
    {
        $data  = $request->except('_method', '_token');
        $cs = collect($data)->all();
        $user = Auth::user();
        // dd($cs);
        $now = Carbon::now();
        $d1 = $now->copy()->addDays(7);
        DB::table('financing')->insert(
                                       ['user_id' => $user->id, 'company_id' => $cs['company_id'], 'amount' => $cs['amount'], 'tenure' => 7, 'due_date' => $d1, 'status' => 1,
                                      'created_at'   => Carbon::now()->toDateTimeString(),
                                  'updated_at'   => Carbon::now()->toDateTimeString()]
                                   );

                    
       return redirect()->route('home');
    }
    public function pay_finance(Request $request)
    {
        $data  = $request->except('_method', '_token');
        $cs = collect($data)->all();
        $user = Auth::user();
        // dd($cs);
        $stat=rand(0,1);
        DB::table('payment')->insert(
                                       ['user_id' => $user->id, 'f_id' => $cs['finance_id'], 'amount' => $cs['amount'], 'status' => $stat,
                                      'created_at'   => Carbon::now()->toDateTimeString(),
                                  'updated_at'   => Carbon::now()->toDateTimeString()]
                                   );
        $pment = DB::table('payment')->where('user_id',$user->id)->where('status',1)->where('f_id',$cs['finance_id'])->groupBy('f_id')->select(DB::raw('SUM(amount) as sub_payment'))->first();
        $finance = DB::table('finance')->where('user_id',$user->id)->where('status',1)->where('id',$cs['finance_id'])->first();
        if($pment->sub_payment > $finance->amount-1){
            DB::table('finance')
            ->where('id', $cs['finance_id'])
            ->update(['status' => 0, 
                    'updated_at' => Carbon::now()->toDateTimeString()]);
        
        }
                    
       return redirect()->route('home');
    }
    public function invoice_cust($id)
    {
        $user = Auth::user();
        // dd($user);
        if($user->user_type==1)dd('test');
        $financed = DB::table('financing')
        ->where('financing.user_id',$user->id)->where('financing.id',$id)->first();
        $company = DB::table('company')
        ->where('id',$financed->company_id)->first();
        $payment = DB::table('payment')
        ->where('f_id',$financed->id)->get();

        // dd($financed);
        $data = ['title' => 'Invoice Details',
                'user' => $user,
                'financed' => $financed,
                'company' => $company,
                'payment' => $payment,
                ];
        $pdf = PDF::loadView('myPDF', $data);
  
        return $pdf->stream('invoice.pdf');
    }
    public function admin()
    {
        $user = Auth::user();
        // dd($user);
        $avail=[];
        
        $clear = DB::table('financing')
        ->leftjoin('users', 'financing.user_id', '=', 'users.id')
        ->where('financing.company_id',$user->company_id)->where('financing.status',0)
        ->select('financing.*','users.name')->orderby('financing.created_at','ASC')->get();
        $finance = DB::table('financing')
        ->leftjoin('users', 'financing.user_id', '=', 'users.id')
        ->where('financing.company_id',$user->company_id)->where('financing.status',1)
        ->select('financing.*','users.name')->orderby('financing.created_at','ASC')->get();
        $out=[];
        $okay=[];
        $today = Carbon::now();
        foreach ($finance as $fin) {
            // var_dump($fin);
            // check outstanding
            $due_date=$fin->created_at;
            $pay=$fin->amount/$fin->tenure;
            $diff = $today->diffInDays($due_date);
            // var_dump($diff);
            // var_dump($pay);
            $payment=$pay*$diff;

            $pment = DB::table('payment')->where('user_id',$fin->user_id)->where('status',1)->where('f_id',$fin->id)->groupBy('f_id')->select(DB::raw('SUM(amount) as sub_payment'))->first();
            // dd($pment);

            if(isset($pment->sub_payment)){
                $total_outstanding=$payment-$pment->sub_payment;
                if($total_outstanding>0){
                    $fin->paid=$pment->sub_payment;
                    $fin->outstanding=$total_outstanding;
                    $fin->balance=$fin->amount-$pment->sub_payment ; 
                }
                else{
                    $fin->paid=$pment->sub_payment;
                    $fin->outstanding=$total_outstanding;
                    $fin->balance=$fin->amount-$pment->sub_payment;
                }
            }else{
                $fin->paid=0;
                    $fin->outstanding=$payment;
                    $fin->balance=$fin->amount;
            }
            // $de[]=$fin;

            if($fin->outstanding<=0){
                array_push($okay,$fin);
            }else{
                array_push($out,$fin);
            }
            // if($diff == 0)

        }
        // dd($okay);
        // die();
        // dd($finance);
        return view('admin.index', [
            'finances' => $finance,
            'okay' => $okay,
            'out' => $out,
            'clears' => $clear
        ]);
    }
    public function invoice_admin($id)
    {
        $user = Auth::user();
        // dd($user);
        $financed = DB::table('financing')
        ->where('financing.company_id',$user->company_id)->where('financing.id',$id)->first();
        $company = DB::table('company')
        ->where('id',$financed->company_id)->first();
        $payment = DB::table('payment')
        ->where('f_id',$financed->id)->where('status',1)->get();

        // dd($financed);
        $data = ['title' => 'Invoice Details',
                'user' => $user,
                'financed' => $financed,
                'company' => $company,
                'payment' => $payment,
                ];
        $pdf = PDF::loadView('myPDF', $data);
  
        return $pdf->stream('invoice.pdf');
    }
    public function new_user(Request $request)
    {
        $data  = $request->except('_method', '_token');
        $cs = collect($data)->all();
        $user = Auth::user();
        // dd($cs);

        
        if($cs['user_type']==0){
            $d_user=User::create([
            'name' => $cs['email'],
            'email' => $cs['email'],
            'address' => $cs['email'],
            'user_type' => $cs['user_type'],
            'password' => Hash::make($cs['email']),
        ]);
            $now = Carbon::now();
        $d1 = $now->copy()->addDays(7);
        DB::table('financing')->insert(
                                       ['user_id' => $d_user->id, 'company_id' => $user->company_id, 'amount' => $cs['amount'], 'tenure' => 7, 'due_date' => $d1, 'status' => 1,
                                      'created_at'   => Carbon::now()->toDateTimeString(),
                                  'updated_at'   => Carbon::now()->toDateTimeString()]
                                   );
    }else{
        $d_user=User::create([
            'name' => $cs['email'],
            'email' => $cs['email'],
            'address' => $cs['email'],
            'user_type' => $cs['user_type'],
            'company_id' => $user->company_id,
            'password' => Hash::make($cs['email']),
        ]);
    }
        // $password= bcrypt($cs['password']);
        

        Mail::to($cs['email'])->send(new MyTestMail($d_user->id));
       return redirect()->route('home');
    }
}
