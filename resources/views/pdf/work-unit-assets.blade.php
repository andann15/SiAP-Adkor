<!DOCTYPE html>
<html>
<head>
    <title>Daftar Aset Unit Kerja</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        th { background-color: #f4f4f4; font-weight: bold; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; padding: 0; }
        .header p { margin: 5px 0; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Daftar Aset Unit Kerja</h2>
        <p>Dicetak pada: {{ now()->format('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nomor BAPBS/BAPBSAT</th>
                <th>Unit Kerja</th>
                <th>Lokasi</th>
                <th>Penugasan Unit Kerja</th>
                <th>Status</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assets as $index => $asset)
            @php
                $parts = array_filter([
                    $asset->workUnit?->department?->compartment?->name,
                    $asset->workUnit?->department?->name,
                    $asset->workUnit?->name,
                ]);
                $workUnitName = $asset->workUnit ? implode(' › ', $parts) : '-';
                $statusName = isset($statuses[$asset->status]) ? $statuses[$asset->status]->name : $asset->status;
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $asset->code ?? '-' }}</td>
                <td>{{ $asset->name ?? '-' }}</td>
                <td>{{ $asset->location->name ?? '-' }}</td>
                <td>{{ $workUnitName }}</td>
                <td>{{ $statusName }}</td>
                <td>{{ $asset->notes ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
