$(document).ready(function () {
       if ($("#privacyPolicyForm").length > 0) {
        $("#privacyPolicyForm").validate({
            rules: {
                title: "required",
            },
        });
       }
       if ($("#termConditionForm").length > 0) {
        $("#termConditionForm").validate({
            rules: {
                title: "required",
            },
        });
       }
        $('.summernote').summernote({
                 placeholder: 'Write the description',
                 tabsize: 2,
                 height: 150
        });

      $("#privacyPolicyForm").submit(function (e) {
        e.preventDefault();
        var check =
            $("#privacyPolicyForm").valid();
        if (check == true) {
          $("label.error").text('');
            var ch = $(document).find(".error").removeClass('.error');

            let formData = $('#privacyPolicyForm').serialize();

            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
            });

            $.ajax({
                url: "privacy-policy/create",
                type: "POST",
                data: formData,
                beforeSend: function () {
                    $.LoadingOverlay('show');
                },
                processData: false,
                success: function (response) {
                    $.LoadingOverlay('hide');
                    if (response.status == 400) {
                        $.each(response.errors, function (key, val) {
                            toastr.error(val);
                        });
                    } else if (response.status == 500) {
                        toastr.error(response.message);
                    } else {
                        location.reload();
                    }
                },
                });
                }
      });


      $("#termConditionForm").submit(function (e) {
        e.preventDefault();
        var check =
            $("#termConditionForm").valid();
        if (check == true) {
          $("label.error").text('');
            var ch = $(document).find(".error").removeClass('.error');

            let formData = $('#termConditionForm').serialize();

            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
            });

            $.ajax({
                url: "term-conditions/create",
                type: "POST",
                data: formData,
                beforeSend: function () {
                    $.LoadingOverlay('show');
                },
                processData: false,
                success: function (response) {
                    $.LoadingOverlay('hide');
                    if (response.status == 400) {
                        $.each(response.errors, function (key, val) {
                            toastr.error(val);
                        });
                    } else if (response.status == 500) {
                        toastr.error(response.message);
                    } else {
                        location.reload();
                    }
                },
                });
                }
      });
});

