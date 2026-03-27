{{--
    File: resources/views/dashboard.blade.php

    DATA MODEL (correct logic):
      Income = user's gross monthly earnings
      Loan / EMI, Expenses, Miscellaneous = real outflows (user-entered)
      Net Savings = Income - Loan - Expenses - Misc  (auto-calculated)

    Chart slices: Loan | Expenses | Miscellaneous | Net Savings
    (There is no separate "remaining" slice — savings IS what remains)
--}}
@extends('layouts.app')
@section('title', 'Dashboard — FinTrack')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Financial overview & insights')

@section('content')

{{-- STAT CARDS --}}
<div class="row g-3 mb-4">
  <div class="col-6 col-lg-3">
    <div class="stat-card green">
      <div class="stat-icon green">
        <svg width="17" height="17" viewBox="0 0 17 17" fill="currentColor">
          <path d="M2 12l4-5 4 3.5 5-6.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
        </svg>
      </div>
      <div class="stat-label">Total Income</div>
      <div class="stat-value green">&#8377;{{ number_format($totalIncome, 0) }}</div>
      <div class="stat-sub">Cumulative gross earnings</div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="stat-card blue">
      <div class="stat-icon blue">
        <svg width="17" height="17" viewBox="0 0 17 17" fill="currentColor">
          <path d="M3 5h11v9H3zm0-2a1.5 1.5 0 011.5-1.5h8A1.5 1.5 0 0114 3H3z"/>
          <line x1="6" y1="9" x2="11" y2="9" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
      </div>
      <div class="stat-label">Total Net Savings</div>
      <div class="stat-value" style="color:var(--accent-blue)">&#8377;{{ number_format($totalSavings, 0) }}</div>
      <div class="stat-sub">Income minus all deductions</div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="stat-card red">
      <div class="stat-icon red">
        <svg width="17" height="17" viewBox="0 0 17 17" fill="currentColor">
          <path d="M3 4h11l-1.5 9H4.5L3 4zm3 10a1 1 0 102 0 1 1 0 00-2 0zm5 0a1 1 0 102 0 1 1 0 00-2 0z"/>
        </svg>
      </div>
      {{-- Total Expenses = loan + expenses + misc (NOT savings) --}}
      <div class="stat-label">Total Deductions</div>
      <div class="stat-value red">&#8377;{{ number_format($totalExpenses, 0) }}</div>
      <div class="stat-sub">Loan + Expenses + Misc</div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="stat-card purple">
      <div class="stat-icon purple">
        <svg width="17" height="17" viewBox="0 0 17 17" fill="currentColor">
          <rect x="2" y="2" width="5.5" height="5.5" rx="1"/>
          <rect x="9.5" y="2" width="5.5" height="5.5" rx="1"/>
          <rect x="2" y="9.5" width="5.5" height="5.5" rx="1"/>
          <rect x="9.5" y="9.5" width="5.5" height="5.5" rx="1"/>
        </svg>
      </div>
      <div class="stat-label">Months Tracked</div>
      <div class="stat-value" style="color:var(--accent-purple)">{{ $totalCount }}</div>
      <div class="stat-sub">Records in database</div>
    </div>
  </div>
</div>

@if($latest)

