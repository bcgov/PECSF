<ul class="nav nav-pills mb-3" id="pills-tab" >
    <li class="nav-item">
        <a class="nav-link {{ str_contains( Route::current()->getName(), 'bank_deposit_form') ? 'active' : ''}}"
           {{-- id="pills-home-tab"  --}}
           href="{{ route('bank_deposit_form') }}" role="tab" aria-controls="pills-home" aria-selected="true">PECSF Event Bank Deposit Form</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ str_contains( Route::current()->getName(), 'volunteering.supply_order_form') ? 'active' : '' }}"
           {{-- id="pills-profile-tab"  --}}
           href="#"  aria-controls="pills-profile" aria-selected="false">Supply Order Form</a>
    </li>
</ul>
