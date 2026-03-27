
@extends('layouts.app')
@section('title', 'Add Monthly Record — FinTrack')
@section('page-title', 'Add Monthly Data')
@section('page-subtitle', 'Enter your income and expenses for the month')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-8 col-xl-7">

    {{-- Info banner --}}
    <div style="background:rgba(79,142,247,.08);border:1px solid rgba(79,142,247,.2);
                border-left:4px solid var(--accent-blue);border-radius:var(--radius-md);
                padding:13px 18px;margin-bottom:20px;display:flex;align-items:flex-start;gap:12px;">
      <svg width="16" height="16" viewBox="0 0 16 16" fill="var(--accent-blue)" style="flex-shrink:0;margin-top:2px;">
        <path d="M8 1a7 7 0 100 14A7 7 0 008 1zm.75 10.5h-1.5v-5h1.5v5zm0-6.5h-1.5V3.5h1.5V5z"/>
      </svg>
      <div>
        <div style="font-size:12.5px;font-weight:600;color:var(--accent-blue);margin-bottom:3px;">How savings are calculated</div>
        <div style="font-size:12px;color:var(--text-muted);line-height:1.6;">
          <strong style="color:var(--text-primary);">Net Savings = Income &minus; Loan/EMI &minus; Expenses &minus; Miscellaneous</strong><br>
          You never enter savings manually &mdash; the system calculates what is left after all deductions.
        </div>
      </div>
    </div>

    <div class="card-custom">
      <div style="display:flex;align-items:center;gap:12px;margin-bottom:24px;">
        <div class="stat-icon green" style="margin:0;width:36px;height:36px;">
          <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
            <path d="M8 1a7 7 0 100 14A7 7 0 008 1zm1 10H7V9H5V7h2V5h2v2h2v2H9v2z"/>
          </svg>
        </div>
        <div>
          <div class="section-title">New Monthly Record</div>
          <div class="section-sub">All amounts in Indian Rupees (&curren;)</div>
        </div>
      </div>

      <form action="{{ route('income.store') }}" method="POST">
        @csrf

        {{-- Month picker --}}
        <div style="margin-bottom:22px;">
          <label class="form-label-custom">
            <svg width="12" height="12" viewBox="0 0 12 12" fill="currentColor" style="margin-right:5px;opacity:.7;">
              <rect x="1" y="2" width="10" height="9" rx="1.5" stroke="currentColor" stroke-width="1.2" fill="none"/>
              <line x1="1" y1="5" x2="11" y2="5" stroke="currentColor" stroke-width="1.2"/>
              <rect x="3.5" y=".5" width="1" height="2.5" rx=".5" fill="currentColor"/>
              <rect x="7.5" y=".5" width="1" height="2.5" rx=".5" fill="currentColor"/>
            </svg>
            Month &amp; Year <span style="color:var(--accent-red)">*</span>
          </label>
          <div style="position:relative;max-width:260px;">
            <input type="month" id="month_picker" class="form-control-custom"
                   min="2020-01" max="2030-12"
                   value="{{ old('month_picker') }}"
                   onchange="syncMonth(this)" onclick="this.showPicker()" required/>
            
          </div>
          <input type="hidden" name="month" id="month" value="{{ old('month') }}"/>
          <div style="font-size:11px;color:var(--text-dim);margin-top:5px;font-family:var(--font-mono);">
            Selected: <span id="monthDisplay" style="color:var(--text-muted);">{{ old('month') ?: '&mdash;' }}</span>
          </div>
          @error('month')<div style="font-size:12px;color:var(--accent-red);margin-top:4px;">{{ $message }}</div>@enderror
        </div>

        {{-- Divider: Income --}}
        <div style="display:flex;align-items:center;gap:10px;margin:18px 0 16px;">
          <div style="flex:1;height:1px;background:var(--border-color);"></div>
          <span style="font-size:10px;color:var(--text-dim);font-family:var(--font-mono);text-transform:uppercase;letter-spacing:1px;">Income</span>
          <div style="flex:1;height:1px;background:var(--border-color);"></div>
        </div>

        {{-- Income --}}
        <div style="margin-bottom:20px;">
          <label class="form-label-custom">
            <svg width="12" height="12" viewBox="0 0 12 12" fill="var(--accent-green)" style="margin-right:4px;opacity:.9;">
              <path d="M6 1a5 5 0 100 10A5 5 0 006 1zm.5 7.5H5.5V7H4V6h1.5V4.5h1V6H8v1H6.5v1.5z"/>
            </svg>
            Total Monthly Income <span style="color:var(--accent-red)">*</span>
          </label>
          <div class="input-prefix">
            <span class="prefix-icon">&#8377;</span>
            <input type="number" name="income" id="income" class="form-control-custom"
                   placeholder="e.g. 50000" step="0.01" min="0"
                   value="{{ old('income') }}" oninput="recalc()" required/>
          </div>
          @error('income')<div style="font-size:12px;color:var(--accent-red);margin-top:4px;">{{ $message }}</div>@enderror
        </div>

        {{-- Divider: Deductions --}}
        <div style="display:flex;align-items:center;gap:10px;margin:18px 0 16px;">
          <div style="flex:1;height:1px;background:var(--border-color);"></div>
          <span style="font-size:10px;color:var(--text-dim);font-family:var(--font-mono);text-transform:uppercase;letter-spacing:1px;">Deductions</span>
          <div style="flex:1;height:1px;background:var(--border-color);"></div>
        </div>

        <div class="row g-3 mb-3">
          <div class="col-sm-6">
            <label class="form-label-custom">
              <svg width="12" height="12" viewBox="0 0 12 12" fill="var(--accent-red)" style="margin-right:4px;opacity:.9;">
                <path d="M2 4.5h8v6H2zm0-2a1 1 0 011-1h6a1 1 0 011 1H2z"/>
              </svg>
              Loan / EMI
            </label>
            <div class="input-prefix">
              <span class="prefix-icon">&#8377;</span>
              <input type="number" name="loan" id="loan" class="form-control-custom"
                     placeholder="0.00" step="0.01" min="0"
                     value="{{ old('loan', 0) }}" oninput="recalc()"/>
            </div>
            @error('loan')<div style="font-size:12px;color:var(--accent-red);margin-top:4px;">{{ $message }}</div>@enderror
          </div>
          <div class="col-sm-6">
            <label class="form-label-custom">
              <svg width="12" height="12" viewBox="0 0 12 12" fill="var(--accent-red)" style="margin-right:4px;opacity:.9;">
                <path d="M2 2.5h8L8.8 9H3.2L2 2.5zm2 8a.8.8 0 101.6 0A.8.8 0 004 10.5zm3.5 0a.8.8 0 101.6 0A.8.8 0 007.5 10.5z"/>
              </svg>
              General Expenses
            </label>
            <div class="input-prefix">
              <span class="prefix-icon">&#8377;</span>
              <input type="number" name="expenses" id="expenses" class="form-control-custom"
                     placeholder="0.00" step="0.01" min="0"
                     value="{{ old('expenses', 0) }}" oninput="recalc()"/>
            </div>
            @error('expenses')<div style="font-size:12px;color:var(--accent-red);margin-top:4px;">{{ $message }}</div>@enderror
          </div>
        </div>

        <div style="margin-bottom:22px;">
          <label class="form-label-custom">
            <svg width="12" height="12" viewBox="0 0 12 12" fill="var(--accent-yellow)" style="margin-right:4px;opacity:.9;">
              <circle cx="2.5" cy="6" r="1.1"/><circle cx="6" cy="6" r="1.1"/><circle cx="9.5" cy="6" r="1.1"/>
            </svg>
            Miscellaneous Expenses
          </label>
          <div class="input-prefix">
            <span class="prefix-icon">&#8377;</span>
            <input type="number" name="miscellaneous" id="miscellaneous" class="form-control-custom"
                   placeholder="0.00" step="0.01" min="0"
                   value="{{ old('miscellaneous', 0) }}" oninput="recalc()"/>
          </div>
          @error('miscellaneous')<div style="font-size:12px;color:var(--accent-red);margin-top:4px;">{{ $message }}</div>@enderror
        </div>

        {{-- Live calculation preview --}}
        <div style="margin-bottom:26px;">
          <div style="font-size:10px;color:var(--text-dim);font-family:var(--font-mono);text-transform:uppercase;letter-spacing:1px;margin-bottom:8px;">
            Live Savings Calculation
          </div>
          <div style="background:var(--bg-primary);border:1px solid var(--border-color);
                      border-radius:var(--radius-md);padding:14px 18px;margin-bottom:10px;">
            <div style="display:flex;flex-direction:column;gap:7px;">
              <div style="display:flex;justify-content:space-between;font-size:12.5px;">
                <span style="color:var(--text-muted);">Monthly Income</span>
                <span id="p_income" style="color:var(--accent-green);font-family:var(--font-mono);font-weight:600;">&#8377;0.00</span>
              </div>
              <div style="display:flex;justify-content:space-between;font-size:12.5px;">
                <span style="color:var(--text-muted);">&minus; Loan / EMI</span>
                <span id="p_loan" style="color:var(--accent-red);font-family:var(--font-mono);">&#8377;0.00</span>
              </div>
              <div style="display:flex;justify-content:space-between;font-size:12.5px;">
                <span style="color:var(--text-muted);">&minus; General Expenses</span>
                <span id="p_exp" style="color:var(--accent-red);font-family:var(--font-mono);">&#8377;0.00</span>
              </div>
              <div style="display:flex;justify-content:space-between;font-size:12.5px;">
                <span style="color:var(--text-muted);">&minus; Miscellaneous</span>
                <span id="p_misc" style="color:var(--accent-yellow);font-family:var(--font-mono);">&#8377;0.00</span>
              </div>
              <div style="height:1px;background:var(--border-color);margin:3px 0;"></div>
              <div style="display:flex;justify-content:space-between;font-size:13.5px;font-weight:700;">
                <span style="color:var(--text-primary);">= Net Savings</span>
                <span id="p_savings" style="font-family:var(--font-mono);color:var(--accent-green);">&#8377;0.00</span>
              </div>
            </div>
          </div>
          <div id="savingsBox" style="background:rgba(0,214,143,.07);border:1px solid rgba(0,214,143,.2);
                                      border-radius:var(--radius-md);padding:14px 20px;
                                      display:flex;align-items:center;justify-content:space-between;">
            <div>
              <div style="font-size:11px;color:var(--text-muted);font-family:var(--font-mono);text-transform:uppercase;letter-spacing:.8px;">
                Net Savings (Auto-Calculated)
              </div>
              <div style="font-size:10.5px;color:var(--text-dim);margin-top:3px;">
                Saved automatically &mdash; no manual entry needed
              </div>
            </div>
            <div id="savingsValue" style="font-size:24px;font-weight:700;font-family:var(--font-mono);color:var(--accent-green);">
              &#8377;0.00
            </div>
          </div>
        </div>

        <div style="display:flex;gap:12px;flex-wrap:wrap;">
          <button type="submit" class="btn-primary-custom" style="flex:1;justify-content:center;">
            <svg width="13" height="13" viewBox="0 0 13 13" fill="currentColor">
              <path d="M2 2h6.5l2.5 2.5V11H2V2zm4.5 1v3h3M4 8h5"/>
            </svg>
            Save Record
          </button>
          <a href="{{ route('dashboard') }}" class="btn-outline-custom" style="flex:1;justify-content:center;">
            Cancel
          </a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
