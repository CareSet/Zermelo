$(document).ready( function() {

    var t = $('input[name="_token"]').attr('value');

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': t
        }
    });

    $('.do-soft-delete').click( function() {
        var controller = $(this).attr( 'data-controller' );
        var id = $(this).attr( 'data-id' );
        if ( confirm( "Are you sure you want to DELETE this record?" ) == true ) {
            // Send DELETE action to the recource route
            $.ajax({
                url : '/DURC/' + controller + '/' + id,
                type : 'DELETE',
                success : function ( response ) {
                    // Freeze all buttons and provide option to undo
                    $(":input").prop("disabled", true);
                    $(".do-recover").prop("disabled", false);
                    $("#delete-success-alert").show();
                }
            });
        }
    });
});