<div class="row g-4">
  {{-- CHART --}}
  <div class="col-lg-6">
    <div class="card-custom h-100">
      <div class="section-header">
        <div>
          <div class="section-title">Spending Breakdown</div>
          <div class="section-sub" id="chartMonthLabel">Latest: {{ $latest->month }}</div>
        </div>
        <span class="badge-custom badge-green" style="font-size:10px;">Live</span>
      </div>
      <div style="position:relative;max-height:260px;display:flex;justify-content:center;">
        <canvas id="expenseChart"></canvas>
      </div>
      <div id="chartLegend" style="margin-top:18px;display:grid;grid-template-columns:1fr 1fr;gap:9px;"></div>
    </div>
  </div>

  {{-- SUMMARY --}}
  <div class="col-lg-6">
    <div class="card-custom h-100">
      <div class="section-header">
        <div>
          <div class="section-title">Financial Summary</div>
          <div class="section-sub" id="summaryMonthLabel">{{ $latest->month }}</div>
        </div>
        <a href="{{ route('income.edit', $latest->id) }}" id="editBtn" class="btn-edit-custom">
          <svg width="11" height="11" viewBox="0 0 12 12" fill="currentColor">
            <path d="M8.5 1.5l2 2L4 10 1.5 10.5 2 8l6.5-6.5z"/>
          </svg>
          Edit
        </a>
      </div>
      <div id="summaryRows" style="display:flex;flex-direction:column;gap:12px;margin-bottom:18px;"></div>
      <div id="savingsBox" style="border-radius:var(--radius-md);padding:15px 18px;">
        <div style="display:flex;align-items:center;justify-content:space-between;">
          <div>
            <div style="font-size:11px;color:var(--text-muted);font-family:var(--font-mono);text-transform:uppercase;letter-spacing:.5px;">
              Net Savings
            </div>
            <div style="font-size:10.5px;color:var(--text-dim);margin-top:3px;">
              Income &minus; Loan &minus; Expenses &minus; Misc
            </div>
          </div>
          <div id="savingsValue" style="font-size:22px;font-weight:700;font-family:var(--font-mono);"></div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- RECENT RECORDS TABLE --}}
<div class="card-custom mt-4">
  <div class="section-header">
    <div>
      <div class="section-title">Recent Records</div>
      <div class="section-sub">Click any row to update chart &amp; summary above</div>
    </div>
    <a href="{{ route('history') }}" class="btn-outline-custom" style="font-size:12.5px;padding:7px 14px;">
      View All
      <svg width="11" height="11" viewBox="0 0 12 12" fill="currentColor">
        <path d="M4 2l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" fill="none"/>
      </svg>
    </a>
  </div>

  @if($records->count())
  <div style="overflow-x:auto;">
    <table class="table-custom">
      <thead>
        <tr>
          <th>Month</th>
          <th>Income</th>
          <th>Loan/EMI</th>
          <th>Expenses</th>
          <th>Misc</th>
          <th>Net Savings</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody id="dashTableBody">
        @foreach($records as $rec)
        <tr class="clickable-row" data-id="{{ $rec->id }}" onclick="selectRecord({{ $rec->id }})"
            style="cursor:pointer;" title="Click to view chart for {{ $rec->month }}">
          <td style="font-weight:600;font-size:13px;">{{ $rec->month }}</td>
          <td style="font-family:var(--font-mono);color:var(--accent-green);">&#8377;{{ number_format($rec->income, 0) }}</td>
          <td style="font-family:var(--font-mono);color:var(--text-muted);">&#8377;{{ number_format($rec->loan, 0) }}</td>
          <td style="font-family:var(--font-mono);color:var(--text-muted);">&#8377;{{ number_format($rec->expenses, 0) }}</td>
          <td style="font-family:var(--font-mono);color:var(--text-muted);">&#8377;{{ number_format($rec->miscellaneous, 0) }}</td>
          <td style="font-family:var(--font-mono);font-weight:600;
                     color:{{ $rec->savings >= 0 ? 'var(--accent-green)' : 'var(--accent-red)' }};">
            &#8377;{{ number_format($rec->savings, 0) }}
          </td>
          <td>
            @if($rec->savings >= 0)
              <span class="badge-custom badge-green">Surplus</span>
            @else
              <span class="badge-custom badge-red">Deficit</span>
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div style="margin-top:9px;font-size:11px;color:var(--text-dim);font-family:var(--font-mono);text-align:center;">
    &uarr; Click any row to preview its chart above
  </div>
  @else
  <div class="empty-state">
    <div class="empty-icon">
      <svg width="44" height="44" viewBox="0 0 48 48" fill="none">
        <rect x="4" y="8" width="40" height="32" rx="4" stroke="currentColor" stroke-width="2" stroke-dasharray="4 3"/>
        <path d="M14 30l8-10 8 7 10-13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </div>
    <div class="empty-title">No records yet</div>
    <div class="empty-sub">Add your first monthly record to see data here</div>
    <a href="{{ route('income.create') }}" class="btn-primary-custom">Add First Record</a>
  </div>
  @endif
