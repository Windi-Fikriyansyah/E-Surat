<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Tandai notifikasi spesifik sebagai telah dibaca dan redirect ke halaman surat.
     */
    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
            $url = $notification->data['url'] ?? route('surat.index');
            return redirect($url);
        }

        return redirect()->route('surat.index');
    }

    /**
     * Tandai semua notifikasi pengguna sebagai telah dibaca (AJAX).
     */
    public function markAllAsRead(): JsonResponse
    {
        auth()->user()->unreadNotifications->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi telah ditandai sebagai dibaca.',
        ]);
    }
}

