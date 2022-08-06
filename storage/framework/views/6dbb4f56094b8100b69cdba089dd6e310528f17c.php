

$("input[name='charity_selection']").click(function(){
if($(this).val() == "dc"){
$("#organizations").show();
$(".org_hook").show();
$("#add_row").show();
$(".form-pool").hide();
}
else{
$(".form-pool").show();
$("#organizations").hide();
$("#add_row").hide();
$(".org_hook").hide();
}
});

$("[name='organization_code']").change(function(){
if($(this).val() == "GOV"){
$("#pecsfid").hide();
$("#bcgovid").show();
}
else{
$("#pecsfid").show();
$("#bcgovid").hide();
}
});

$("[name='event_type']").change(function(){
$("#sub_type").attr("disabled",false);


if($(this).val()=="Fundraiser"){
$("#sub_type").html('<option value="">None</option><option value="Auction">Auction</option><option value="Entertainment">Entertainment</option><option value="Food">Food</option><option value="Other">Other</option><option value="Sports">Sports</option>');
$(".address_hook").hide();
//$("#sub_type").select2();
}
else if($(this).val()=="Gaming"){
$("#sub_type").html('<option value="">None</option><option value="50/50 Draw">50/50 Draw</option>');
$(".address_hook").hide();
//$("#sub_type").select2();
}
else{
$(".address_hook").show();

$("#sub_type").html('<option value="false">Disabled</option>');
$("#sub_type").attr("disabled",true);
//$("#sub_type").select2();
$(".sub_type .selection").children(0).children(0).remove();
}

});

$("body").on("change","[name='attachments[]']",function(){
$(this).parents("tr").find(".filename").html( $(this)[0].files[0].name);
});


let row_number = 0;




$("body").on("click",".select",function(e){
e.preventDefault();

text = $("#organization-tmpl").html();
text = text.replace(/XXX/g, row_number + 1);
$('#organizations').append( text );
$("#organizations").css("display","block");
row_number++;
$('.organization').last().find(".organization_name").val($(this).attr("name"));
$('.organization').last().append("<input type='hidden' name='vendor_id[]' value='"+$(this).attr('org_id')+"'/>");
});



let attachment_number = 1;
$("body").on("click",".add_attachment_row",function(e){
e.preventDefault();
text = $("#attachment-tmpl").html();
text = text.replace(/XXX/g, attachment_number + 1);
$('.attachment').last().after( text );
attachment_number++;
});

