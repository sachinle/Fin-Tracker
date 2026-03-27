
@extends('layouts.app')

@section('title', 'History — FinTrack')
@section('page-title', 'History')
@section('page-subtitle', 'All monthly financial records')

@section('content')

<div class="section-header mb-4">
    <div>
        <div class="section-title" style="font-size:17px;">All Records</div>
        <div class="section-sub">{{ $records->total() }} entries · Click any row to view detailed breakdown</div>
    </div>
    <a href="{{ route('income.create') }}" class="btn-primary-custom">
        <svg width="13" height="13" viewBox="0 0 13 13" fill="currentColor">
            <path d="M6.5 1a5.5 5.5 0 100 11A5.5 5.5 0 006.5 1zM7 8.5H6V7H4.5V6H6V4.5h1V6h1.5v1H7v1.5z"/>
        </svg>
        Add New
    </a>
</div>

@if($records->count())

<div class="card-custom" style="padding:0;overflow:hidden;">
    <div style="overflow-x:auto;">
        <table class="table-custom" style="margin:0;">
            <thead>
                <tr>
                    <th style="padding:14px 18px;">#</th>
                    <th>Month</th>
                    <th>Income</th>
                    <th>Loan / EMI</th>
                    <th>Expenses</th>
                    <th>Savings</th>
                    <th>Misc</th>
                    <th>Balance</th>
                    <th>Status</th>
                    <th style="text-align:right;padding-right:18px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $record)
                <tr class="clickable-row"
                    onclick="window.location='{{ route('income.show', $record->id) }}'"
                    style="cursor:pointer;"
                    title="Click to view full breakdown for {{ $record->month }}">

                    <td style="padding:13px 18px;color:var(--text-dim);font-family:var(--font-mono);font-size:11.5px;">
                        {{ $records->firstItem() + $loop->index }}
                    </td>

                    <td>
                        <div style="font-weight:600;font-size:13px;">{{ $record->month }}</div>
                        <div style="font-size:10.5px;color:var(--text-dim);font-family:var(--font-mono);">
                            {{ $record->created_at->format('d M Y') }}
                        </div>
                    </td>

                    <td style="font-family:var(--font-mono);color:var(--accent-green);font-weight:600;">
                        ₹{{ number_format($record->income, 0) }}
                    </td>
                    <td style="font-family:var(--font-mono);color:var(--text-muted);">
                        ₹{{ number_format($record->loan, 0) }}
                    </td>
                    <td style="font-family:var(--font-mono);color:var(--text-muted);">
                        ₹{{ number_format($record->expenses, 0) }}
                    </td>
                    <td style="font-family:var(--font-mono);color:var(--accent-blue);">
                        ₹{{ number_format($record->savings, 0) }}
                    </td>
                    <td style="font-family:var(--font-mono);color:var(--text-muted);">
                        ₹{{ number_format($record->miscellaneous, 0) }}
                    </td>
                    <td style="font-family:var(--font-mono);font-weight:700;
                               color:{{ $record->remaining_balance >= 0 ? 'var(--accent-green)' : 'var(--accent-red)' }};">
                        {{ $record->remaining_balance >= 0 ? '' : '-' }}₹{{ number_format(abs($record->remaining_balance), 0) }}
                    </td>

                    <td>
                        @if($record->remaining_balance >= 0)
                            <span class="badge-custom badge-green">Surplus</span>
                        @else
                            <span class="badge-custom badge-red">Deficit</span>
                        @endif
                    </td>

                    {{-- Actions — stop propagation so row click doesn't fire --}}
                    <td style="text-align:right;padding-right:18px;" onclick="event.stopPropagation()">
                        <div style="display:flex;align-items:center;justify-content:flex-end;gap:7px;">
                            <a href="{{ route('income.show', $record->id) }}" class="btn-edit-custom" style="background:rgba(139,92,246,.1);color:var(--accent-purple);border-color:rgba(139,92,246,.2);">
                                <svg width="11" height="11" viewBox="0 0 11 11" fill="currentColor">
                                    <circle cx="5.5" cy="5.5" r="4.5" stroke="currentColor" stroke-width="1.2" fill="none"/>
                                    <line x1="5.5" y1="3.5" x2="5.5" y2="5.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                                    <circle cx="5.5" cy="7.5" r=".6" fill="currentColor"/>
                                </svg>
                                View
                            </a>
                            <a href="{{ route('income.edit', $record->id) }}" class="btn-edit-custom">
                                <svg width="11" height="11" viewBox="0 0 11 11" fill="currentColor">
                                    <path d="M7.5 1.5l2 2L3 10l-2.5.5.5-2.5L7.5 1.5z"/>
                                </svg>
                                Edit
                            </a>
                            <form action="{{ route('income.destroy', $record->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Delete record for {{ $record->month }}? This cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger-custom">
                                    <svg width="11" height="11" viewBox="0 0 11 11" fill="currentColor">
                                        <path d="M2 3h7M4 3V2h3v1M4.5 9V5m2 4V5M3 3l.5 6.5h4L8 3"/>
                                    </svg>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Hint text --}}
