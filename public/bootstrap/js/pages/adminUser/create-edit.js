
   $(function(){

     var countryValue = $('#countrySelect').val();
     var stateValue   = $('#stateSelect').val();
     
     if(isEmpty(countryValue)){
         $('#stateSelect').attr("disabled",true);
     }

     if(isEmpty(stateValue)){
         $('#citySelect').attr("disabled",true);
     }

     // On Change The value of country select filed
     $('#countrySelect').change(function(base_url){
        
        $('#stateSelect').removeAttr("disabled");

        var id = this.value; // To get a Country Id

        if(!isEmpty(id)){
            ajaxRequest({      type : 'GET',
                                url : base_url + 'ajax/getStates',
                               data : { 'id' : id }
                       }); // To Call a ajax method with passign a argument
        }
         getAjaxResponse = (response) => { 
             let option = '';
             if(response.status == true){
                  option += '<option value="">select state</option>';  
                for(var i in response.data){
                  option += '<option value='+response.data[i].id+' >'+response.data[i].name+'</option>';
                }
             }else{
                  option += '<option>state not found</option>';  
             }
                $('#stateSelect').html(option);
          }

     });

     // On Change The value of state select filed
     $('#stateSelect').change(function(){

        $('#citySelect').removeAttr("disabled");

        var id = this.value; // To get a Country Id

        if(!isEmpty(id)){
            ajaxRequest({      type : 'GET',
                                url : base_url + 'ajax/getCities',
                               data : { 'id' : id }
                       }); // To Call a ajax method with passign a argument
        }
         getAjaxResponse = (response) => { 
             let option = '';
             if(response.status == true){
                  option += '<option value="">select city</option>';  
                for(var i in response.data){
                  option += '<option value='+response.data[i].id+' >'+response.data[i].name+'</option>';
                }
             }else{
                  option += '<option>city not found</option>';  
             }
                $('#citySelect').html(option);
          }
     });
});
           