@extends('donate.layout.main')

@section ("step-content")
<h2 class="mt-5">3. Decide on the distribution</h2>
<p class="mt-3">You can distribute your contributions to each charity here. Start from the top and specify the amount of percentage so that together they are total 100%.</p>
<form action="{{route('donate.save.distribution')}}" method="POST">

<div class="d-flex align-items-center justify-content-between mb-3">
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="distributeByDollarAmount" name="distributionByAmount" value="true">
        <label class="form-check-label" for="distributeByDollarAmount">Distribute by dollar amount</label>
    </div>
    <button class="btn btn-link">Distribute evenly</button>
</div>
    @csrf
    <table class="table table-sm">
        @foreach ($charities as $charity)
        <tr>
            <td class="p-2">{{ $charity['text'] }}</td>
            <td style="width:110px" class="by-percent">
                <div class="input-group input-group-sm mb-3">
                    <input type="number" class="form-control form-control-sm percent-input" name="percent[{{ $charity['id'] }}]" placeholder="" value="{{$charity['percentage-distribution']}}">
                    <span class="input-group-text">%</span>
                </div>
            </td>
            <td style="width:110px" class="by-amount d-none">
                <div class="input-group input-group-sm mb-3">
                    <input type="number" class="form-control form-control-sm amount-input" name="amount[{{ $charity['id'] }}]" placeholder="" value="{{$charity['amount-distribution']}}">
                </div>
            </td>
            {{-- <td>
                <div class="d-flex flex-row">
                    <button class="btn border btn-sm btn-light me-1">-</button>
                    <button class="btn border btn-sm btn-light ms-1">+</button>
                </div>
            </td> --}}
        </tr>
        @endforeach
        <tr>
            <td></td>
            <td class="by-percent">
                <div class="input-group input-group-sm mb-3">
                    <input type="text" class="form-control form-control-sm total-percent" placeholder="" disabled>
                    <span class="input-group-text">%</span>
                </div>
            </td>
            <td class="by-amount d-none">
                <div class="input-group input-group-sm mb-3">
                    <input type="number" class="form-control form-control-sm total-amount" placeholder="" disabled>
                </div>
            </td>
            <td></td>
        </tr>
    </table>
</form>
<div class="mt-5">
    <a class="btn btn-lg btn-outline-primary" href="{{route('donate.amount')}}">Previous</a>
    <button class="btn btn-lg btn-primary" type="submit">Next</button>
</div>
@endsection

@push('js')
    <script>
        $(document).on('change', '#distributeByDollarAmount', function () {
            if (!$(this).prop("checked")) {
                $(".by-percent").removeClass("d-none");
                $(".by-amount").addClass("d-none");
            } else {
                $(".by-amount").removeClass("d-none");
                $(".by-percent").addClass("d-none");
            }
        });
        $(document).on('change', '.percent-input', function () {
            let total = 0;
            $(".percent-input").each( function () {
                total += Number($(this).val());
            });
            $(".total-percent").val(total);
        });


        $(document).on('change', '.amount-input', function () {
            let total = 0;
            $(".amount-input").each( function () {
                total += Number($(this).val());
            });
            $(".total-amount").val(total);
        });

        $(".percent-input").change();
        $(".amount-input").change();
    </script>
@endpush