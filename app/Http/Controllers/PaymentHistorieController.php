<?php

namespace App\Http\Controllers;

use App\Models\PaymentHistorie;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentHistorieController extends Controller
{
    public function index(Request $request)
    {
        $query = PaymentHistorie::query();


        if ($request->has('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                    ->orWhere('user_id', 'like', "%$search%")
                    ->orWhere('contract_id', 'like', "%$search%")
                    ->orWhere('amount', 'like', "%$search%")
                    ->orWhere('content', 'like', "%$search%")
                    ->orWhere('payment_method', 'like', "%$search%")
                    ->orWhere('transaction_id', 'like', "%$search%")
                    ->orWhere('receipt_url', 'like', "%$search%");
                // Puedes agregar más campos si es necesario
            });
        }

        if ($request->attribute) {
            $query->orderBy($request->attribute, $request->order);
        } else {
            $query->orderBy('id', 'asc');
        }

        $payment = $query->with('user')->latest()->paginate(8)->through(function ($item) {
            return [
                'id' => $item->id,
                'user_id' => $item->user->name ?? 'None',
                'contract_id' => $item->contract_id,
                'amount' => $item->amount,
                'content' => $item->content,
                'payment_method' => $item->payment_method,
                'transaction_id' => $item->transaction_id,
                'receipt_url' => $item->receipt_url,
            ];
        });

        $totalPaymentsCount = PaymentHistorie::count();

        return Inertia::render('Admin/PaymentHistories/PaymentHistories', [
            'contracts' => $payment,
            'pagination' => [
                'links' => $payment->links()->elements[0],
                'next_page_url' => $payment->nextPageUrl(),
                'prev_page_url' => $payment->previousPageUrl(),
                'per_page' => $payment->perPage(),
                'total' => $payment->total(),
            ],
            'success' => session('success') ?? null,
            'totalPaymentsCount' => $totalPaymentsCount
        ]);
    }
    public function store(PaymentHistorie $request)
    {
        PaymentHistorie::create([
            'user_id' =>$request->user_id,
            'contract_id' =>$request->contract_id,
            'amount' =>$request->amount,
            'content' =>$request->content,
            'payment_method' =>$request->payment_method,
            'transaction_id' =>$request->transaction_id,
            'receipt_url' =>$request->receipt_url,
        ]);
    }
}
