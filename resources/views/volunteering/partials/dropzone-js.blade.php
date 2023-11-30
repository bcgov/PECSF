<link href="{{ asset('vendor/dropzone/dropzone.min.css') }}" rel="stylesheet">
<script src="{{ asset('vendor/dropzone/dropzone.min.js') }}" ></script>

<style>

    .dropzone {
        border-radius: 10px;
        border: 2px dashed #0087f7 !important;
        margin-left: auto;
        margin-right: auto;
        background-color: #ffffff;
    }

    .dropzone .dz-preview.dz-file-preview .dz-details {
       opacity: 0;
    }

    .dropzone .dz-preview.dz-file-preview .dz-details:hover {
       opacity: 1;
    }

    /* .dropzone .dz-preview .dz-details .dz-filename {
       white-space: wrap !important;
       font-weight: bold;
    } */

    .dz-success-mark {
	    background-color: rgb(102, 187, 106, 0.8) !important;
    }

    .dz-success-mark svg {
        font-size: 54px;
        fill: #fff !important;
    }

    .dz-error-mark {
        background-color: rgba(239, 83, 80, 0.8) !important;
        top: 28% !important;
    }

    .dz-error-mark svg {
        font-size: 54px;
        fill: #fff !important;
    }

    #mytmp .dz-remove {
        z-index: 999;
        position: absolute;
        display: block;
        top: 0%;
        left: 0%;
        margin-left: -16px;
        margin-top: -16px;
    }

    #mytmp .dz-remove svg {
        fill: #444;
        cursor: pointer;
    }

    #mytmp .filename {
        visibility: visible;
        white-space: wrap;
        font-size: 13px;

    }


</style>     

<script type="text/javascript">

    var uploadedAttachmentMap = {};

    Dropzone.options.attachmentDropzone = {
        url: '{{ route("bank_deposit_form.storeMedia") }}',
        maxFilesize: 2, // MB
        maxFiles: 5,        
        // autoQueue: false,
        dictDefaultMessage: "<strong>Drop files here or click to upload. </strong>",
        previewTemplate: document.querySelector("#my-template").innerHTML,
        acceptedFiles:  ".xls,.xlsx,.csv,.pdf,.jpg,.jpeg,.png,.gif",
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        init: function () {
            this.on("success", function (file, response) {
                $('#bank_deposit_form').append('<input type="hidden" name="attachments[]" value="' + response.name + '">')
                uploadedAttachmentMap[file.name] = response.name;

                var e = $("<span class='filename'>" + response.original_name + "</span>");
                $(file.previewTemplate).append(e); // put it into the DOM     

                // show difference icon
                var ext = file.name.split('.').pop();
                if (ext == "pdf") {
                    $(file.previewElement).find(".dz-image img").attr("src", "{{ asset('img/pdf-icon.png') }}");
                } else if (ext.indexOf("xls") != -1) {
                    $(file.previewElement).find(".dz-image img").attr("src", "{{ asset('img/excel-icon.png') }}");
                } else if (ext.indexOf("xlsx") != -1) {
                    $(file.previewElement).find(".dz-image img").attr("src", "{{ asset('img/excel-icon.png') }}");
                } else if (ext.indexOf("csv") != -1) {
                    $(file.previewElement).find(".dz-image img").attr("src", "{{ asset('img/csv-icon.png') }}");
                }
            });
            this.on("removedfile", function (file) {
                // Called whenever a file is removed.
                file.previewElement.remove();
                var name = '';
                if (typeof file.file_name !== 'undefined') {
                    name = file.file_name;
                } else {
                    name = uploadedAttachmentMap[file.name];
                }
                $('#bank_deposit_form').find('input[name="attachments[]"][value="' + name + '"]').remove();
            });  
            
            this.on("addedfiles", function(files) {
            // Called when a file is added to the queue
            // Receives `file`
            });
            this.on("complete", function(file) {
            // When the upload is finished, either with success or an error.
            // Receives `file`
            });
            this.on("sendingmultiple", function() {
            // Gets triggered when the form is actually being sent.
            // Hide the success button or the complete form.
            });
            this.on("successmultiple", function(files, response) {
            // Gets triggered when the files have successfully been sent.
            // Redirect user or notify of success.
            });
            this.on("errormultiple", function(files, response) {
            // Gets triggered when there was an error sending the files.
            // Maybe show form again, and notify user of error
            });
        },
    
    }

</script>