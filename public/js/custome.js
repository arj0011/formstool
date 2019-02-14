$(document).ready(function(){
    $('a , #submit').click(function(){
           $('.loader').addClass('show');
    });
});


 
 // To Allow only number key
 function isNumberKey(evt){
        var charCode = (evt.which) ? evt.which : evt.keyCode
        return !(charCode > 31 && (charCode < 48 || charCode > 57));
      }

// To Allow only alpha charecter
 function isAlphaKey(evt){
        var keyCode = (evt.which) ? evt.which : evt.keyCode
        if ((keyCode < 65 || keyCode > 90) && (keyCode < 97 || keyCode > 123) && keyCode != 32)
        return false;
        return true;
}

$('.alpha').on('keypress',function(evt){
   var keyCode = (evt.which) ? evt.which : evt.keyCode
        if ((keyCode < 65 || keyCode > 90) && (keyCode < 97 || keyCode > 123) && keyCode != 32)
        return false;
        return true;
});

$('.numeric').on('keypress',function(evt){
     var charCode = (evt.which) ? evt.which : evt.keyCode
        return !(charCode > 31 && (charCode < 48 || charCode > 57));
});