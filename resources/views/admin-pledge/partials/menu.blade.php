<div class="button-group">

    <a href="/admin-pledge/create"><button class="btn btn-primary">Add a New Event Pledge</button></a>
    <button style="color:#1a5a96;background:transparent;font-weight:bold;text-decoration: none;" class="{{ str_contains(Route::current()->getName(), 'admin-pledge.submission-queue') ? 'active' : '' }} btn btn-secondary">
        <a
           id="pills-home-tab" class="activewhite" href="{{ route('admin-pledge.submission-queue.index') }}">PECSF Event Submission Queue</a>
    </button>
</div>
