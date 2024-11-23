<?php

namespace App\Http\Controllers;

use App\Exports\GenericExport;
use App\Models\PaymentHistorie;
use DragonCode\Contracts\Cashier\Config\Payment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
                    ->orWhere('worker', 'like', "%$search%")
                    // ->orWhere('contract_id', 'like', "%$search%")
                    ->orWhere('amount', 'like', "%$search%")
                    // ->orWhere('content', 'like', "%$search%")
                    ->orWhere('payment_method', 'like', "%$search%")
                    ->orWhere('transaction_id', 'like', "%$search%")
                    ->orWhere('receipt_url', 'like', "%$search%");
                // Puedes agregar más campos si es necesario
            });
        }

        $total = DB::table('payment_histories')->sum('amount');
        $total_month = $total;
        if (isNull($request->date)) {
            $query->where('created_at', 'like', $request->date . '%');
            $total_month = DB::table('payment_histories')->where('created_at', 'like', $request->date . '%')->sum('amount');
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
                'worker' => $item->worker,
                'amount' => $item->amount,
                // 'content' => $item->content,
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
            'totalPaymentsCount' => $totalPaymentsCount,
            'totalAmount' => $total,
            'totalAmountMonth' => $total_month
        ]);
    }
    public function store(PaymentHistorie $request)
    {
        PaymentHistorie::create([
            'user_id' => $request->user_id,
            'worker' => $request["worker"],
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
            "data" => $request->data ?? null,
        ];

        try {
            $payment = PaymentHistorie::findOrFail($id);
            $payment->delete();

            return Redirect::route('payment', $data)->with('success', 'Registro eliminado en éxito');
        } catch (Exception $e) {
            return Redirect::route('payment', $data)->with('error', 'Error al cargar el registro');
        }
    }

    public function cutMonth($date, Request $request)
    {
        // dd($request->all());
        $data = [
            "q" => $request->q ?? null,
            "attribute" => $request->attribute ?? null,
            "order" => $request->order ?? null,
            "data" => $date ?? null,
        ];

        DB::beginTransaction();
        try {
            $payments = PaymentHistorie::where('created_at', 'like', $date . '%')->get();

            if ($payments->isEmpty()) {
                return Redirect::route('payment', $data)->with('info', 'No hay registros para eliminar en este mes');
            }

            PaymentHistorie::destroy($payments->pluck('id'));

            DB::commit();
            return Redirect::route('payment', $data)->with('success', 'Se ha realizado el corte');
        } catch (Exception $e) {
            DB::rollBack();
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
            'TRABAJADOR',
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
                $payment->worker ?? null,
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
        $paymentHistorie = PaymentHistorie::with('user')->findOrFail($id);

        return Inertia::render('Admin/PaymentHistories/Show', [
            'paymentHistorie' => $paymentHistorie,
        ]);
    }
}