const MONTHS = ['January','February','March','April','May','June',
                'July','August','September','October','November','December'];

function syncMonth(el) {
  if (!el.value) { document.getElementById('month').value=''; document.getElementById('monthDisplay').textContent='—'; return; }
  const [y,m] = el.value.split('-');
  const r = MONTHS[parseInt(m)-1] + ' ' + y;
  document.getElementById('month').value = r;
  document.getElementById('monthDisplay').textContent = r;
}

function fmt(n) {
  return '₹' + Math.abs(n).toLocaleString('en-IN',{minimumFractionDigits:2,maximumFractionDigits:2});
}

function recalc() {
  const income = parseFloat(document.getElementById('income').value)||0;
  const loan   = parseFloat(document.getElementById('loan').value)||0;
  const exp    = parseFloat(document.getElementById('expenses').value)||0;
  const misc   = parseFloat(document.getElementById('miscellaneous').value)||0;
  const sav    = income - loan - exp - misc;
  const pos    = sav >= 0;

  document.getElementById('p_income').textContent  = fmt(income);
  document.getElementById('p_loan').textContent    = fmt(loan);
  document.getElementById('p_exp').textContent     = fmt(exp);
  document.getElementById('p_misc').textContent    = fmt(misc);

  const disp = (pos?'':'-') + fmt(sav);
  document.getElementById('p_savings').textContent  = disp;
  document.getElementById('savingsValue').textContent = disp;
  document.getElementById('p_savings').style.color    = pos?'var(--accent-green)':'var(--accent-red)';
  document.getElementById('savingsValue').style.color  = pos?'var(--accent-green)':'var(--accent-red)';

  const box = document.getElementById('savingsBox');
  box.style.borderColor = pos?'rgba(0,214,143,.2)':'rgba(248,113,113,.2)';
  box.style.background  = pos?'rgba(0,214,143,.07)':'rgba(248,113,113,.07)';
}

@if(old('month_picker'))
  document.getElementById('month_picker').value = '{{ old("month_picker") }}';
  syncMonth(document.getElementById('month_picker'));
@endif

recalc();
</script>
@endsection