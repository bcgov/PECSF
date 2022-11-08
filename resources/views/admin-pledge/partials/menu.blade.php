<div class="button-group">
    <a href="/admin-pledge/create" class="btn btn-primary">Add a New Event Pledge</a>
        <a
           id="pills-home-tab" style="color:#1a5a96;background:transparent;font-weight:bold;text-decoration: none;" class="{{ str_contains(Route::current()->getName(), 'admin-pledge.submission-queue') ? 'active' : '' }} btn btn-secondary activewhite"  href="{{ route('admin-pledge.submission-queue.index') }}">PECSF Event Submission Queue</a>
</div>
