<?php
    function ordinal($number) {
        $ends = array('th','st','nd','rd','th','th','th','th','th','th');
        if ((($number % 100) >= 11) && (($number%100) <= 13))
            return $number. 'th';
        else
            return $number. $ends[$number % 10];
    }
?>

<?php $__env->startSection('content_header'); ?>

    <ul class="nav nav-pills mb-3" id="pills-tab" >
        <li class="nav-item">
            <a class="nav-link <?php echo e(str_contains( Route::current()->getName(), 'challege.leaderboard') ? 'active' : ''); ?>"

               href="<?php echo e(route('challege.leaderboard')); ?>" role="tab" aria-controls="pills-home" aria-selected="true">Leaderboard</a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo e(str_contains( Route::current()->getName(), 'challege.daily_campaign') ? 'active' : ''); ?>"

               href="<?php echo e(route('challege.daily_campaign')); ?>"  aria-controls="pills-profile" aria-selected="false">Daily Campaign Update</a>
        </li>


    </ul>

<div class="mt-3">
<h1>Challenge</h1>
<p class="h5 mt-3">Visit this page daily during the PECSF campaign to see updated statistics, including organization participation rates!<br>

    View and download daily statistics updates below.<br>

    If you have questions about PECSF statistics, send us an e-mail at <a href="mailto:PECSF@gov.bc.ca?subject=Challenge%20page">PECSF@gov.bc.ca</a>.</p>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-end">
<label style="min-width: 130px">
Campaign Year
<select name="year" class="form-control form-control-sm">
<option <?php echo e($year==2021?"selected":""); ?> value="2021">2021</option>
<option <?php echo e($year==2020?"selected":""); ?> value="2020">2020</option>
</select>
</label>
</div>
<div class="card">
<div class="card-body">
<table class="table table-bordered rounded" id="myTable2">
<tr class="bg-light">
<th onclick="sortTable('rank')" style="cursor: pointer;">Rank</th>
<th onclick="sortTable('name')" style="cursor: pointer;">Organization name <img style="width:16px;height:16px;" class="sort-hook float-right" src="<?php echo  ($request->sort == "ASC") ? asset("img/icons/FilterDescending.png"):asset("img/icons/FilterAscending.png") ?>" /></th>
<th onclick="sortTable('participation_rate')" style="cursor: pointer;">Participation rate <img style="width:16px;height:16px;" class="sort-hook float-right" src="<?php echo  ($request->sort == "ASC") ? asset("img/icons/FilterDescending.png"):asset("img/icons/FilterAscending.png") ?>" /></th>
<th onclick="sortTable('participation_rate')" style="cursor: pointer;">Previous participation rate </th>
    <th style="cursor: pointer;">Change</th>
<th onclick="sortTable('donors')" style="cursor: pointer;">Donors <img style="width:16px;height:16px;" class="sort-hook float-right" src="<?php echo  ($request->sort == "ASC") ? asset("img/icons/FilterDescending.png"): asset("img/icons/FilterAscending.png") ?>" /></th>
<th onclick="sortTable('dollars')" style="cursor: pointer;">Dollars Donated <img style="width:16px;height:16px;" class="sort-hook float-right" src="<?php echo  ($request->sort == "ASC") ? asset("img/icons/FilterDescending.png"): asset("img/icons/FilterAscending.png") ?>" /></th>
</tr>


<?php $__currentLoopData = $charities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $charity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<!--  <tr>
<td><?php echo e($count == 0 ? 'st' : ($count == 1 ? 'nd' : ($count == 2 ? 'rd' : 'th'))); ?></td>
<td><?php echo e($charity['name']); ?></td>
<td><?php echo e(round($charity['participation_rate'])); ?>%</td>
<td><?php echo e(round($charity['final_participation_rate'])); ?>%</td>
<td>
<?php if($charity['change'] < 0): ?>
    <span style="color:red">
<?php else: ?>
    <span style="color:green">
<?php endif; ?>
<?php echo e($charity['change']); ?>%
</td>
<td><?php echo e($charity['total_donors']); ?></td>
<td>$<?php echo e(number_format($charity['total_donation'])); ?></td>
</tr> -->
<tr>
<td><?php echo e(ordinal($count)); ?></td>

<?php



                        if($request->sort == "ASC"){
                            $count--;
                        }
                        else{
                            $count++;
                        }

                    ?>

                    <td><?php echo e($charity['name']); ?></td>
                    <td><?php echo e(round(($charity['participation_rate'] * 100))); ?>%</td>
                    <td><?php echo e(round($charity['previous_participation_rate'])); ?>%</td>
                    <td><?php echo e(round($charity['change'])); ?>%</td>
                    <td><?php echo e($charity['donors']); ?></td>
                    <td>$<?php echo e(number_format($charity['dollars'])); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </table>
<br>
        <div>
            <?php echo e($charities->links()); ?>

        </div>


    </div>
</div>

<br>
<br>




<?php $__env->stopSection(); ?>
<?php $__env->startPush('js'); ?>
<script>
    var year = '<?php echo e($request->year ? $request->year : "2021"); ?>';

    $("#sort,#start_date").change(function(){
        $.ajax({
            method: "GET",
            url:  '/challenge/preview?sort=' + $("#sort").val() + '&start_date=' + new Date($("#start_date").val()).getFullYear(),
            success: function(data)
            {
                $("#preview").html(data);
            },
            error: function(data) {
                $("#preview").html(data);
            }
        });
    });

    $("select[name='year']").change(function(){
        window.location = "/challenge?year="+$(this).val();
    });

    var new_sort = '<?php echo e($request->sort == "ASC" ? "DESC" : "ASC"); ?>';
function sortTable(field='participation_rate') {
    window.location = "/challenge?field="+field+"&sort="+new_sort+"&year="+year;
    $(".sort-hook").attr("src",);
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\x257354\Sites\PECSF\resources\views/challenge/index.blade.php ENDPATH**/ ?>