<?php

namespace App\Http\Controllers;

use App\Enums\CarStatus;
use App\Enums\PeminjamanStatus;
use App\Models\Car;
use App\Models\Divisi;
use App\Models\Peminjaman;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ChatbotController extends Controller
{
    /**
     * Handle chatbot messages via keyword matching + database queries.
     */
    public function chat(Request $request): JsonResponse
    {
        $pesan = strtolower(trim($request->input('pesan', '')));

        $jawaban = $this->proses($pesan);

        return response()->json(['jawaban' => $jawaban]);
    }

    private function proses(string $pesan): string
    {
        if ($this->cocok($pesan, ['halo', 'hai', 'hello', 'hi', 'selamat', 'selamat pagi', 'selamat siang', 'selamat sore', 'selamat malam'])) {
            return $this->sapaan();
        }

        if ($this->cocok($pesan, ['mobil tersedia', 'mobil yang tersedia', 'mobil kosong'])) {
            return $this->mobilTersedia();
        }

        if ($this->cocok($pesan, ['mobil', 'daftar mobil', 'info mobil', 'data mobil', 'jenis mobil', 'kendaraan'])) {
            return $this->daftarMobil();
        }

        if ($this->cocok($pesan, ['jadwal', 'jadwal peminjaman', 'jadwal mobil', 'kalender'])) {
            return $this->jadwal();
        }

        if ($this->cocok($pesan, ['status peminjaman', 'status pengajuan', 'pengajuan saya', 'peminjaman saya', 'status saya'])) {
            return $this->statusPeminjaman($request = request());
        }

        if ($this->cocok($pesan, ['divisi', 'daftar divisi', 'bagian'])) {
            return $this->daftarDivisi();
        }

        if ($this->cocok($pesan, ['cara', 'bagaimana', 'gimana', 'how to', 'cara peminjaman', 'cara ajukan', 'ajukan', 'cara pinjam'])) {
            return $this->caraPeminjaman();
        }

        if ($this->cocok($pesan, ['status'])) {
            return $this->statusPeminjaman(request());
        }

        if ($this->cocok($pesan, ['bantuan', 'help', 'fitur', 'menu', 'apa yang bisa', 'bisa apa'])) {
            return $this->bantuan();
        }

        return $this->bantuan();
    }

    private function cocok(string $pesan, array $kataKunci): bool
    {
        foreach ($kataKunci as $kata) {
            if (str_contains($pesan, $kata)) {
                return true;
            }
        }

        return false;
    }

    private function sapaan(): string
    {
        return "Halo! Saya asisten virtual CETM. Saya bisa membantu Anda dengan:\n\n"
            ."- Info mobil (ketik: **info mobil**)\n"
            ."- Mobil tersedia (ketik: **mobil tersedia**)\n"
            ."- Jadwal peminjaman (ketik: **jadwal**)\n"
            ."- Status pengajuan (ketik: **status**)\n"
            ."- Daftar divisi (ketik: **divisi**)\n"
            ."- Cara pengajuan (ketik: **cara ajukan**)\n\n"
            .'Ketik pertanyaan Anda untuk memulai!';
    }

    private function daftarMobil(): string
    {
        $mobil = Car::all();

        if ($mobil->isEmpty()) {
            return 'Belum ada data mobil yang tersedia di sistem.';
        }

        $daftar = "Berikut daftar mobil CETM:\n\n";

        $no = 1;
        foreach ($mobil as $m) {
            $status = match ($m->status) {
                CarStatus::Tersedia => 'Tersedia',
                CarStatus::TidakTersedia => 'Tidak Tersedia',
                CarStatus::DiServis => 'Di Servis',
            };
            $daftar .= "{$no}. {$m->nama} ({$m->nomor_plat}) — {$status}\n";
            $no++;
        }

        $tersedia = $mobil->where('status', 'tersedia')->count();
        $daftar .= "\nTotal: {$mobil->count()} mobil ({$tersedia} tersedia)";

        return $daftar;
    }

    private function mobilTersedia(): string
    {
        $mobil = Car::where('status', 'tersedia')->get();

        if ($mobil->isEmpty()) {
            return 'Saat ini tidak ada mobil yang tersedia untuk dipinjam.';
        }

        $daftar = "Mobil yang tersedia saat ini:\n\n";

        $no = 1;
        foreach ($mobil as $m) {
            $daftar .= "{$no}. {$m->nama} ({$m->nomor_plat})\n";
            $no++;
        }

        $daftar .= "\nUntuk mengajukan pinjaman, silakan buka menu Ajukan Peminjaman.";

        return $daftar;
    }

    private function jadwal(): string
    {
        $jadwal = Peminjaman::with(['car', 'divisi'])
            ->whereIn('status', ['pending', 'disetujui'])
            ->where('tanggal_selesai', '>=', Carbon::now())
            ->orderBy('tanggal_mulai')
            ->limit(10)
            ->get();

        if ($jadwal->isEmpty()) {
            return 'Tidak ada jadwal peminjaman aktif saat ini.';
        }

        $daftar = "Jadwal peminjaman aktif:\n\n";

        $no = 1;
        foreach ($jadwal as $j) {
            $mulai = Carbon::parse($j->tanggal_mulai)->format('d M Y');
            $selesai = Carbon::parse($j->tanggal_selesai)->format('d M Y');
            $status = match ($j->status) {
                PeminjamanStatus::Pending => 'Menunggu',
                PeminjamanStatus::Disetujui => 'Disetujui',
                PeminjamanStatus::Ditolak => 'Ditolak',
                PeminjamanStatus::Selesai => 'Selesai',
            };
            $divisi = $j->divisi?->nama_divisi ?? '-';
            $daftar .= "{$no}. {$j->car->nama} — {$mulai} s/d {$selesai}\n";
            $daftar .= "   Peminjam: {$j->nama_peminjam} ({$divisi}) — {$status}\n\n";
            $no++;
        }

        return $daftar;
    }

    private function statusPeminjaman(Request $request): string
    {
        $user = $request->user();

        if (! $user) {
            return 'Silakan login terlebih dahulu untuk melihat status pengajuan Anda.';
        }

        $peminjaman = Peminjaman::with('car')
            ->where('email_peminjam', $user->email)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        if ($peminjaman->isEmpty()) {
            return 'Anda belum memiliki pengajuan peminjaman. Silakan ajukan melalui menu Ajukan Peminjaman.';
        }

        $daftar = "Pengajuan terakhir Anda:\n\n";

        $no = 1;
        foreach ($peminjaman as $p) {
            $tanggal = Carbon::parse($p->created_at)->format('d M Y');
            $status = match ($p->status->value) {
                'pending' => 'Menunggu',
                'disetujui' => 'Disetujui',
                'ditolak' => 'Ditolak',
                'selesai' => 'Selesai',
                default => ucfirst($p->status->value),
            };
            $daftar .= "{$no}. #{$p->id} {$p->car->nama} — {$status} ({$tanggal})\n";
            $no++;
        }

        return $daftar;
    }

    private function daftarDivisi(): string
    {
        $divisi = Divisi::all();

        if ($divisi->isEmpty()) {
            return 'Belum ada data divisi di sistem.';
        }

        $daftar = "Daftar divisi CETM:\n\n";

        $no = 1;
        foreach ($divisi as $d) {
            $daftar .= "{$no}. {$d->nama_divisi}\n";
            $no++;
        }

        return $daftar;
    }

    private function caraPeminjaman(): string
    {
        return "Untuk mengajukan peminjaman mobil:\n\n"
            ."1. Buka menu \"Ajukan Peminjaman\" di sidebar\n"
            ."2. Isi formulir: pilih divisi, mobil, tanggal pinjam & kembali\n"
            ."3. Isi tujuan, keperluan, dan lokasi tujuan\n"
            ."4. Isi KM awal dan kondisi tangki BBM\n"
            ."5. Klik \"Kirim Pengajuan\"\n"
            ."6. Tunggu approval dari admin\n\n"
            .'Anda bisa memantau status pengajuan di menu "Daftar Pengajuan".';
    }

    private function bantuan(): string
    {
        return "Saya adalah asisten virtual CETM. Berikut yang bisa saya bantu:\n\n"
            ."• **info mobil** — Lihat daftar semua mobil\n"
            ."• **mobil tersedia** — Lihat mobil yang bisa dipinjam\n"
            ."• **jadwal** — Lihat jadwal peminjaman aktif\n"
            ."• **status** — Lihat status pengajuan Anda\n"
            ."• **divisi** — Lihat daftar divisi\n"
            ."• **cara ajukan** — Panduan mengajukan peminjaman\n\n"
            .'Ketik salah satu kata kunci di atas untuk mendapatkan informasi!';
    }
}
