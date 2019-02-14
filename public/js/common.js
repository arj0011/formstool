function readURL(input){
    if (input.files && input.files[0]){
        var reader = new FileReader();

        reader.onload = function (e){
            var fileType=input.files[0].type;
            if(fileType=="image/jpeg" ||fileType=="image/png" || fileType=="image/jpg")
            {
            $('#preview_res_image').show().attr('src', e.target.result);
            }else{
                $('#preview_res_image').hide();
                alert('Please select image type');
            }
        }

        reader.readAsDataURL(input.files[0]);
    }
}
$(document).ready(function(){
$("#res_image").change(function(){
   readURL(this);
});
});