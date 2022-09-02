jQuery( function($) {
    $( "#datefilter-date" ).datepicker({
        onSelect: function(dateText, inst) {
            var date = $(this).val();

            $.ajax({
                type: "get",
                url: df_filter.ajaxurl,
                data: {
                    action: "dffilter_post",
                    nonce: df_filter.nonce,
                    date: date
                },
                beforeSend: ()=>{
                    $(".df_loader").removeClass("dnone");
                },
                dataType: "json",
                success: function (response) {
                    $(".df_loader").addClass("dnone");
                    if(response.success){
                        if(response.success.length > 0){
                            $("#df_posts").html("");
                            response.success.forEach(post => {
                                $("#df_posts").append(`<li>${post}</li>`);
                            });
                        }else{
                            $("#df_posts").html(`<div class="df_error">No post found!</div>`);
                        }
                    }
                    if(response.error){
                        $("#df_posts").html(`<div class="df_error">No post found!</div>`);
                    }
                }
            });
        } 
    });
} );