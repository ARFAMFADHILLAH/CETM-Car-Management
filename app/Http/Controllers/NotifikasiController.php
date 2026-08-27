<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class NotifikasiController extends Controller
{
    /**
     * Display the authenticated user's notifications.
     */
    public function index(): Response
    {
        $notifikasi = Notifikasi::query()
            ->where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->get();

        return Inertia::render('notifikasi/Index', [
            'notifikasi' => $notifikasi,
        ]);
    }

    /**
     * Mark a single notification as read.
     */
    public function markRead(Notifikasi $notifikasi): RedirectResponse
    {
        abort_unless($notifikasi->user_id === auth()->id(), 403);

        $notifikasi->update(['dibaca' => true]);

        return back();
    }

    /**
     * Mark all of the authenticated user's notifications as read.
     */
    public function markAllRead(): RedirectResponse
    {
        Notifikasi::query()
            ->where('user_id', auth()->id())
            ->where('dibaca', false)
            ->update(['dibaca' => true]);

        return back();
    }
}