$("body").on("click",".remove",function(e){
e.preventDefault();
$(this).parents("tr").remove();
});
var formData = new FormData();
$("#bank_deposit_form").submit(function(e)
{
e.preventDefault();
var form = document.getElementById("create_pool");

formData = new FormData();

$("select").each(function(){
if($(this).val()){
if($(this).val().length > 0){
formData.append($(this).attr("name"), $(this).val());
}
}

});
$("input").each(function(){
if($(this).attr('type') != "submit"){
if($(this).attr('type') == "radio"){
if($(this).is(':checked')){
formData.append($(this).attr("name"), $(this).val());
}
}
else if($(this).attr('type') == "file"){
//formData.append('attachments[]',  $(this)[0].files[0]);
}
else if($(this).val().length > 0){
formData.append($(this).attr("name"), $(this).val());
}
}
});
$("textarea").each(function(){
if($(this).val().length > 0) {
formData.append($(this).attr("name"), $(this).val());
}
});
formData.append("org_count", $(".organization").length);
formData.append("ignoreFiles[]", ignoreFiles);

$(this).fadeTo("slow",0.2);
$.ajax({
url: "<?php echo e(route("bank_deposit_form")); ?>",
type:"POST",
data: formData,
headers: {'X-CSRF-TOKEN': $("input[name='_token']").val()},
processData: false,
cache: false,
contentType: false,
dataType: 'json',
success:function(response){
$("#bank_deposit_form").fadeTo("slow",1);
$('.errors').html("");

window.location = response[0];
console.log(response);
},
error: function(response) {
$('.errors').html("");

if(response.responseJSON.errors){
errors = response.responseJSON.errors;
for(const prop in response.responseJSON.errors){
count = prop.substring(prop.indexOf(".")+1);
tag = prop.substring(0,prop.indexOf("."));
error = errors[prop][0].split(".");
error = error[0] + error[1].substring(1,error[1].length);
error = error.replace("_"," ");
$("."+tag+"_errors").html('<span class="invalid-feedback">'+error+'</span>');
$("#organization"+count).find("."+tag+"_errors").html('<span class="invalid-feedback">'+error+'</span>');
$("." + prop + "_errors").html('<span class="invalid-feedback">'+error+'</span>');
}
}
$(".invalid-feedback").css("display","block");
$("#bank_deposit_form").fadeTo("slow",1);
},
});

});
/*
$('#organization_code').select2({
ajax: {
url: '/bank_deposit_form/organization_code',
dataType: 'json'
}
});
$('.organization_name').select2({
ajax: {
url: '/bank_deposit_form/organization_name',
dataType: 'json'
}
});
*/
$(".sub_type .selection").children(0).children(0).remove();
$('.more-info').click( function(event) {
event.stopPropagation();
// var current_id = event.target.id;
id = $(this).attr('data-id');
name = $(this).attr('data-name');

console.log( 'more info - ' + id );
if ( id  ) {
// Lanuch Modal page for listing the Pool detail
$.ajax({
url: '/donate/regional-pool-detail/' + id,
type: 'GET',
// data: $("#notify-form").serialize(),
dataType: 'html',
success: function (result) {
$('.modal-title span').html(name);
target = '.pledgeDetail';
$(target).html('');
$(target).html(result);
},
complete: function() {
},
error: function () {
alert("error");
$(target).html('<i class="glyphicon glyphicon-info-sign"></i> Something went wrong, Please try again...');
}
})

$('#regionalPoolModal').modal('show')
}
});

$("body").on("click",".view_attachment",function(){
window.open(URL.createObjectURL($(this).parents("tr").find("input")[0].files[0]));
});

function readFile(file) {
const reader = new FileReader();
reader.addEventListener('load', (event) => {
const result = event.target.result;
// Do something with result
});
reader.readAsDataURL(file);
}


$("html").on("drop", function(e) { e.preventDefault(); e.stopPropagation(); });

// Drag enter
$('.upload-area').on('dragenter', function (e) {
e.stopPropagation();
e.preventDefault();
$("#upload-area-text").text("Drop");
});

// Drag over
$('.upload-area').on('dragover', function (e) {
e.stopPropagation();
e.preventDefault();
$("#upload-area-text").html("<span style='margin-left:75px'>Drop</span>");
});

// Drag over
$('.upload-area').on('dragleave', function (e) {
e.stopPropagation();
e.preventDefault();
$("#upload-area-text").html("Drag and Drop Or <u>Browse</u> Files");
});


// Drop
$('.upload-area').on('drop', function (e) {
e.stopPropagation();
e.preventDefault();
$("#upload-area-text").html("Drag and Drop Or <u>Browse</u> Files");
var file = e.originalEvent.dataTransfer.files;
formData.append('attachments[]', file[0]);
$("#attachments").append("<span>"+file[0].name+"</span> <i attachment='"+file[0].name+"' class='remove_attachment fas fa-window-close'></i><br>");
const index = ignoreFiles.indexOf(file[0].name);
if (index > -1) { // only splice array when item is found
ignoreFiles.splice(index, 1); // 2nd parameter means remove one item only
}


});

var ignoreFiles = [];
$("body").on("click",".remove_attachment",function(){
$(this)[0].previousElementSibling.remove()
$(this).remove();
ignoreFiles.push($(this).attr("attachment"));
});
<?php /**PATH C:\Users\x257354\Sites\PECSF\resources\views/volunteering/partials/add-event-js.blade.php ENDPATH**/ ?>