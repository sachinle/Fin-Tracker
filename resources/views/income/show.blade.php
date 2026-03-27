

@extends('layouts.app')

@section('title', '{{ $record->month }} — FinTrack')
@section('page-title', $record->month)
@section('page-subtitle', 'Detailed breakdown & analysis')

@section('content')

{{-- Back + actions --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:10px;">
    <a href="{{ route('history') }}" class="btn-outline-custom" style="font-size:12.5px;padding:8px 14px;">
        <svg width="13" height="13" viewBox="0 0 13 13" fill="currentColor">
            <path d="M9 6.5H4M4 6.5l3-3M4 6.5l3 3" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" fill="none"/>
        </svg>
        Back to History
    </a>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('income.edit', $record->id) }}" class="btn-edit-custom" style="font-size:12.5px;padding:8px 14px;">
            <svg width="12" height="12" viewBox="0 0 12 12" fill="currentColor">
                <path d="M8.5 1.5l2 2L4 10 1.5 10.5 2 8l6.5-6.5z"/>
            </svg>
            Edit
        </a>
        <form action="{{ route('income.destroy', $record->id) }}" method="POST"
              onsubmit="return confirm('Delete {{ $record->month }}? This cannot be undone.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-danger-custom" style="font-size:12.5px;padding:8px 14px;">
                <svg width="12" height="12" viewBox="0 0 12 12" fill="currentColor">
                    <path d="M2 3h8M4.5 3V2h3v1M5 9V5m2 4V5M3 3l.5 7h5L9 3"/>
                </svg>
                Delete
            </button>
        </form>
    </div>
</div>

{{-- ─── STAT CARDS ──────────────────────────────────────────────────────────── --}}
@php
    // FIXED: total deductions = loan + expenses + misc (NOT savings)
    $totalDeductions = $record->loan + $record->expenses + $record->miscellaneous;
    $isPositive      = $record->savings >= 0;
@endphp

<div class="row g-3 mb-4">

    {{-- Income --}}
    <div class="col-6 col-md-3">
        <div class="stat-card green">
            <div class="stat-icon green">
                <svg width="17" height="17" viewBox="0 0 17 17" fill="none">
                    <path d="M2 12l4-5 4 3.5 5-8" stroke="currentColor" stroke-width="2"
                          stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="stat-label">Monthly Income</div>
            <div class="stat-value green">₹{{ number_format($record->income, 0) }}</div>
            <div class="stat-sub">Gross earnings</div>
        </div>
    </div>

    {{-- Total Spent (loan + expenses + misc) --}}
    <div class="col-6 col-md-3">
        <div class="stat-card red">
            <div class="stat-icon red">
                <svg width="17" height="17" viewBox="0 0 17 17" fill="currentColor">
                    <path d="M3 4h11l-1.5 8.5H4.5L3 4zm3 10a1 1 0 102 0 1 1 0 00-2 0zm5 0a1 1 0 102 0 1 1 0 00-2 0z"/>
                </svg>
            </div>
            <div class="stat-label">Total Spent</div>
            {{-- FIXED: savings excluded from "spent" --}}
            <div class="stat-value red">₹{{ number_format($totalDeductions, 0) }}</div>
            <div class="stat-sub">Loan + expenses + misc</div>
        </div>
    </div>

    {{-- Savings (auto-calculated result) --}}
    <div class="col-6 col-md-3">
        <div class="stat-card {{ $isPositive ? 'blue' : 'red' }}">
            <div class="stat-icon {{ $isPositive ? 'blue' : 'red' }}">
                <svg width="17" height="17" viewBox="0 0 17 17" fill="currentColor">
                    <path d="M14 8C14 5 12 3 10 3 8 3 6.5 4.6 6 6H4.5L6 9H5v2h1.5V13H7v-2h7V13h1.5V11H14V9h-1.5L15 6H14v2z"/>
                </svg>
            </div>
            <div class="stat-label">Savings This Month</div>
            {{-- FIXED: savings = the auto-calculated result (income − all expenses) --}}
            <div class="stat-value {{ $isPositive ? '' : 'red' }}" style="{{ $isPositive ? 'color:var(--accent-blue)' : '' }}">
                {{ $isPositive ? '' : '-' }}₹{{ number_format(abs($record->savings), 0) }}
            </div>
            <div class="stat-sub">
                {{ $record->income > 0
                    ? number_format(($record->savings / $record->income) * 100, 1)
                    : '0' }}% of income
            </div>
        </div>
    </div>

    {{-- Savings Rate indicator --}}
    <div class="col-6 col-md-3">
        <div class="stat-card {{ $isPositive ? 'green' : 'red' }}">
            <div class="stat-icon {{ $isPositive ? 'green' : 'red' }}">
                <svg width="17" height="17" viewBox="0 0 17 17" fill="currentColor">
                    <circle cx="8.5" cy="8.5" r="6.5" stroke="currentColor" stroke-width="1.5" fill="none"/>
                    <path d="M8.5 5.5v3l2 1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="stat-label">Status</div>
            <div class="stat-value {{ $isPositive ? 'green' : 'red' }}" style="font-size:20px;">
                {{ $isPositive ? 'Surplus' : 'Overspent' }}
            </div>
            <div class="stat-sub">
                {{ $isPositive ? 'You saved money this month' : 'Spent more than income' }}
            </div>
        </div>
    </div>
