<?php

namespace App\Http\Controllers;

use App\Models\LocalPay;
use App\Models\PaymentHistorie;
use App\Services\PaymentService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Inertia\Inertia;

use function PHPUnit\Framework\isNull;

class LocalPayController extends Controller
{
    //

    public function index() {}

    public function show($id, Request $request)
    {
        try {
            $payment = LocalPay::findOrFail($id);

            $user = Auth::user();
            return Inertia::render('User/Pays/ShowLocalPay', [
                'cart' => $payment->payment_info,
                'amount' => floatval($payment->total_amount),
                'token' => $payment->token,
                'user' => [
                    "id" => $user->id,
                    "name" => $user->name,
                ],
                'date' => $payment->created_at,
                'orderId' => $payment->id,
                'success' => $request->success,
                'warning' => $request->error,
                'error' => $request->error,
            ]);
        } catch (Exception $e) {
            dd($e);
            return Redirect::route('pays')->with('error', 'No se ha encontrado el registro');
        }
    }

    public function search(Request $request)
    {
        try {
            if (!isset($request["token"])) {
                return Inertia::render('Admin/Pays/Index', [
                    'success' => $request->success,
                    'warning' => $request->error,
                    'error' => $request->error,
                ]);
            }

            $token = $request->token;

            $payment = LocalPay::where('token', $token)->first();

            if (is_null($payment)) {
                return Redirect::route('local.pay.search')->with('error', 'No se encontro ningun resultado');
            }

            $user = Auth::user();
            return Inertia::render('Admin/Pays/Index', [
                'cart' => $payment->payment_info,
                'amount' => floatval($payment->total_amount),
                'token' => $payment->token,
                'user' => [
                    "id" => $user->id,
                    "name" => $user->name,
                ],
                'date' => $payment->created_at,
                'orderId' => $payment->id,
                'success' => $request->success,
                'warning' => $request->error,
                'error' => $request->error,
            ]);
        } catch (Exception $e) {
            return Inertia::render('Admin/Pays/Index', [])->with('error', 'Error al cargar registro');
        }
    }

    public function showAdmin($token, $url = 'Admin/Pays/ShowLocalPay')
    {
        try {
            $payment = LocalPay::where('tokne', $token);

            $user = Auth::user();
            return Inertia::render($url, [
                'cart' => $payment->payment_info,
                'amount' => floatval($payment->total_amount),
                'token' => $payment->token,
                'user' => [
                    "id" => $user->id,
                    "name" => $user->name,
                ],
                'date' => $payment->created_at,
                'orderId' => $payment->id,
            ]);
        } catch (Exception $e) {
            return Redirect::back()->with('error', 'No se ha encontrado el registro');
        }
    }


    public function store(Request $request)
    {

        try {
            $reference = DB::transaction(function () use ($request) {
                $order = LocalPay::where('user_id', Auth::id())->first();

                if (!is_null($order)) {
                    self::destroyByToken($order->token);
                }

                $request->validate([
                    'cart' => 'required|array',
                    'amount' => 'required|numeric',
                ]);

                $reference = LocalPay::create([
                    'user_id' => Auth::id(),
                    'payment_info' => $request->cart,
                    'total_amount' => $request->amount,
                    'token' =>  Auth::id() . Str::random(6),
                    'status' =>  0,
                ]);
                // dd($reference);

                return $reference;
            });
            // DeleteUnpaidPayment::dispatch($reference->id)->delay(now()->addMinutes(3));
            return Redirect::route('local.pay.show', $reference->id);
        } catch (Exception $e) {
            dd($e);
            return Redirect::route('pays')->with('error', 'Error al crear la orden');
        }
    }

    public function destroy($id)
    {
        try {
            $reference = LocalPay::findOrFail($id);
            $reference->delete();
            return Redirect::route('pays')->with('success', 'Orden eliminada');
        } catch (Exception $e) {
            return Redirect::route('local.pay.show', $id)->with('error', 'Error al eliminar la orden');
        }
    }

    public function checkStatus( Request $request)
    {
        try {
            $token = $request->token ?? null;
            $payment = PaymentHistorie::where('transaction_id', $token)->get();

            if (!is_null($payment)) {
                return Redirect::route('pays')->with('success', 'Orden terminada');
            }
            return Redirect::back()->with('warning', 'No ha sido pagado');
        } catch (Exception $e) {
            return Redirect::back()->with('error', 'Error al eliminar la orden');
        }
    }


    public function confirmLocalPay($token, Request $request)
    {
        try {

            DB::transaction(function () use ($token, $request) {
                $pay = LocalPay::where('token', $token)->first();
                // dd($pay->user_id);
                self::update($request->amount, $request->cart,  $token, $pay->user_id);
                $result = self::destroyByToken($token);

                if (!$result) {
                    throw new Exception("error al eliminar datos");
                }
            });

            return Redirect::route('local.pay.search')->with('success', 'Pago realizado con exito');
        } catch (Exception $e) {
            return Redirect::route('local.pay.search', [
                'token' => $token
            ])->with('error', 'Errror al realizar el pago');
        }
    }

    public function destroyByToken($token)
    {
        try {
            $reference = LocalPay::where('token', $token)->first();
            $reference->delete();
            return true;
        } catch (Exception $e) {
            dd($e->getMessage());
            return false;
        }
    }

    public function update($amount, $cart,  $transaction, $user_id = null)
    {
        $payment = new PaymentService();

        // dd($user_id);
        $worker = Auth::id() . " " . Auth::user()->name;
        $payment->createPayment(
            $amount,
            $cart,
            $transaction,
            "/local/pay/show/{$transaction}",
            "Local",
            $worker,
            $user_id ?? null,
        );

        $payment->updateDataPayments($cart);
    }
}
