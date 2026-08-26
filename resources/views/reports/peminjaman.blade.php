<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #1B75BB; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 16px; color: #1B75BB; }
        .header p { margin: 2px 0 0; font-size: 10px; color: #666; }
        .filters { margin-bottom: 10px; font-size: 9px; color: #666; }
        .filters span { background: #f0f0f0; padding: 2px 6px; border-radius: 3px; margin-right: 8px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #1B75BB; color: #fff; padding: 6px 8px; text-align: left; font-size: 9px; }
        td { padding: 5px 8px; border-bottom: 1px solid #eee; font-size: 9px; }
        tr:nth-child(even) { background: #f9f9f9; }
        .footer { margin-top: 15px; text-align: right; font-size: 8px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>PT CEM — Dicetak pada {{ $generatedAt }}</p>
    </div>

    @if(!empty($filters))
    <div class="filters">
        <strong>Filter:</strong>
        @if(!empty($filters['tanggal_mulai'])) <span>Dari: {{ $filters['tanggal_mulai'] }}</span> @endif
        @if(!empty($filters['tanggal_selesai'])) <span>Sampai: {{ $filters['tanggal_selesai'] }}</span> @endif
        @if(!empty($filters['status']) && $filters['status'] !== 'semua') <span>Status: {{ ucfirst($filters['status']) }}</span> @endif
        @if(!empty($filters['divisi_id'])) <span>Divisi ID: {{ $filters['divisi_id'] }}</span> @endif
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Peminjam</th>
                <th>Email</th>
                <th>Divisi</th>
                <th>Mobil</th>
                <th>Tgl Mulai</th>
                <th>Tgl Selesai</th>
                <th>Kegiatan</th>
                <th>Lokasi</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->nama_peminjam }}</td>
                <td>{{ $item->email_peminjam }}</td>
                <td>{{ $item->divisi?->nama_divisi ?? '-' }}</td>
                <td>{{ $item->car?->nama ?? '-' }}</td>
                <td>{{ $item->tanggal_mulai->format('d/m/Y H:i') }}</td>
                <td>{{ $item->tanggal_selesai->format('d/m/Y H:i') }}</td>
                <td>{{ $item->kegiatan }}</td>
                <td>{{ $item->lokasi_tujuan }}</td>
                <td>
                    @if($item->status->value === 'pending') Menunggu
                    @elseif($item->status->value === 'disetujui') Disetujui
                    @elseif($item->status->value === 'ditolak') Ditolak
                    @elseif($item->status->value === 'selesai') Selesai
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" style="text-align:center;">Tidak ada data.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Dicetak otomatically oleh Sistem CETM</div>
</body>
</html>
