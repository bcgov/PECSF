<ul class="menu nav nav-pills mb-3" id="pills-tab" >

    <li class="nav-item dropdown">
      @php $active =  ( str_contains(Route::current()->getName(), 'reporting.donation-upload') ||
                        str_contains(Route::current()->getName(), 'reporting.donation-data')
                      ) ? 'active' : ''
      @endphp
      <a class="nav-link dropdown-toggle {{ $active }}" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Pay Period Report</a>
      <div class="dropdown-menu">
        <a class="dropdown-item {{ str_contains( Route::current()->getName(), 'reporting.donation-upload') ? 'active' : ''}}"
              href="{{ route('reporting.donation-upload.index') }}">Upload Donation Data</a>
        <a class="dropdown-item {{ str_contains( Route::current()->getName(), 'reporting.donation-data') ? 'active' : ''}}"
              href="{{ route('reporting.donation-data.index') }}">Review Donation Data </a>
      </div>
    </li>

    <li class="nav-item dropdown">
      @php $active =  ( 
                        str_contains(Route::current()->getName(), 'reporting.pledges') ||
                        str_contains(Route::current()->getName(), 'reporting.pledge-charities') ||
                        str_contains(Route::current()->getName(), 'reporting.eligible-employees') ||
                        str_contains(Route::current()->getName(), 'reporting.gaming-and-fundrasing') ||
                        str_contains(Route::current()->getName(), 'reporting.cra-charities') ||
                        str_contains(Route::current()->getName(), 'reporting.challenge-page-data')
                      ) ? 'active' : ''
      @endphp
      <a class="nav-link dropdown-toggle {{ $active }}" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Program reports</a>
      <div class="dropdown-menu">
        <a class="dropdown-item {{ str_contains( Route::current()->getName(), 'reporting.pledges') ? 'active' : ''}}"
                href="{{ route('reporting.pledges.index') }}" role="tab" aria-controls="pills-home" aria-selected="true">Annual and Event Pledges</a>
        <a class="dropdown-item {{ str_contains( Route::current()->getName(), 'reporting.pledge-charities') ? 'active' : ''}}"
                href="{{ route('reporting.pledge-charities.index') }}" role="tab" aria-controls="pills-home" aria-selected="true">Amount by Charity</a>              
        <div class="dropdown-divider"></div>                              
        <a class="dropdown-item {{ str_contains( Route::current()->getName(), 'reporting.gaming-and-fundrasing') ? 'active' : ''}}"
          href="{{ route('reporting.gaming-and-fundrasing.index') }}" role="tab" aria-controls="pills-home" aria-selected="true">Gaming and Fundrasing Pledges</a>              
        <div class="dropdown-divider"></div>    
        <a class="dropdown-item {{ str_contains( Route::current()->getName(), 'reporting.cra-charities') ? 'active' : ''}}"
          href="{{ route('reporting.cra-charities.index') }}" role="tab" aria-controls="pills-home" aria-selected="true">Charity</a>              
        <div class="dropdown-divider"></div>                              
        <a class="dropdown-item {{ str_contains( Route::current()->getName(), 'reporting.eligible-employees') ? 'active' : ''}}"
              href="{{ route('reporting.eligible-employees.index') }}" role="tab" aria-controls="pills-home" aria-selected="true">Eligible Employee Report</a>
        <div class="dropdown-divider"></div>                                 
        <a class="dropdown-item {{ str_contains( Route::current()->getName(), 'reporting.challenge-page-data') ? 'active' : ''}}"
          href="{{ route('reporting.challenge-page-data') }}" role="tab" aria-controls="pills-home" aria-selected="true">Challenge Page Data</a>
      </div>
    </li>

    {{-- <li class="nav-item">
      <a class="nav-link {{ str_contains( Route::current()->getName(), 'reporting.eligible-employees') ? 'active' : ''}}"
        href="{{ route('reporting.eligible-employees.index') }}" role="tab" aria-controls="pills-home" aria-selected="true">Eligible Employee Report</a>
    </li> --}}

    <li class="nav-item">
        <a class="nav-link {{ str_contains( Route::current()->getName(), 'reporting.supply-report') ? 'active' : ''}}"
           {{-- id="pills-home-tab"  --}}
           href="{{ route('reporting.supply-report.index') }}" role="tab" aria-controls="pills-home" aria-selected="true">Supply Order Report</a>
    </li>
</ul>


