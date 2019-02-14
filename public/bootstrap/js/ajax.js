
      setAjaxResponse = (response) => {
          getAjaxResponse(response) ;
        }

       ajaxRequest =  (request ) => {
                 $.ajax({
                  type: request.type,
                  url: request.url,
                  data: {
                    data: request.data,
                    '_token': '{{ csrf_token() }}',
                  },
                                   // your ajax code
                  beforeSend: function(){
                       $('.loader').show()
                   },
                  complete: function(){
                       $('.loader').hide();
                  },
                  success : function(response){
                      setAjaxResponse(response);
                  },
                });
              } 
              
      // To Check Given Value Is Emplty Or Not
       isEmpty = (val) => {
           return (val === undefined || val == null || val.length <= 0) ? true : false;
      }