<div style="margin-top:10px;font-size:11px;color:var(--text-dim);font-family:var(--font-mono);text-align:center;">
    Click any row to open a detailed chart &amp; breakdown for that month
</div>

{{-- Pagination --}}
@if($records->hasPages())
<div style="margin-top:20px;display:flex;justify-content:center;gap:6px;align-items:center;">
    @if($records->onFirstPage())
        <span style="padding:7px 14px;border-radius:var(--radius-sm);border:1px solid var(--border-color);color:var(--text-dim);font-size:12.5px;cursor:not-allowed;">← Prev</span>
    @else
        <a href="{{ $records->previousPageUrl() }}"
           style="padding:7px 14px;border-radius:var(--radius-sm);border:1px solid var(--border-color);color:var(--text-muted);font-size:12.5px;text-decoration:none;transition:all .2s;">← Prev</a>
    @endif

    @foreach($records->links()->elements[0] as $page => $url)
        @if($page == $records->currentPage())
            <span style="padding:7px 13px;border-radius:var(--radius-sm);background:var(--accent-green);color:#0b0f1a;font-size:12.5px;font-weight:600;">{{ $page }}</span>
        @else
            <a href="{{ $url }}" style="padding:7px 13px;border-radius:var(--radius-sm);border:1px solid var(--border-color);color:var(--text-muted);font-size:12.5px;text-decoration:none;transition:all .2s;">{{ $page }}</a>
        @endif
    @endforeach

    @if($records->hasMorePages())
        <a href="{{ $records->nextPageUrl() }}"
           style="padding:7px 14px;border-radius:var(--radius-sm);border:1px solid var(--border-color);color:var(--text-muted);font-size:12.5px;text-decoration:none;transition:all .2s;">Next →</a>
    @else
        <span style="padding:7px 14px;border-radius:var(--radius-sm);border:1px solid var(--border-color);color:var(--text-dim);font-size:12.5px;cursor:not-allowed;">Next →</span>
    @endif
</div>
@endif

{{-- Summary Row --}}
<div class="row g-3 mt-3">
    <div class="col-4">
        <div class="stat-card green" style="padding:15px 16px;">
            <div class="stat-label">Total Income</div>
            <div class="stat-value green" style="font-size:17px;">₹{{ number_format($records->sum('income'), 0) }}</div>
        </div>
    </div>
    <div class="col-4">
        <div class="stat-card blue" style="padding:15px 16px;">
            <div class="stat-label">Total Savings</div>
            <div class="stat-value" style="font-size:17px;color:var(--accent-blue);">₹{{ number_format($records->sum('savings'), 0) }}</div>
        </div>
    </div>
    <div class="col-4">
        <div class="stat-card red" style="padding:15px 16px;">
            <div class="stat-label">Total Expenses</div>
            <div class="stat-value red" style="font-size:17px;">₹{{ number_format($records->sum('expenses') + $records->sum('loan') + $records->sum('miscellaneous'), 0) }}</div>
        </div>
    </div>
</div>

@else
<div class="card-custom">
    <div class="empty-state">
        <div class="empty-icon">
            <svg width="48" height="48" viewBox="0 0 48 48" fill="none">
                <rect x="4" y="8" width="40" height="32" rx="4" stroke="currentColor" stroke-width="2" stroke-dasharray="4 3"/>
                <path d="M16 24h16M16 31h10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="empty-title">No Records Found</div>
        <div class="empty-sub">You haven't added any monthly records yet.</div>
        <a href="{{ route('income.create') }}" class="btn-primary-custom">Add First Record</a>
    </div>
</div>
@endif

@endsection