</div>

{{-- ─── CHART + BREAKDOWN ───────────────────────────────────────────────────── --}}
<div class="row g-4">

    {{-- DOUGHNUT CHART --}}
    <div class="col-lg-6">
        <div class="card-custom h-100">
            <div class="section-header">
                <div>
                    <div class="section-title">Income Allocation</div>
                    <div class="section-sub">{{ $record->month }}</div>
                </div>
                <span class="badge-custom {{ $isPositive ? 'badge-green' : 'badge-red' }}" style="font-size:10px;">
                    {{ $isPositive ? 'Surplus' : 'Overspent' }}
                </span>
            </div>

            <div style="position:relative;max-height:260px;display:flex;justify-content:center;">
                <canvas id="expenseChart"></canvas>
            </div>

            {{--
                LEGEND: Loan, Expenses, Misc, Savings (auto-calc)
                Savings = green slice = what's left after spending
            --}}
            @php
                $legendItems = [
                    ['Loan / EMI',    $record->loan,                 '#4f8ef7'],
                    ['Expenses',      $record->expenses,             '#f87171'],
                    ['Miscellaneous', $record->miscellaneous,        '#fbbf24'],
                    ['Savings',       max(0, $record->savings),      '#00d68f'],
                ];
            @endphp
            <div style="margin-top:18px;display:grid;grid-template-columns:1fr 1fr;gap:9px;">
                @foreach($legendItems as [$label, $val, $color])
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="width:9px;height:9px;border-radius:50%;background:{{ $color }};flex-shrink:0;"></div>
                    <span style="font-size:11.5px;color:var(--text-muted);">{{ $label }}</span>
                    <span style="font-size:11.5px;color:var(--text-primary);margin-left:auto;font-family:var(--font-mono);">
                        ₹{{ number_format($val, 0) }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- PROGRESS BAR BREAKDOWN --}}
    <div class="col-lg-6">
        <div class="card-custom h-100">
            <div class="section-header">
                <div>
                    <div class="section-title">Financial Breakdown</div>
                    <div class="section-sub">{{ $record->month }} · as % of income</div>
                </div>
            </div>

            {{--
                PROGRESS BAR ROWS (CORRECT):
                1. Income       — baseline (100%)
                2. Loan / EMI   — % of income (expense, blue)
                3. Expenses     — % of income (expense, red)
                4. Misc         — % of income (expense, yellow)
                ─────────────────────────────────────────────
                = Savings       — what's left (green) — the RESULT, shown last
            --}}
            @php
                $inc = (float)$record->income;
                $summaryRows = [
                    ['Monthly Income',  $inc,                   'var(--accent-green)',  100,   null],
                    ['Loan / EMI',      (float)$record->loan,   'var(--accent-blue)',   $inc > 0 ? min(100,($record->loan/$inc)*100) : 0,        $inc > 0 ? number_format(($record->loan/$inc)*100,1).'%' : '—'],
                    ['Expenses',        (float)$record->expenses,'var(--accent-red)',   $inc > 0 ? min(100,($record->expenses/$inc)*100) : 0,    $inc > 0 ? number_format(($record->expenses/$inc)*100,1).'%' : '—'],
                    ['Miscellaneous',   (float)$record->miscellaneous,'var(--accent-yellow)',$inc > 0 ? min(100,($record->miscellaneous/$inc)*100) : 0, $inc > 0 ? number_format(($record->miscellaneous/$inc)*100,1).'%' : '—'],
                ];
            @endphp

            <div style="display:flex;flex-direction:column;gap:14px;margin-bottom:18px;">
                @foreach($summaryRows as [$label, $value, $color, $pct, $tag])
                <div>
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div style="width:8px;height:8px;border-radius:50%;background:{{ $color }};flex-shrink:0;"></div>
                            <span style="font-size:12.5px;color:var(--text-muted);">{{ $label }}</span>
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;">
                            @if($tag)
                                <span style="font-size:10.5px;color:var(--text-dim);font-family:var(--font-mono);">{{ $tag }}</span>
                            @endif
                            <span style="font-size:13px;font-weight:600;color:var(--text-primary);font-family:var(--font-mono);">
                                ₹{{ number_format($value, 2) }}
                            </span>
                        </div>
                    </div>
                    <div class="progress-custom">
                        <div class="progress-fill" style="width:0%;background:{{ $color }};"
                             data-width="{{ $pct }}"></div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Savings result box --}}
            <div style="background:{{ $isPositive ? 'rgba(0,214,143,.07)' : 'rgba(248,113,113,.07)' }};
                        border:1px solid {{ $isPositive ? 'rgba(0,214,143,.2)' : 'rgba(248,113,113,.2)' }};
                        border-radius:var(--radius-md);
                        padding:15px 18px;">
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <div>
                        <div style="font-size:11px;color:var(--text-muted);font-family:var(--font-mono);
                                    text-transform:uppercase;letter-spacing:.5px;">
                            Your Savings (Result)
                        </div>
                        {{-- FIXED: formula shows correct derivation --}}
                        <div style="font-size:10.5px;color:var(--text-dim);margin-top:3px;font-family:var(--font-mono);">
                            Income − Loan − Expenses − Misc
                        </div>
                    </div>
                    <div style="font-size:22px;font-weight:700;font-family:var(--font-mono);
                                color:{{ $isPositive ? 'var(--accent-green)' : 'var(--accent-red)' }};">
                        {{ $isPositive ? '' : '-' }}₹{{ number_format(abs($record->savings), 2) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ─── METADATA CARD ───────────────────────────────────────────────────────── --}}