</div>

@else
<div class="card-custom" style="text-align:center;padding:72px 20px;">
  <div style="font-size:56px;color:var(--text-dim);margin-bottom:18px;">
    <svg width="64" height="64" viewBox="0 0 64 64" fill="none" style="margin:0 auto;display:block;">
      <rect x="4" y="10" width="56" height="44" rx="6" stroke="currentColor" stroke-width="2.5" stroke-dasharray="5 4"/>
      <path d="M14 44l10-14 10 8L48 22" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
  </div>
  <h2 style="font-size:20px;font-weight:700;color:var(--text-muted);margin-bottom:10px;">No Financial Data Yet</h2>
  <p style="font-size:13.5px;color:var(--text-dim);max-width:360px;margin:0 auto 26px;">
    Add your first monthly record to see your income dashboard, charts, and insights.
  </p>
  <a href="{{ route('income.create') }}" class="btn-primary-custom" style="font-size:14px;padding:12px 30px;">
    Add First Record
  </a>
</div>
@endif

@endsection

@section('scripts')
@if($latest)
<script>


// All records passed from Laravel for client-side chart switching
@php
$recordsArray = $allRecords->map(function($r) {
    return [
        'id'            => $r->id,
        'month'         => $r->month,
        'income'        => (float)$r->income,
        'loan'          => (float)$r->loan,
        'expenses'      => (float)$r->expenses,
        'miscellaneous' => (float)$r->miscellaneous,
        'savings'       => (float)$r->savings,
    ];
});
@endphp

const ALL_RECORDS = @json($recordsArray);
const RECORD_MAP = {};
ALL_RECORDS.forEach(r => RECORD_MAP[r.id] = r);

// Chart colours: Loan | Expenses | Misc | Net Savings
const COLORS = [
  'rgba(248,113,113,0.88)',  // loan — red
  'rgba(251,191,36,0.88)',   // expenses — yellow
  'rgba(139,92,246,0.88)',   // misc — purple
  'rgba(0,214,143,0.88)',    // savings — green (last, so it's positive/last segment)
];
const DOT_COLORS = ['#f87171','#fbbf24','#8b5cf6','#00d68f'];

const ctx = document.getElementById('expenseChart').getContext('2d');
const chart = new Chart(ctx, {
  type: 'doughnut',
  data: {
    labels: ['Loan/EMI','Expenses','Miscellaneous','Net Savings'],
    datasets: [{
      data: [0,0,0,0],
      backgroundColor: COLORS,
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
        backgroundColor:'#161d2e', borderColor:'#1e2a42', borderWidth:1,
        titleColor:'#f0f4ff', bodyColor:'#6b7a99', padding:12,
        callbacks: {
          label: ctx => {
            const val = ctx.parsed;
            const total = ctx.dataset.data.reduce((a,b)=>a+b,0);
            const pct = total>0?((val/total)*100).toFixed(1):0;
            return ` ₹${val.toLocaleString('en-IN')} (${pct}%)`;
          }
        }
      }
    }
  }
});

