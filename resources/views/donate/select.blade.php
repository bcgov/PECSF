@extends('donate.layout.main')

@section ("step-content")
<h2 class="mt-5">1. Choose your charities</h2>
<div>
    <form action="{{route('donate.save.select')}}" method="post">
        @csrf
        <label class="w-100">
            <span class="text-muted">Search for your CRA Charity</span>
            <select class="form-control" placeholder="type in charity name">
            </select>
            <small class="text-danger">
                {{ $errors->first('id') }}
            </small>
        </label>
        <div id="selected-charity-list">

        </div>
        <div class="mt-2">
            <button class="btn btn-lg btn-outline-primary">Cancel</button>
            <button class="btn btn-lg btn-primary" type="submit">Next</button>
        </div>
    </form>
</div>
@endsection
@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.1.1/dist/select2-bootstrap-5-theme.min.css" />
<style>
    #selected-charity-list {
        min-height: 200px;
    }
</style>
@endpush
@push('js')
<script type="x-tmpl" id="charity-tmpl">
        <div class="m-2">
            <div class="charity bg-light p-2 m-1">
                <b>${this.text}</b>
                <button type="button" class="btn btn-light btn-sm float-end clear-charity" data-id="${this.id}">&times;</button>
            </div>
            <label class="w-100">
                <input type="hidden" name="id[]" value="${this.id}">
                <input type="text" name="additional[]" class="form-control form-control-sm additional-text" data-id="${this.id}" value="${this.additional}" placeholder="If you have specific community or initiative in mind, enter it here.">
            </label>
        </div>
    </script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    let selectedCharity = {!!json_encode($charities)!!};
    const $select = $("select");
    const tmplCharity = $("#charity-tmpl").html();

    const tmplParse = function (templateString, templateVars) {
        return new Function("return `" + templateString + "`;").call(templateVars);
    };
    renderSelectedCharity();
    $select.select2({
        theme: "bootstrap-5",
        minimumInputLength: 3,
        ajax: {
            delay: 250,
            url: 'donate/select',
            data: function (params) {
                const query = {
                    search: params.term,
                    page: params.page || 1
                }

                // Query parameters will be ?search=[term]&page=[page]
                return query;
            },
            processResults: function (response, params) {
                return {
                    results: response.result.data,
                    pagination: {
                        more: !!response.result.next_page_url
                    }
                };
            }
        }
    });

    $select.on('select2:select', function (e) {
        if (selectedCharity.findIndex((c) => c.id === parseInt($(this).val())) === -1) {
            selectedCharity.push({
                id: parseInt($(this).val()),
                text: $select.find("option:selected").text(),
                additional: ''
            });

            $select.empty().trigger('change');
            renderSelectedCharity();
        }
    });

    function renderSelectedCharity() {
        $list = $("#selected-charity-list");
        $list.html("");
        for (charity of selectedCharity.reverse()) {
            $list.append(tmplParse(tmplCharity, charity));
        }
    }

    $(document).on('click', '.clear-charity', function () {
        const idToRemove = $(this).data("id")
        selectedCharity = selectedCharity.filter((charity) => charity.id != idToRemove);
        $.post("/donate/remove",
            {
                charity_id: idToRemove
            },
            function (data, status) {
                console.log("Data: " + data + "\nStatus: " + status);
            });
        renderSelectedCharity();
    });

    $(document).on('change', '.additional-text', function () {
        const idToUpdate = $(this).data("id");
        const charity = selectedCharity.find((charity) => charity.id == idToUpdate);
        if (charity) {
            charity.additional = $(this).val();
        }
    });

</script>
@endpush