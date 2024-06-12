


    $(document).ready(function() {
        $('#passworReset').on('click', function() {
            $('#resetPasswordModal').modal('show'); // Show the modal after setting values

        });
        $('#resetPasswordForm').submit(function (e) {
            e.preventDefault();
            let formData = $('#resetPasswordForm').serialize();
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
            });
            var url= baseUrl +'/'+ 'users/resetPassword';
            $.ajax({
                url: url,
                type: "POST",
                data: formData,
                beforeSend: function () {
                    // $.LoadingOverlay('show');
                },
                processData: false,
                success: function (response) {
                    // $.LoadingOverlay('hide');
                    console.log(response.status);
                    if (response.status == 422) {
                        displayErrorsPassword(response.errors);
                    } else if (response.status == 500) {
                        toastr.error(response.message);
                    } else if (response.status == 400) {
                        $.each(response.errors, function (key, val) {
                            toastr.error(val);
                        });
                    } else {
                        $("#resetPasswordModal").modal("hide");
                        location.reload();
                    }
                },

            });

        });
        function displayErrorsPassword(errors) {
            // Clear previous errors
            $('.error-message').text('');

            // Display errors below each input
            $.each(errors, function(field, message) {
                $('#' + field + 'Error').text(message[0]);
            });
        }

        $('#testBtn').on('click', function() {
            // alert(1);
            // AJAX GET request to fetch user data
            var id=$('#testBtn').attr('data-id');
            var url=baseUrl + '/teamEdit'+'/' + id
            $.ajax({
                url: url, // Replace with your actual URL
                type: 'GET',
                success: function(response) {
                    // Assuming the response contains data in JSON format
                    // Fill the form fields with received data
                    if (response.status) {
                        var data_value = response.data;
                        console.log(data_value);

                        var roles = response.roles;
                        var selectedRole = response.selected;
                        // console.log('role',selectedRole);
                        // Assuming your response structure matches the data fields needed in the form
                        $('#fullName').val(data_value.fullName);
                        $('#phoneNumber').val(data_value.phoneNumber);
                        $('#email').val(data_value.email);
                        // $('#gender').val(data.gender);

                        // $('#gender').val(data.gender).change();
                        $('#userId').val(data_value.id);
                        var roleDropdown = $('#role');
                        roleDropdown.empty(); // Clear existing options

                        // Loop through roles to populate dropdown
                        $.each(roles, function(key, value) {
                            var option = $('<option></option>').attr('value', key).text(value);
                            roleDropdown.append(option);
                        });

                        // Set the selected role
                        $('#role').val(selectedRole);
                        // Assuming selected roles are returned as 'selected' in response
                        $('#exampleModal').modal('show'); // Show the modal after setting values
                    } else {
                        console.error('Error fetching user data:', response.data);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching data:', error);
                }
            });
        });
        $('#saveBtn').on('click', function(event) {
            // Get form data
            event.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var formData = $('#userForm').serialize();
            var id=$('#userId').val();
            $('#loader').removeClass('d-none');
            // Send AJAX POST request
            $.ajax({
                url: '/teamUpdate/'+ id, // Replace with your save endpoint URL
                type: 'POST',
                data: formData, // Send form data
                success: function(response) {
                    // Handle success response
                    $('#exampleModal').modal('hide');
                    $('#loader').addClass('d-none');
                },
                error: function(xhr, status, error) {
                    // Handle errors
                    // console.error('Error submitting form:', error);
                    // Display errors if any
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        var errors = xhr.responseJSON.errors;
                        displayErrors(errors); // Function to display errors
                    }
                    $('#loader').addClass('d-none');
                }
            });
        });

        function displayErrors(errors) {
            console.log(errors.email);
            $('#fullNameError').text(errors.fullName ? errors.fullName[0] : '');
            $('#emailError').text(errors.email ? errors.email[0] : '');
            $('#phoneNumberError').text(errors.phoneNumber ? errors.phoneNumber[0] : '');
            // Add more lines for additional fields if needed
        }


    });
    $('#cancelBtn').on('click', function() {
        $('#exampleModal').modal('hide'); // Close the modal when cancel button is clicked
    });
    $('#cancelBtn2').on('click', function() {
        $('#resetPasswordModal').modal('hide'); // Close the modal when cancel button is clicked
    });





