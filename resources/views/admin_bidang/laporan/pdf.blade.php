<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Infrastruktur SIGAP - Bidang {{ $namaBidangAdmin }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 3px solid #1E3A8A; padding-bottom: 10px; position: relative; }
        /* Menggunakan path lokal absolut untuk menghindari error gambar di PDF */
        .logo { position: absolute; left: 0; top: 0; width: 80px; }
        .title { font-size: 18px; font-weight: bold; color: #1E3A8A; margin: 0; padding-top: 10px; }
        .subtitle { font-size: 12px; margin-top: 5px; color: #555; }
        .date { font-size: 10px; font-style: italic; text-align: right; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #999; padding: 7px; text-align: left; vertical-align: top; }
        th { background-color: #1E3A8A; color: #fff; font-size: 11px; text-transform: uppercase; }
        tr:nth-child(even) { background-color: #f8fafc; }
        .status { font-weight: bold; padding: 3px 6px; border-radius: 4px; font-size: 9px; }
    </style>
</head>
<body>

    <div class="header">
        <img src="{{ public_path('gambar/puprlogo.png') }}" class="logo" alt="Logo PUPR">
        <h1 class="title">REKAPITULASI LAPORAN INFRASTRUKTUR (SIGAP)</h1>
        <p class="subtitle">
            Dinas Pekerjaan Umum dan Penataan Ruang Kabupaten Subang<br>
            <strong>BIDANG {{ strtoupper($namaBidangAdmin) }}</strong>
        </p>
    </div>

    <div class="date">Dicetak pada: {{ \Carbon\Carbon::now()->format('d F Y H:i') }}</div>

    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="12%">ID Laporan</th>
                <th width="12%">Tanggal</th>
                <th width="15%">Pelapor</th>
                <th width="25%">Lokasi Kejadian</th>
                <th width="10%">Status</th>
                <th width="23%">Deskripsi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($laporan as $index => $item)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td><b>#{{ $item->id_laporan }}</b></td>
                <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}<br><small>{{ \Carbon\Carbon::parse($item->created_at)->format('H:i') }} WIB</small></td>
                <td>{{ $item->pelapor->nama_lengkap ?? 'Anonim' }}</td>
                <td>{{ $item->alamat_map }}</td>
                <td style="text-align: center; text-transform: uppercase; font-weight: bold;">
                    @if($item->status == 'selesai')
                        <span style="color: green;">Selesai</span>
                    @elseif($item->status == 'proses' || $item->status == 'diteruskan')
                        <span style="color: orange;">Proses</span>
                    @elseif($item->status == 'ditolak')
                        <span style="color: red;">Ditolak</span>
                    @else
                        <span style="color: gray;">Pending</span>
                    @endif
                </td>
                <td>{{ Str::limit($item->deskripsi_laporan, 80) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 20px;">Belum ada data laporan yang diproses pada bidang ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
