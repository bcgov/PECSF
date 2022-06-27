<div class="button-group">
    <div class="{{ str_contains(Route::current()->getName(), 'admin-pledge.maintain-event') ? 'active' : '' }}">
        <a class="activewhite" href="{{ route('admin-pledge.maintain-event.index') }}">Find an Existing Value</a>
    </div>
    <div class="add-event-modal">Add a New Value</div>
    <div class="{{ str_contains(Route::current()->getName(), 'admin-pledge.submission-queue') ? 'active' : '' }}">
        <a
           id="pills-home-tab" class="activewhite" href="{{ route('admin-pledge.submission-queue.index') }}">PECSF Event Submission Queue</a>
    </div>
</div>
