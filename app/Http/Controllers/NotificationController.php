<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function unread(Request $request)
    {
        $user = $request->user(); // Obtener el usuario autenticado
        $notifications = $user->unreadNotifications; // Obtener notificaciones no leídas
       // dd(($notifications));
        return response()->json($notifications);
    }
    
    public function markAsRead(Request $request, $id)
    {
        $user = $request->user();
        $notification = $user->unreadNotifications()->find($id);

        if ($notification) {
            $notification->markAsRead();
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'error'], 404);
    }
}