<div class="card-custom mt-4">
    <div class="section-title" style="margin-bottom:14px;">Record Details</div>
    <div class="row g-3">
        @php
            // FIXED: Expense Ratio = (loan+expenses+misc) / income (savings excluded)
            $expenseRatio = $inc > 0
                ? number_format(($totalDeductions / $inc) * 100, 1)
                : '0';

            // FIXED: Savings Rate = savings / income
            $savingsRate = $inc > 0
                ? number_format(($record->savings / $inc) * 100, 1)
                : '0';

            $meta = [
                ['Record ID',      '#' . $record->id,                            'var(--text-muted)'],
                ['Month',          $record->month,                                'var(--accent-green)'],
                ['Created',        $record->created_at->format('d M Y, h:i A'),  'var(--text-muted)'],
                ['Last Updated',   $record->updated_at->format('d M Y, h:i A'),  'var(--text-muted)'],
                ['Savings Rate',   $savingsRate . '%',                            $isPositive ? 'var(--accent-blue)' : 'var(--accent-red)'],
                ['Expense Ratio',  $expenseRatio . '%',                           'var(--accent-red)'],
            ];
        @endphp

        @foreach($meta as [$key, $val, $color])
        <div class="col-6 col-md-4">
            <div style="background:var(--bg-primary);border:1px solid var(--border-color);
                        border-radius:var(--radius-md);padding:13px 15px;">
                <div style="font-size:10px;color:var(--text-dim);font-family:var(--font-mono);
                            text-transform:uppercase;letter-spacing:1px;margin-bottom:5px;">
                    {{ $key }}
                </div>
                <div style="font-size:14px;font-weight:600;color:{{ $color }};font-family:var(--font-mono);">
                    {{ $val }}
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Animate progress bars
    window.addEventListener('load', () => {
        setTimeout(() => {
            document.querySelectorAll('.progress-fill[data-width]').forEach(el => {
                el.style.width = el.dataset.width + '%';
            });
        }, 150);
    });

    // Doughnut chart
    // SEGMENTS: Loan, Expenses, Miscellaneous, Savings (auto-calculated)
    new Chart(document.getElementById('expenseChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Loan/EMI', 'Expenses', 'Miscellaneous', 'Savings'],
            datasets: [{
                data: [
                    {{ $record->loan }},
                    {{ $record->expenses }},
                    {{ $record->miscellaneous }},
                    {{ max(0, $record->savings) }}  {{-- chart can't render negative slice --}}
                ],
                backgroundColor: [
                    'rgba(79,142,247,0.88)',   // loan — blue
                    'rgba(248,113,113,0.88)',  // expenses — red
                    'rgba(251,191,36,0.88)',   // misc — yellow
                    'rgba(0,214,143,0.88)',    // savings — green
                ],
                borderColor: '#161d2e',
                borderWidth: 3,
                hoverOffset: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '65%',
            animation: { animateRotate: true, duration: 900, easing: 'easeInOutQuart' },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#161d2e',
                    borderColor: '#1e2a42',
                    borderWidth: 1,
                    titleColor: '#f0f4ff',
                    bodyColor: '#6b7a99',
                    padding: 12,
                    callbacks: {
                        label: ctx => {
                            const val   = ctx.parsed;
                            const total = ctx.dataset.data.reduce((a,b)=>a+b,0);
                            const pct   = total > 0 ? ((val/total)*100).toFixed(1) : 0;
                            return ` ₹${val.toLocaleString('en-IN')} (${pct}%)`;
                        }
                    }
                }
            }
        }
    });
</script>
@endsection