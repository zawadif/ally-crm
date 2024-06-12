$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});


function resetForm(id) {
    $('.select2').val('').trigger('change');
    $('.dropify-clear').trigger('click')
    document.getElementById(id).reset();
}



$(document).ready(function() {
    // Call getData() function when the document is ready
    // getData();
});



// Add event listener to the search input





/* add  */
$("#addForm").on('submit', (function(e) {
    e.preventDefault()

    // $.LoadingOverlay("show");

    $.ajax({
        type: 'POST',
        url: baseUrl + '/teams',
        data: new FormData(this),
        dataType: 'JSON',
        contentType: false,
        cache: false,
        processData: false,
        success: function(data) {
            resetForm('addForm')
            // $.LoadingOverlay("hide");
            if (data.status == true) {
                toastr.success(data.data)
                $(this).trigger("reset");
                location.reload();
                // getData('teamsTable')
            } else {
                // $.LoadingOverlay("hide");
                toastr.error(data.data)
            }

        },
        error: function(xhr, status, error) {
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                var errors = xhr.responseJSON.errors;
                displayErrors(errors); // Function to display errors
            }



        }

    });

    function displayErrors(errors) {
        $('#fullNameError').text(errors.fullName ? errors.fullName[0] : '');
        $('#emailError').text(errors.email ? errors.email[0] : '');
        $('#phoneNumberError').text(errors.phoneNumber ? errors.phoneNumber[0] : '');
        // Add more lines for additional fields if needed
    }


}));



/* edit  */
    /* change password  */
$('#profileLoader ').on('click', '#reset-password', function() {
    $('#passwordEdit').modal('dispose');
    var teamId = $(this).attr('data-id')
    $('#passwordEdit #teamId').val(teamId)
    $('#updatepasswordteamForm').attr('action', baseUrl + '/teams/' + teamId + '/password')
    $('#passwordEdit #deleteMember').attr('href', baseUrl + '/teams/' + teamId + '/delete')
    $.ajax({
        type: 'get',
        dataType: 'json',
        url: baseUrl + '/teams/' + teamId + '/edit',
        success: function(data) {
            var member = data.data
            var title = 'Edit ' + ucfirst(member.name) + '`s Password';
            $('#passwordEdit  #exampleModalLabel').html(title)
            $('#passwordEdit').modal('show');
        },
        error: function(data) {
            toastr.error('Something went wrong!')
        }

    });

})













function ucfirst(str, force) {
    str = force ? str.toLowerCase() : str;
    return str.replace(/(\b)([a-zA-Z])/,
        function(firstLetter) {
            return firstLetter.toUpperCase();
        });
}



$('#clearAll').on('click', function() {
    resetForm('addForm')


})

function resetDatatable(tableclass) {
    var table = $(tableclass).DataTable()
    table.destroy()
}
