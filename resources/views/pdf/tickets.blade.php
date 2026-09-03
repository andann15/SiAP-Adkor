<!DOCTYPE html>
<html>
<head>
    <title>Daftar Tiket Kendala</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; padding: 0; }
        .header p { margin: 5px 0; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Daftar Tiket Kendala</h2>
        <p>Dicetak pada: {{ now()->format('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nomor Tiket</th>
                <th>Aset</th>
                <th>Pembuat</th>
                <th>Operator</th>
                <th>Prioritas</th>
                <th>Status</th>
                <th>Tgl Dibuat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tickets as $index => $ticket)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $ticket->ticket_number }}</td>
                <td>{{ $ticket->asset->name ?? '-' }}</td>
                <td>{{ $ticket->creator->name ?? '-' }}</td>
                <td>{{ $ticket->assignedOperator->name ?? '-' }}</td>
                <td>{{ $ticket->priority->name ?? '-' }}</td>
                <td>{{ $statusMap[$ticket->status] ?? $ticket->status }}</td>
                <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
