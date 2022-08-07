<?php $__env->startSection('content_header'); ?>
    <div class="d-flex mt-3">
        <h1>Forms</h1>
        <div class="flex-fill"></div>
    </div>
<?php $__env->stopSection(); ?>



<?php $__env->startSection('content'); ?>

    <?php echo $__env->make('volunteering.partials.form_tabs', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>




            <?php echo $__env->make('volunteering.partials.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


    <?php $__env->startPush('css'); ?>
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


        <style>
            .select2 {
                width:100% !important;
            }
            .select2-selection--multiple{
                overflow: hidden !important;
                height: auto !important;
                min-height: 38px !important;
            }

            .select2-container .select2-selection--single {
                height: 38px !important;
            }
            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 38px !important;
            }

            table tr{
                background:#fff;
            }

        </style>

    <?php $__env->stopPush(); ?>


    <?php $__env->startPush('js'); ?>
       <!-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>-->

        <script type="x-tmpl" id="organization-tmpl">
            <?php echo $__env->make('volunteering.partials.add-organization', ['index' => 'XXX'] , \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </script>



        <script>

            <?php echo $__env->make('volunteering.partials.add-event-js', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        </script>
        <script>
            $(document).ready(function(){
                var keywordTypingTimer;

                $(document).on('click', '.pagination a', function(event){
                    event.preventDefault();
                    var page = $(this).attr('href').split('page=')[1];
                    fetch_data(page);
                });

                $("#keyword").keyup(function(){
                    clearTimeout(keywordTypingTimer);
                    keywordTypingTimer = setTimeout(fetch_data,800)
                });

                $("#category").change(function(){
                    fetch_data(1);
                });

                $("#charity_province").change(function(){
                    fetch_data(1);
                });

                function fetch_data(page=1)
                {
                    $("#charities").fadeTo("slow",0.2);
                    $.ajax({
                        url:"/bank_deposit_form/organizations?page="+page+"&category="+$("#category").val()+"&province="+$("#charity_province").val()+"&keyword="+$("#keyword").val(),
                        success:function(data)
                        {
                            $("#charities").fadeTo("slow",1);
                            $('#charities').html(data);
                        }
                    });
                }

            });
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\x257354\Sites\PECSF\resources\views/volunteering/forms.blade.php ENDPATH**/ ?>