function renderSummary(rec) {
  // Progress bar rows — only real deductions + savings row at end
  const rows = [
    { label:'Monthly Income', val:rec.income,        color:'var(--accent-green)',  pct:100 },
    { label:'Loan / EMI',     val:rec.loan,           color:'var(--accent-red)',    pct: rec.income>0?Math.min(100,(rec.loan/rec.income)*100):0 },
    { label:'Expenses',       val:rec.expenses,       color:'var(--accent-yellow)', pct: rec.income>0?Math.min(100,(rec.expenses/rec.income)*100):0 },
    { label:'Miscellaneous',  val:rec.miscellaneous,  color:'var(--accent-purple)', pct: rec.income>0?Math.min(100,(rec.miscellaneous/rec.income)*100):0 },
  ];

  document.getElementById('summaryRows').innerHTML = rows.map(r => `
    <div>
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:5px;">
        <div style="display:flex;align-items:center;gap:8px;">
          <div style="width:8px;height:8px;border-radius:50%;background:${r.color};flex-shrink:0;"></div>
          <span style="font-size:12.5px;color:var(--text-muted);">${r.label}</span>
        </div>
        <span style="font-size:13px;font-weight:600;color:var(--text-primary);font-family:var(--font-mono);">
          &#8377;${r.val.toLocaleString('en-IN',{minimumFractionDigits:2,maximumFractionDigits:2})}
        </span>
      </div>
      <div class="progress-custom">
        <div class="progress-fill" style="width:${r.pct}%;background:${r.color};"></div>
      </div>
    </div>
  `).join('');

  // Net savings box
  const sav = rec.savings;
  const pos  = sav >= 0;
  const box  = document.getElementById('savingsBox');
  const valEl= document.getElementById('savingsValue');
  box.style.background  = pos?'rgba(0,214,143,.07)':'rgba(248,113,113,.07)';
  box.style.border      = `1px solid ${pos?'rgba(0,214,143,.2)':'rgba(248,113,113,.2)'}`;
  valEl.style.color     = pos?'var(--accent-green)':'var(--accent-red)';
  valEl.textContent     = (pos?'':'−') + '₹' + Math.abs(sav).toLocaleString('en-IN',{minimumFractionDigits:2,maximumFractionDigits:2});
}

function renderLegend(rec) {
  const items = [
    ['Loan/EMI',      rec.loan,          DOT_COLORS[0]],
    ['Expenses',      rec.expenses,      DOT_COLORS[1]],
    ['Miscellaneous', rec.miscellaneous, DOT_COLORS[2]],
    ['Net Savings',   Math.max(0,rec.savings), DOT_COLORS[3]],
  ];
  document.getElementById('chartLegend').innerHTML = items.map(([l,v,c]) => `
    <div style="display:flex;align-items:center;gap:7px;">
      <div style="width:9px;height:9px;border-radius:50%;background:${c};flex-shrink:0;"></div>
      <span style="font-size:11.5px;color:var(--text-muted);">${l}</span>
      <span style="font-size:11.5px;color:var(--text-primary);margin-left:auto;font-family:var(--font-mono);">
        &#8377;${v.toLocaleString('en-IN')}
      </span>
    </div>
  `).join('');
}

function updateDashboard(rec) {
  // Chart uses only 4 slices: loan, expenses, misc, savings (no "remaining")
  chart.data.datasets[0].data = [
    rec.loan,
    rec.expenses,
    rec.miscellaneous,
    Math.max(0, rec.savings)  // negative savings = 0 in chart (no negative slice)
  ];
  chart.update();

  document.getElementById('chartMonthLabel').textContent    = 'Showing: ' + rec.month;
  document.getElementById('summaryMonthLabel').textContent  = rec.month;
  document.getElementById('editBtn').href = `/income/${rec.id}/edit`;

  renderSummary(rec);
  renderLegend(rec);

  // Highlight selected row
  document.querySelectorAll('#dashTableBody tr').forEach(tr => {
    const active = parseInt(tr.dataset.id) === rec.id;
    tr.style.background  = active ? 'rgba(0,214,143,.06)' : '';
    tr.style.borderLeft  = active ? '3px solid var(--accent-green)' : '';
    tr.style.outline     = active ? '1px solid rgba(0,214,143,.18)' : '';
  });
}

function selectRecord(id) {
  const r = RECORD_MAP[id];
  if (r) updateDashboard(r);
}

// Init with latest record
updateDashboard(RECORD_MAP[{{ $latest->id }}]);

</script>
@endif
@endsection