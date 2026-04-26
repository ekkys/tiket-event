@foreach($logs as $log)
<tr>
    <td>{{ ($logs->currentPage() - 1) * $logs->perPage() + $loop->iteration }}</td>
    <td>
        @if($log->ticket?->registration)
            <div style="font-weight:700;">{{ $log->ticket->registration->full_name }}</div>
            <div style="font-size:11px; color:var(--muted);">{{ $log->token }}</div>
        @else
            <span style="font-family: monospace; font-size: 12px; color: var(--muted);">{{ $log->token }}</span>
        @endif
    </td>
    <td style="font-size: 12px;">{{ $log->ticket?->registration->id_number ?? '-' }}</td>
    <td>
        <span class="badge badge-{{ $log->success ? 'success' : 'error' }}">
            {{ $log->success ? 'Berhasil' : 'Gagal' }}
        </span>
    </td>
    <td>{{ $log->message }}</td>
    <td style="font-weight:600;">{{ $log->scanner_name }}</td>
    <td style="color:var(--muted); font-size:12px;">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
</tr>
@endforeach
