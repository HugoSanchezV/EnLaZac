class MercadoPagoController extends Controller
{
    // Constructor para inicializar el SDK de Mercado Pago
    public function __construct()
    {
        SDK::setAccessToken(env('MERCADOPAGO_ACCESS_TOKEN')); // Configuración del token de acceso
    }

    /**
     * Listar todas las transacciones guardadas en la base de datos.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $transactions = Transaction::all(); // Obtener todas las transacciones
        return response()->json($transactions); // Devolver las transacciones en formato JSON
    }

    /**
     * Crear una nueva transacción y generar un pago en Mercado Pago.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Crear una preferencia de pago en Mercado Pago
        $preference = new Preference();

        // Crear un ítem para la preferencia (producto a vender)
        $item = new \MercadoPago\Item();
        $item->title = $request->input('title'); // Nombre del producto
        $item->quantity = $request->input('quantity'); // Cantidad
        $item->unit_price = $request->input('unit_price'); // Precio unitario
        $preference->items = [$item]; // Asignar ítem a la preferencia

        // Crear un pagador (información del comprador)
        $payer = new Payer();
        $payer->email = $request->input('email'); // Email del comprador
        $preference->payer = $payer; // Asignar pagador a la preferencia

        // Guardar la preferencia en Mercado Pago
        $preference->save();

        // Almacenar la transacción en la base de datos
        $transaction = new Transaction();
        $transaction->title = $request->input('title'); // Título del producto
        $transaction->quantity = $request->input('quantity'); // Cantidad
        $transaction->unit_price = $request->input('unit_price'); // Precio unitario
        $transaction->payer_email = $request->input('email'); // Email del comprador
        $transaction->preference_id = $preference->id; // ID de la preferencia de Mercado Pago
        $transaction->save();

        // Devolver el ID de la preferencia para abrir el checkout de Mercado Pago
        return response()->json(['preference_id' => $preference->id]);
    }

    /**
     * Actualizar el estado de una transacción en la base de datos.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Buscar la transacción en la base de datos por su ID
        $transaction = Transaction::find($id);
        
        if ($transaction) {
            // Actualizar el estado de la transacción
            $transaction->status = $request->input('status', $transaction->status);
            $transaction->save();
            return response()->json(['message' => 'Transacción actualizada correctamente.']);
        }

        return response()->json(['message' => 'Transacción no encontrada.'], 404);
    }

    /**
     * Eliminar una transacción de la base de datos.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        // Buscar la transacción en la base de datos por su ID
        $transaction = Transaction::find($id);
        
        if ($transaction) {
            $transaction->delete(); // Eliminar la transacción
            return response()->json(['message' => 'Transacción eliminada correctamente.']);
        }

        return response()->json(['message' => 'Transacción no encontrada.'], 404);
    }
}