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

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Mobil</th>
                <th>Nomor Plat</th>
                <th>Status</th>
                <th>Jumlah Peminjaman</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->nama }}</td>
                <td>{{ $item->nomor_plat }}</td>
                <td>
                    @if($item->status->value === 'tersedia') Tersedia
                    @elseif($item->status->value === 'tidak_tersedia') Tidak Tersedia
                    @elseif($item->status->value === 'di_servis') Di Servis
                    @endif
                </td>
                <td>{{ $item->peminjaman_count }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center;">Tidak ada data.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Dicetak otomatically oleh Sistem CETM</div>
</body>
</html>
