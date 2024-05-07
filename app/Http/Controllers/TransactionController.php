<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class TransactionController extends Controller
{
    public function showAll(Request $request)
    {
        $userEmail = Session::get('user_email');

        $transactions = transaction::join('users', 'users.id', '=', 'transactions.user_id')
            ->where('users.email', $userEmail)
            ->get();

        $userbalance = User::where('email', $userEmail)->get();

        $data = compact('transactions', 'userbalance');

        return view('transactions')->with($data);
    }

    public function deposits(Request $request)
    {
        try {
            DB::beginTransaction();

            $user = User::find($request->input('user_id'));
            $user->balance += $request->input('amount');
            $user->save();

            $transaction = new transaction();
            $transaction->user_id  = $request->input('user_id');
            $transaction->trasansaction_type = 'deposit';
            $transaction->amount = $request->input('amount');

            $transaction->save();

            DB::commit();
            return redirect('transaction')->with('success', 'Deposit Successful.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect('transaction')->with('error', 'An error occurred while inserting user information.');
        }
    }

    public function withdraws(Request $request)
    {
        try {
            DB::beginTransaction();

            $userEmail = Session::get('user_email');
            $transactions = transaction::join('users', 'users.id', '=', 'transactions.user_id')
            ->where('users.email', $userEmail)
            ->where('transactions.trasansaction_type', 'withdraw')
            ->sum('transactions.amount');


            $percentage = 0;
            $amount = 0;
            $user = User::find($request->input('user_id'));
            $balance = $request->input('amount');
            $bal = $balance - 1000;

            if ($transactions > 50000) {
                $amount = $balance;
            } elseif(Carbon::today()->isFriday()) {
                $amount = $balance;
            }
            else {
                if ($user->account_type == 'Individual') {
                    $percentage = ($bal * 0.015) / 100;
                } elseif ($user->account_type == 'Business') {
                    $percentage = ($bal * 0.025) / 100;
                }
                $amount = $bal + $percentage + 1000;
            }
            $user->balance -= $amount;
            $user->save();

            $transaction = new transaction();
            $transaction->user_id  = $request->input('user_id');
            $transaction->trasansaction_type = 'withdraw';
            $transaction->amount = $request->input('amount');
            $transaction->fee = $percentage;

            $transaction->save();

            DB::commit();
            return redirect('transaction')->with('success', 'Deposit Successful.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect('transaction')->with('error', 'An error occurred while inserting user information.');
        }
    }

    public function deposit(Request $request)
    {

        return view('deposit');
    }

    public function withdraw(Request $request)
    {
        return view('withdrawal');
    }

    public function showWithdrawals()
    {
        $withdrawals = Transaction::where('type', 'withdrawal')->get();
        return view('withdrawals')->with('withdrawals', $withdrawals);
    }
}
