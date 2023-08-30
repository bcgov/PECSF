<div class="modal fade" id="announcementModal" tabindex="-1" role="dialog" aria-labelledby="announcementModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
        <div class="modal-header bg-primary">
            <h5 class="modal-title" id="announcementModalTitle">
                {{ isset($announcement) ? $announcement->title : '' }}
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <textarea class="announcement_preview">
                    {{ isset($announcement) ? $announcement->body : '' }}
            </textarea>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Close</button>
        </div>
        </div>
    </div>
</div>