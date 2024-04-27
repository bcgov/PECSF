<ul class="mt-3 menu nav nav-pills" id="pills-tab">
  <li class="nav-item nav-center-4">
      <a  class="nav-link {{ str_contains( Route::current()->getName(), 'challenge.index') ? 'active disabled' : ''}}"
         href="{{ route('challenge.index') }}" role="tab" aria-controls="pills-home" aria-selected="false">
          Leaderboard</a>
  </li>
  <li class="nav-item nav-center-4">
      <a  class="nav-link {{ str_contains( Route::current()->getName(), 'challenge.daily_campaign') ? 'active disabled' : ''}}"
         href="{{  route('challenge.daily_campaign') }}" role="tab" aria-controls="pills-profile" aria-selected="true">
          Daily Campaign Update</a>
  </li>
  <li class="nav-item nav-center-4">
      <a class="nav-link {{ str_contains( Route::current()->getName(), 'challenge.org_participation_tracker') ? 'active disabled' : ''}}" 
          href="{{  route('challenge.org_participation_tracker') }}" role="tab" aria-controls="pills-tracker" aria-selected="false">
          Organization Participation Tracker</a>
  </li>
</ul>
