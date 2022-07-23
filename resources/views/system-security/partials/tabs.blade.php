<ul class="nav nav-pills mb-3" id="pills-tab" >
    <li class="nav-item">
      <a class="dropdown-item {{ str_contains( Route::current()->getName(), 'settings.schedule-job-audits') ? 'active' : ''}}"
        href="{{ route('settings.schedule-job-audits.index') }}">Schedule Job Audit Logs</a>
    </li>

    <li class="nav-item">
      <a class="dropdown-item {{ str_contains( Route::current()->getName(), 'settings.access-logs') ? 'active' : ''}}"
        href="{{ route('settings.access_logs') }}">Access Logs</a>      
    </li>

</ul>
