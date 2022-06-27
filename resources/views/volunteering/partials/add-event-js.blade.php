$("[name='event_type']").change(function(){
$("#sub_type").attr("disabled",false);

if($(this).val()=="Fundraiser"){
$("#sub_type").html('<option value="Auction">Auction</option><option value="Entertainment">Entertainment</option><option value="Food">Food</option><option value="Other">Other</option><option value="Sports">Sports</option>');
}
else if($(this).val()=="Gaming"){
$("#sub_type").html('<option value="50/50 Draw">50/50 Draw</option>');
}
else{
$("#sub_type").html('<option value="false">Disabled</option>');
$("#sub_type").attr("disabled",true);;
}
$("select").select2();
});

$("body").on("change","[name='attachments[]']",function(){
$(this).parents("tr").find(".filename").html( $(this)[0].files[0].name);
});

$("select").select2();

let row_number = 0;
$("#add_row").click(function(e){
e.preventDefault();
text = $("#organization-tmpl").html();
text = text.replace(/XXX/g, row_number + 1);
$('.organization').last().after( text );
row_number++;
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

$("#bank_deposit_form").submit(function(e)
{
e.preventDefault();
var form = document.getElementById("create_pool");
var formData = new FormData();
$("select").each(function(){
if($(this).val().length > 0){
formData.append($(this).attr("name"), $(this).val());
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
formData.append('attachments[]',  $(this)[0].files[0]);
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
$(this).fadeTo("slow",0.2);
$.ajax({
url: "{{ route("bank_deposit_form") }}",
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
alert("Success!");
// window.location = response[0];
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
$("#attachment"+(parseInt(count)+1)).find("."+tag+"_errors").html('<span class="invalid-feedback">'+error+'</span>');
$("#organization"+count).find("."+tag+"_errors").html('<span class="invalid-feedback">'+error+'</span>');
$("." + prop + "_errors").html('<span class="invalid-feedback">'+error+'</span>');
}
}
$(".invalid-feedback").css("display","block");
$("#bank_deposit_form").fadeTo("slow",1);
},
});

});
