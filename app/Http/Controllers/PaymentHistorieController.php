<?php

namespace App\Http\Controllers;

use App\Exports\GenericExport;
use App\Models\PaymentHistorie;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Maatwebsite\Excel\Facades\Excel;

use function PHPUnit\Framework\isNull;

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

        $order = 'asc';
        if ($request->order && isNull($request->order)) {
            $order = $request->order;
        }
        $query->orderBy(
            $request->attribute ?: 'id',
            $order
        );

        $payment = $query->with('user')->latest()->paginate(8)->through(function ($item) {
            return [
                'id' => $item->id,
                'user_id' => $item->user->name ?? 'None',
                'contract_id' => $item->contract_id,
                'amount' => $item->amount,
                'content' => $item->content,
                'payment_method' => $item->payment_method,
                'transaction_id' => $item->transaction_id,
                'created_at' => $item->created_at->format('Y-m-d H:i'),
                // 'receipt_url' => $item->receipt_url,
            ];
        });

        $totalPaymentsCount = PaymentHistorie::count();

        return Inertia::render('Admin/PaymentHistories/PaymentHistories', [
            'payments' => $payment,
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
        Log::info(json_encode($request));
        PaymentHistorie::create([
            'user_id' => $request->user_id,
            'contract_id' => $request->contract_id,
            'amount' => $request->amount,
            'content' => $request->content,
            'payment_method' => $request->payment_method,
            'transaction_id' => $request->transaction_id,
            'receipt_url' => $request->receipt_url,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $data = [
            "q" => $request->q ?? null,
            "attribute" => $request->attribute ?? null,
            "order" => $request->order ?? null,
        ];
        try {
            $payment = PaymentHistorie::findOrFail($id);
            $payment->delete();

            return Redirect::route('payment', $data)->with('success', 'Registro eliminado en éxito');
        } catch (Exception $e) {
            return Redirect::route('payment', $data)->with('error', 'Error al cargar el registro');
        }
    }

    public function exportExcel()
    {
        $query = PaymentHistorie::with(['user']);

        $headings = [
            'ID',
            'USUARIO ID',
            'USUARIO NOMBRE',
            'CONTRATO ID',
            'CANTIDAD',
            'CONTENIDO',
            'METODO DE PAGO',
            'TRANSACCION ID',
            'RECIVO URL',
        ];

        $mappingCallback = function ($payment) {
            return [
                $payment->id,
                $payment->user_id,
                $payment->user->name ?? null,
                $payment->contract_id,
                $payment->amount,
                $payment->content,
                $payment->payment_method,
                $payment->transaction_id,
                $payment->receipt_url,
            ];
        };
        return Excel::download(new GenericExport($query, $headings, $mappingCallback), 'Payments' . now() . '.xlsx');
    }
    public function show(string $id)
    {

        $paymentHistorie = PaymentHistorie::with('user', 'contract', 'transaction')->findOrFail($id);

        return Inertia::render('Admin/PaymentHistorie/Show', [
            'paymentHistorie' => $paymentHistorie,
        ]);
    }
}
