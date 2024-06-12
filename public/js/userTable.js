$(document).ready(function(){

    if ($("#profileForm").length > 0) {
        $("#profileForm").validate({
            rules: {
                firstName: "required",
                email: "required",
                address: "required",
                gender: {
                    required: true,
                },
                userContact: {
                    required: true,
                },
                date: {
                    required: true,
                },
                city: {
                    required: true,
                },
                state: {
                    required: true,
                },
                country: {
                    required: true,
                },
                postalCode: {
                    required: true,
                },
                emergencyFirstName: {
                    required: true,
                },
                relation: {
                    required: true,
                },
                emergencyContact: {
                    required: true,
                },

            },


        });
    }

    if ($("#editMatchScoreForm").length > 0) {
        $("#editMatchScoreForm").validate({
            rules: {
                set1Player1Score: {
                    required: true,
                    digits:true
                } ,
                set1Player2Score: {
                    required: true,
                    digits:true
                },
                set2Player1Score: {
                    digits:true
                } ,
                set2Player2Score: {
                    digits:true
                },
                set3Player1Score: {
                    digits:true
                } ,
                set3Player2Score: {
                    digits:true
                } ,

            },
        });
    }
        $.ajax({
            url: "/seasons/filter/get",
        type: "GET",
        async:false,
        beforeSend: function () {
                $.LoadingOverlay('show');
            },
     success: function (response) {

                $.LoadingOverlay('hide');
         if (response.status == 200) {
             if (response.filter.regionId) {
                 $('#regionId').find('option[value="' + response.filter.regionId + '"]').attr("selected", true);
                 region = response.filter.regionId;
             }
             if (response.filter.seasonId) {
                 $('#seasonId').find('option[value="' + response.filter.seasonId + '"]').attr("selected", true);
                 $('#rankingSeasonTag').find('option[value="' + response.filter.seasonId + '"]').attr("selected", true);
                 season = response.filter.seasonId;
             }
             if (response.filter.ladderId) {
                 $('#ladderId').find('option[value="' + response.filter.ladderId + '"]').attr("selected", true);
                 $('#rankingLadderTag').find('option[value="' + response.filter.ladderId + '"]').attr("selected", true);
                 ladder = response.filter.ladderId;
             }
             if (response.filter.weekId) {
                 $('#weekId').find('option[value="' + response.filter.weekId + '"]').attr("selected", true);
                 week = response.filter.weekId;
             }
             if (response.filter.countryId) {
                 $('#countryId').find('option[value="' + response.filter.countryId + '"]').attr("selected", true);
                 country = response.filter.countryId;
             }

            }

            },
    });
     $('#element').on('click', function( e ) {
         e.preventDefault();
         $('#availableCreditValue').html($('#available').html());
         $('#availableCreditModal').modal('show');
     });
    // partner
      $('#mixedDoublePartner').on('click', function( e ) {
          e.preventDefault();

        if ($.fn.DataTable.isDataTable('#partner-table')) {
            $('#partner-table').DataTable().destroy();
        }
        $('#partner-table tbody').empty();
          let userid = $('#userId').html();
          var table = $('#partner-table').DataTable({
            dom: 'frtp',
            processing: true,
            serverSide: true,
            ajax: '/users/partner/'+userid+'/'+3,
            columns: [
            {
                data: 'id',
                name: 'id'
            },
            {
                data: 'ladder',
                name: 'ladder'
            },
            {
                data: 'partner',
                name: 'partner'
            },{
                data: 'action',
                name: 'action'
            }
            ]
          });
          $('#partnerModal').modal('show');
      });
     $('#doublePartner').on('click', function( e ) {
          e.preventDefault();

        if ($.fn.DataTable.isDataTable('#partner-table')) {
            $('#partner-table').DataTable().destroy();
        }
        $('#partner-table tbody').empty();
          let userid = $('#userId').html();
          var table = $('#partner-table').DataTable({
            dom: 'frtp',
            processing: true,
            serverSide: true,
            ajax: '/users/partner/'+userid+'/'+2,
            columns: [
            {
                data: 'id',
                name: 'id'
            },
            {
                data: 'ladder',
                name: 'ladder'
            },
            {
                data: 'partner',
                name: 'partner'
            },{
                data: 'action',
                name: 'action'
            }
            ]
          });
          $('#partnerModal').modal('show');
     });
     $('#reset').on('click', function( e ) {
         e.preventDefault();

         $('#resetPasswordModal').appendTo("body").modal('show');
     });
     $("#subtractCredit, #subtractCredit").on("keydown keyup", function(event) {
         var input =document.getElementById('subtractCredit').value;
        var available = $('#availableCreditValue').html();
         if (parseFloat(available) < parseFloat(input)) {
            toastr.error('you do not have enough credit');
         } else {
             var remaining = parseFloat(available) - parseFloat(input);
             $('#remaining').val(parseFloat(remaining));
         }
         $('#remainingCreditValue').html(remaining);
     });
     $('#editCreditForm').submit(function (e) {
          e.preventDefault();
         let formData = $('#editCreditForm').serialize();

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            url: "/users/update-credit",
            type: "POST",
            data: formData,
            beforeSend: function () {
                $.LoadingOverlay('show');
            },
            processData: false,
            success: function (response) {

                $.LoadingOverlay('hide');
                if(response.status == 422){
                    toastr.error(response.message);
                }else if(response.status == 500){
                    toastr.error(response.message);
                }else{
                    $("#availableCreditModal").modal("hide");
                    location.reload();
                }
            },
        });
     });

     $('#profileForm').submit(function (e) {
         e.preventDefault();
          var check =
            $("#profileForm").valid();
         if (check == true) {
             $("label.error").text('');
             var ch = $(document).find(".error").removeClass('.error');
             let formData = new FormData($('#profileForm')[0]);
             $.ajaxSetup({
                 headers: {
                     "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                 },
             });

             $.ajax({
                 url: "/users/profile/update",
                 type: "POST",
                 data: formData,
                 contentType: false,
                 processData: false,
                 beforeSend: function () {
                     $.LoadingOverlay('show');
                 },
                 processData: false,
                 success: function (response) {

                     $.LoadingOverlay('hide');
                     if (response.status == 422) {
                         toastr.error(response.message);
                     } else if (response.status == 500) {
                         toastr.error(response.message);
                     }else if (response.status == 400) {
                         $.each(response.errors, function (key, val) {
                             toastr.error(val);
                         });
                     } else {
                         $("#editProfileModal").modal("hide");
                         location.reload();
                     }
                 },
             });
         }
     });


     $('#editPartnerForm').submit(function (e) {
         e.preventDefault();

         let formData = $('#editPartnerForm').serialize();

             $.ajaxSetup({
                 headers: {
                     "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                 },
             });

             $.ajax({
                 url: "/users/update/partner",
                 type: "POST",
                 data: formData,
                 beforeSend: function () {
                     $.LoadingOverlay('show');
                 },
                 processData: false,
                 success: function (response) {

                     $.LoadingOverlay('hide');
                     if (response.status == 422) {
                         toastr.error(response.message);
                     } else if (response.status == 500) {
                         toastr.error(response.message);
                     }else if (response.status == 400) {
                         $.each(response.errors, function (key, val) {
                             toastr.error(val);
                         });
                     } else {
                         $("#editPartnerModal").modal("hide");
                         location.reload();
                     }
                 },
             });

     });
    $('#resetPasswordForm').submit(function (e) {
         e.preventDefault();
         let formData = $('#resetPasswordForm').serialize();
             $.ajaxSetup({
                 headers: {
                     "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                 },
             });
            $.ajax({
                url: "/users/resetPassword",
                type: "POST",
                data: formData,
                beforeSend: function () {
                    $.LoadingOverlay('show');
                },
                processData: false,
                success: function (response) {
                    $.LoadingOverlay('hide');
                    if (response.status == 422) {
                        toastr.error(response.message);
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

     $('.filter').on('click', function( e ) {
         e.preventDefault();
           $('#filterModal').modal('show');
     });
      $('#acceptScore').on('click', function( e ) {
          e.preventDefault();
          let matchId = $('#editScoreMatchId').val();
          let userId = $('#updateBy').val();
            var formData = {
        'matchId': matchId,
        'userId': userId,
            }

    $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
            });
            $.ajax({
                url: "/users/match/accept-score",
                type: "POST",
                data: { 'data': formData },
                 beforeSend: function () {
                $.LoadingOverlay('show');
            },
                success: function (response) {

                $.LoadingOverlay('hide');
                if(response.status == 422){
                    toastr.error(response.message);
                }else if(response.status == 500){
                    toastr.error(response.message);
                }else{
                    $("#editMatchScoreModal").modal("hide");
                    location.reload();
                }
                },
                });
      });
    $('.currentllyParticipating').on('click', function( e ) {
          e.preventDefault();
        let addMoreRoomClone = $(this).find('input').val();
        let amount = $(this).find('.purchaseAmount').val();
        $('#purchaseId').val(addMoreRoomClone);
        $('#amountPaid').html(amount);
        $('#cancelSubscriptionModal').appendTo("body").modal('show');
     });

//upload score
    $("#editMatchScoreForm").submit(function (e) {
        e.preventDefault();
        var check =
            $("#editMatchScoreForm").valid();
        if (check == true) {
            $("label.error").text('');
            var ch = $(document).find(".error").removeClass('.error');
            let formData = $('#editMatchScoreForm').serialize();
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
            });

            $.ajax({
                url: "matches/upload-score",
                type: "POST",
                data: formData,
                beforeSend: function () {
                    $.LoadingOverlay('show');
                },
                processData: false,
                success: function (response) {
                    $.LoadingOverlay('hide');

                    if (response.status == 422) {
                          toastr.error(response.message);
                    }  else if (response.status == 400) {
                         $.each(response.errors, function (key, val) {
                        toastr.error(val);
                    });
                    }
                    else if (response.status == 500) {
                        toastr.error(response.message);
                    }
                    else {
                        $("#editMatchScoreModal").modal("hide");
                        location.reload();
                    }
                },

            });
        }
    }
    );
    $("#cancelSubscriptionForm").submit(function (e) {
        e.preventDefault();
            let formData = $('#cancelSubscriptionForm').serialize();
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
            });

            $.ajax({
                url: "/users/cancel-subscription",
                type: "POST",
                data: formData,
                beforeSend: function () {
                    $.LoadingOverlay('show');
                },
                processData: false,
                success: function (response) {
                    $.LoadingOverlay('hide');

                    if (response.status == 422) {
                          toastr.error(response.message);
                    }
                    else if (response.status == 500) {
                        toastr.error(response.message);
                    }
                    else {
                        $("#cancelSubscriptionModal").modal("hide");
                        location.reload();
                    }
                },

            });
        }

    );
        $('#deleteUserForm').submit(function (e) {
          e.preventDefault();
        let formData = $('#deleteUserForm').serialize();
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            url: "/users/delete",
            type: "DELETE",
            data: formData,
            beforeSend: function () {
                $.LoadingOverlay('show');
            },
            processData: false,
            success: function (response) {
                $.LoadingOverlay('hide');
                if(response.status == 422){
                    toastr.error(response.message);
                }else if(response.status == 500){
                    toastr.error(response.message);
                }else{
                    $("#deleteUserModal").modal("hide");
                    window.location.href = '/users/active';
                }
            },
        });
        });
     $('#blockUserForm').submit(function (e) {
          e.preventDefault();
        let formData = $('#blockUserForm').serialize();
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            url: "/users/unblock",
            type: "PATCH",
            data: formData,
            beforeSend: function () {
                $.LoadingOverlay('show');
            },
            processData: false,
            success: function (response) {
                $.LoadingOverlay('hide');
                if(response.status == 422){
                    toastr.error(response.message);
                }else if(response.status == 500){
                    toastr.error(response.message);
                }else{
                    $("#blockUserModal").modal("hide");
                    location.reload();
                }
            },
        });
     });
});
// matches table
$(document).ready(function () {
    var url = $('#matchTab').attr('data-url');
alert(1);
        var season = $("#seasonId").val();
        var region = $("#regionId").val();
        var country = $("#countryId").val();
        var week = $("#weekId").val();
        var ladder = $("#ladderId").val();

        if (region.length == 0 && season.length == 0 && week.length == 0 && country.length == 0 && ladder.length == 0) {

        var Table = $('#matches-table').DataTable({
            dom: 'iBlfrtp',
            searching: true,
            paging: true,
            "language": {
                "info": "_START_-_END_ of _TOTAL_"
            },

            "fnInfoCallback": function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
                $('#matches_info').text(iStart + ' - ' + iEnd + " of " + iTotal);
            },
            lengthChange: false,
            scrollX: 900,
            order: [[0, 'desc']],
            processing: true,
            serverSide: true,
            ajax: url,
            columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'teamTwo',
                name: 'teamTwo'
            },
            {
                data: 'matchTime',
                name: 'matchTime'
            },
            {
                data: 'address',
                name: 'address'
            }, {
                data: 'Status',
                name: 'Status'
            },
            {
                data: 'action',
                'name': 'action'
            }
            ]
        });
        $('#matches-table tbody').on('click', 'button', function () {
        });
        $('#matchSearch').keydown(function () {
            Table.search($(this).val()).draw();
        });

    }
});
    // proposals table
$(document).ready(function () {

    var url = $('#proposalTab').attr('data-url');

        var season = $("#seasonId").val();
        var region = $("#regionId").val();
        var country = $("#countryId").val();
        var week = $("#weekId").val();
        var ladder = $("#ladderId").val();
    if (region.length == 0 && season.length == 0 && week.length == 0 && country.length == 0 && ladder.length == 0) {
        var Table = $('#proposals-table').DataTable({
            dom: 'iBfrtp',
            searching: true,
            paging: true,
            "language": {
                "info": "_START_-_END_ of _TOTAL_"
            },

            "fnInfoCallback": function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
                $('#proposals_info').text(iStart + ' - ' + iEnd + " of " + iTotal);
            },
            lengthChange: false,
            scrollX: 900,
            order: [[0, 'desc']],
            processing: true,
            serverSide: true,
            ajax: url,
            columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'ladder',
                name: 'ladder'
            },
            {
                data: 'matchTime',
                name: 'matchTime'
            },
            {
                data: 'address',
                name: 'address'
            },
                {
                data: 'status',
                name: 'status'

            },
            {
                data: 'acceptedBy',
                name: 'acceptedBy'
            },
            {
                data: 'action',
                'name': 'action'
            }
            ]
        });
        $('#proposals-table tbody').on('click', 'button', function () {
        });
        $('#proposalSearch').keydown(function () {
            Table.search($(this).val()).draw();
        });
    }
});
    // challenge table
$(document).ready(function () {

    var url = $('#challengeTab').attr('data-url');

        var season = $("#seasonId").val();
        var region = $("#regionId").val();
        var country = $("#countryId").val();
        var week = $("#weekId").val();
        var ladder = $("#ladderId").val();

    if (region.length == 0 && season.length == 0 && week.length == 0 && country.length == 0 && ladder.length == 0) {
        var Table = $('#challenges-table').DataTable({
            dom: 'iBlfrtp',
            searching: true,
            paging: true,
            "language": {
                "info": "_START_-_END_ of _TOTAL_"
            },

            "fnInfoCallback": function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
                $('#challenges_info').text(iStart + ' - ' + iEnd + " of " + iTotal);
            },
            lengthChange: false,
            scrollX: 900,
            order: [[0, 'desc']],
            processing: true,
            serverSide: true,
            ajax: url,
            columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'teamTo',
                name: 'teamTo'
            },
            {
                data: 'matchTime',
                name: 'matchTime'
            },
            {
                data: 'address',
                name: 'address'
            },
            {
                data: 'status',
                name: 'status'
            },
            {
                data: 'action',
                'name': 'action'
            }
            ]
        });
        $('#challenges-table tbody').on('click', 'button', function () {
        });
        $('#challengeSearch').keydown(function () {
            Table.search($(this).val()).draw();
        });
    }else {
        applyMatchesFilter();
    }
});
    // ranking table
$(document).ready(function () {

    var url = $('#rankingTab').attr('data-url');

    var season=$('#rankingSeasonTag').val();
    var ladder=$('#rankingLadderTag').val();

    if (season.length == 0 && ladder.length == 0) {
        var Table = $('#rankings-table').DataTable({
            dom: 'iBlfrtp',
            searching: true,
            fixedColumns: true,
            paging: true,
            "language": {
                "info": "_START_-_END_ of _TOTAL_"
            },
            "fnInfoCallback": function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
                $('#rankings_info').text(iStart + ' - ' + iEnd + " of " + iTotal);
            },
            lengthChange: false,
            scrollX: 1200,
            order: [[0, 'desc']],
            processing: true,
            serverSide: true,
            ajax: url,
            columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'fullName',
                name: 'fullName'
            },
            {
                data: 'ranking',
                name: 'ranking'
            },
            {
                data: 'won',
                name: 'won'
            }, {
                data: 'lost',
                name: 'lost'
            }, {
                data: 'points',
                name: 'points'
            },
            ]
        });
        $('#rankings-table tbody').on('click', 'button', function () {
        });
        $("#rankingSearch, #rankingSearch").on("keydown keyup", function (event) {

            let value = $(this).val();
            if (value.length < 0) {
                Table.draw();
            }
            Table.search($(this).val()).draw();

        });
    } else {
        changeTag();
    }

});
    // history table
$(document).ready(function () {

    var url = $('#historyTab').attr('data-url');

    var season="";
    var region="";
    var country="";
    var week="" ;
    var ladder="";

        var Table = $('#history-table').DataTable({
            dom: 'iBlfrtp',
            searching: true,
            paging: true,
            "language": {
                "info": "_START_-_END_ of _TOTAL_"
            },

            "fnInfoCallback": function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
                $('#history_info').text(iStart + ' - ' + iEnd + " of " + iTotal);
            },
            lengthChange: false,
            scrollX: 1200,
            order: [[0, 'desc']],
            processing: true,
            serverSide: true,
            ajax: url,
            columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'amountPlayer',
                        name: 'amountPlayer'
                    },
                    {
                        data: 'partner',
                        name: 'partner'
                    },
                    {
                       data: 'season',
                        name: 'season'
                    },{
                        data: 'ladder',
                        name: 'ladder'
                    },
                    {
                        data: 'buyingDate',
                        name: 'buyingDate'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                ]

        });
        $('#history-table tbody').on('click', 'button', function () {
        });
        $('#buyingHistorySearch').keyup(function () {
            Table.search($(this).val()).draw();
        });
});
// filter form
$(document).ready(function () {

    $('#filterForm').submit(function (e) {
        e.preventDefault();
        applyMatchesFilter();
        $('#filterModal').modal('hide');

                 $('#rankingSeasonTag').find('option[value="' +$("#seasonId").val()  + '"]').attr("selected", true);
        $('#rankingLadderTag').find('option[value="' + $("#ladderId").val() + '"]').attr("selected", true);
        changeTag();

    });
});


function changeTag() {
    var seasonTag = $("#rankingSeasonTag").val();
    var ladderTag = $("#rankingLadderTag").val();
    $('#ladderId').find('option[value="' + $("#rankingLadderTag").val() + '"]').attr("selected", true);
     $('#seasonId').find('option[value="' + $("#rankingSeasonTag").val() + '"]').attr("selected", true);
    var url = $('#rankingTab').attr('data-url');
    saveRankingFilter();
    applyMatchesFilter();
    if (seasonTag != "") {
        url += '?season=' + seasonTag;
    };
    if (ladderTag != "") {
        if (seasonTag != "") {
            url += '&ladder=' + ladderTag;
        } else {
            url += '?ladder=' + ladderTag;
        }
    }

        if ($.fn.DataTable.isDataTable('#rankings-table')) {
            $('#rankings-table').DataTable().destroy();
        }
        $('#rankings-table tbody').empty();
        var Table = $('#rankings-table').DataTable({
            dom: 'iBlfrtp',
            searching: true,
            paging: true,
            "language": {
                "info": "_START_-_END_ of _TOTAL_"
            },

            "fnInfoCallback": function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
                $('#rankings_info').text(iStart + ' - ' + iEnd + " of " + iTotal);
            },
            lengthChange: false,
            order: [[0, 'desc']],
            scrollX: 900,
            processing: true,
            serverSide: true,
            ajax: url,
            columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'fullName',
                name: 'fullName'
            },
            {
                data: 'ranking',
                name: 'ranking'
            },
            {
                data: 'won',
                name: 'won'
            }, {
                data: 'lost',
                name: 'lost'
            }, {
                data: 'points',
                name: 'points'
            },
            ]
        });
    $("#rankingSearch, #rankingSearch").on("keydown keyup", function (event) {

            let value = $(this).val();
            if (value.length < 0) {
                Table.draw();
            }
            Table.search($(this).val()).draw();

        });
    }
function withdrawn(id) {
    var id = id.getAttribute("data-id");
    $('#proposalId').val(id);
    $('#withdrawProposalModal').modal('toggle');

}

function openEditProfileModal(id) {

   $(document).find(".error").removeClass('.error');
     $("label.error").text('');
    $('#date').datepicker({format:'mm/dd/yyyy'});
      $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            url: "/users/profile/"+id,
            type: "GET",
            processData: false,
            success: function (response) {
                if (response.status == 200) {
                    let name = response.user.fullName.split(" ");
                    $('#editProfileId').val(response.user.id);
                    $('#firstName').val(name[0]);
                    $('#lastName').val(name[1]);
                    $('#email').val(response.user.email);
                    $('#userContact').val(response.userDetail.phoneNumber);
                    $('#gender').val(response.userDetail.gender);
                    if (response.experty[0] ) {
                        $('#doubleExperty').val(response.experty[0].level);
                    }
                    if (response.experty[1]) {
                        $('#singleExperty').val(response.experty[1].level);
                    }
                    $('#address').val(response.userDetail.completeAddress);
                    $('#city').val(response.userDetail.city);
                    $('#state').val(response.userDetail.state);
                    $('#country').val(response.userDetail.country);
                    let date = new Date(response.userDetail.dob*1000).toLocaleDateString();
                    $('#date').val(date);
                    $('#userPostalCode').val(response.userDetail.postalCode);
                    if (response.userDetail.emergencyContactName) {
                        let emergencyName = response.userDetail.emergencyContactName.split(" ");
                        $('#emergencyFirstName').val(emergencyName[0]);
                        $('#emergencyLastName').val(emergencyName[1]);
                    }
                    $('#userRelation').val(response.userDetail.emergencyContactRelation);
                    $('#userEmergencyContact').val(response.userDetail.emergencyContactNumber);
                    $('#bio').val(response.userDetail.bio);
                    $('#editProfileModal').modal('show');
                }
            },
        });

 }

//**************** withdraw proposal ****************//
$("#withdrawProposalForm").submit(function (e) {
        e.preventDefault();
        let formData = $('#withdrawProposalForm').serialize();
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            url: "/users/withdraw/proposal",
            type: "POST",
            data: formData,
            beforeSend: function () {
                $.LoadingOverlay('show');
            },
            processData: false,
            success: function (response) {
                $.LoadingOverlay('hide');
                if(response.status == 422){
                    toastr.error(response.message);
                }else if(response.status == 500){
                    toastr.error(response.message);
                }else{
                    $("#withdrawProposalModal").modal("hide");
                    location.reload();
                }
            },
        });
});

function viewScore(id) {
    var source =  $("#image-source").val();
    $('#team1Player1').prop('src', source);

    $('#team1Player2').prop('src',source);

    $('#team2Player1').prop('src',source);

    $('#team2Player2').prop('src',source);

    $("label.error").text('');
    var ch = $(document).find(".error").removeClass('.error');
    var id = id.getAttribute("data-id");
    $('#uploadScore').css('display', 'none');
    $('.uploadScore').css('display', 'none');
    $('#editScore').css('display', 'block');
    $('.editScore').css('display', 'block');
    $('.setScore').prop('readonly', true);
    $('#editScoreMatchId').val(id);
    $('#editMatchScoreModal').modal('show');
    $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            url: "match-score/"+id+"/edit",
            type: "GET",
            beforeSend: function () {
                $.LoadingOverlay('show');
            },
            processData: false,
            success: function (response) {
                $.LoadingOverlay('hide');
                if (response.status == 200) {
                    if (response.match.categoryId == 1) {
                        $('#team1Player2').css('display', 'none');
                        $('#team2Player2').css('display', 'none');
                        $('#team2Player2Name').css('display', 'none');
                        $('#team1Player2Name').css('display', 'none');
                    if (response.user1[0].avatar) {
                        $('#team1Player1').prop('src',response.link.user1link);
                    }
                        if (response.user2[0].avatar) {
                        $('#team2Player1').prop('src',response.link.user2link);
                    }
                        $('#team1Player1Name').text(response.user1[0].fullName);
                        $('#team2Player1Name').text(response.user2[0].fullName);

                    } else  {

                        $('#team1Player2').css('display', 'inline-block');
                        $('#team2Player2').css('display', 'inline-block');
                        $('#team2Player2Name').css('display', 'inline-block');
                        $('#team1Player2Name').css('display', 'inline-block');


                    if (response.user1[0].avatar) {
                        $('#team1Player1').prop('src',response.link.user1link);
                    }
                    if (response.user1[1].avatar) {
                        $('#team1Player2').prop('src',response.link.user1link2);
                    }

                    if (response.user2[0].avatar) {
                        $('#team2Player1').prop('src',response.link.user2link);
                    }
                    if (response.user2[1].avatar) {
                        $('#team2Player2').prop('src',response.link.user2link2);
                    }
                        $('#team1Player1Name').text(response.user1[0].fullName);
                        $('#team1Player2Name').text(response.user1[1].fullName);
                        $('#team2Player1Name').text(response.user2[0].fullName);
                        $('#team2Player2Name').text(response.user2[1].fullName);

                    }
                    var set1count,set2count;
                    if (response.matchDetail.teamOneSetOneScore) {
                        set1count = response.matchDetail.teamOneSetOneScore;
                        $('#set1Player1').val(response.matchDetail.teamOneSetOneScore);
                    } else {

                         $('#set1Player1').attr("placeholder", "Not Played");

                    }
                    if (response.matchDetail.teamOneSetTwoScore) {
                        set1count += response.matchDetail.teamOneSetTwoScore;
                        $('#set2Player1').val(response.matchDetail.teamOneSetTwoScore);
                    } else {
                        $('#set2Player1').attr("placeholder", "Not Played");
                    }
                    if (response.matchDetail.teamOneSetThreeScore) {
                        set1count += response.matchDetail.teamOneSetThreeScore;
                        $('#set3Player1').val(response.matchDetail.teamOneSetThreeScore);
                    } else {
                        $('#set3Player1').attr("placeholder", "Not Played");
                    }

                    if (response.matchDetail.teamTwoSetOneScore) {
                        set2count = response.matchDetail.teamTwoSetOneScore;
                        $('#set1Player2').val(response.matchDetail.teamTwoSetOneScore);
                    }else {
                        $('#set1Player2').attr("placeholder", "Not Played");
                    }
                    if (response.matchDetail.teamTwoSetTwoScore) {
                        set2count += response.matchDetail.teamTwoSetTwoScore;
                        $('#set2Player2').val(response.matchDetail.teamTwoSetTwoScore);
                    }else {
                        $('#set2Player2').attr("placeholder", "Not Played");
                    }
                    if (response.matchDetail.teamTwoSetThreeScore) {
                        set2count += response.matchDetail.teamTwoSetThreeScore;
                        $('#set3Player2').val(response.matchDetail.teamTwoSetThreeScore);
                    }else {
                        $('#set3Player2').attr("placeholder", "Not Played");
                    }
                    if (set1count >= set2count) {
                        $('#set1Score').html(`<span>WP(${response.match.winningPoint})</span>`);
                        $('#set2Score').html(`<span>LP(${response.match.losingPoint})</span>`);
                    } else if (set1count <= set2count) {
                        $('#set2Score').html(`<span>WP(${response.match.winningPoint})</span>`);
                        $('#set1Score').html(`<span>LP(${response.match.losingPoint})</span>`);
                    }

                }

            },
        });

}

//***************** update score and upload score ***********/
function toggleButton(e) {
    e.preventDefault();
    if (e.target.id == 'editScore') {

        if (!$('#set2Player1').val()) {
            $('#set2Player1').attr("placeholder", "Enter Score");
        }if (!$('#set2Player2').val()) {
            $('#set2Player2').attr("placeholder", "Enter Score");
        }

        if (!$('#set3Player1').val()) {
            $('#set3Player1').attr("placeholder", "Enter Score");
        }if (!$('#set3Player2').val()) {
            $('#set3Player2').attr("placeholder", "Enter Score");
        }
        $('#editScore').css('display', 'none');
        $('.editScore').css('display', 'none');
        $('.setScore').prop('readonly', false);
        $('#uploadScore').css('display', 'block');
     $('.uploadScore').css('display', 'block');
    } else {
        $('#uploadScore').css('display', 'none');
        $('.uploadScore').css('display', 'none');
        $('.setScore').prop('readonly', false);
        $('#editScore').css('display', 'block');
    }
}

//********************  get single challenge for withdraw********************/
function withdrawnChallenge(id) {
    var id = id.getAttribute("data-id");
    $('#challengeId').val(id);
    $('#withdrawChallengeModal').modal('toggle');

}

//******************** withdraw challenge ********************/
$("#withdrawChallengeForm").submit(function (e) {
    e.preventDefault();
        let formData = $('#withdrawChallengeForm').serialize();
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            url: "/users/withdraw/challenge",
            type: "POST",
            data: formData,
            beforeSend: function () {
                $.LoadingOverlay('show');
            },
            processData: false,
            success: function (response) {
                $.LoadingOverlay('hide');
                if (response.status == 422) {
                    toastr.error(response.message);
                } else if (response.status == 500) {
                    toastr.error(response.message);
                } else {
                    $("#withdrawChallengeModal").modal("hide");
                    location.reload();
                }
            },
        });

    });
function saveMatchesFilter() {
    var seasonTag = $("#seasonId").val();
    var regionTag = $("#regionId").val();
    var countryTag = $("#countryId").val();
    var weekTag = $("#weekId").val();
    var ladderTag = $("#ladderId").val();
    var formData = {
        'season': seasonTag,
        'region': regionTag,
        'country': countryTag,
        'week': weekTag,
        'ladder':ladderTag
    }
    $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
            });
            $.ajax({
                url: "/seasons/filter/save",
                type: "POST",
                data: {'data': formData },
                success: function (response) {

                },
                });

}

function saveRankingFilter() {

    var seasonTag = $("#rankingSeasonTag").val();
    var ladderTag = $("#rankingLadderTag").val();

    var formData = {
        'season': seasonTag,
        'region': null,
        'country': null,
        'week': null,
        'ladder':ladderTag
    }
    $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
            });
            $.ajax({
                url: "/seasons/filter/save",
                type: "POST",
                data: {'data': formData },
                success: function (response) {

                },
                });

}

function applyMatchesFilter() {

        var seasonTag = $("#seasonId").val();
        var regionTag = $("#regionId").val();
        var countryTag = $("#countryId").val();
        var weekTag = $("#weekId").val();
    var ladderTag = $("#ladderId").val();

        var url = $('#matchTab').attr('data-url');
        var proposalUrl = $('#proposalTab').attr('data-url');
        var challengeurl = $('#challengeTab').attr('data-url');

        if (seasonTag != "") {
            url += '?season=' + seasonTag;
            proposalUrl += '?season=' + seasonTag;
            challengeurl += '?season=' + seasonTag;
        };
        if (countryTag != "") {
            if (seasonTag != "") {
            url += '&country=' + countryTag;
            proposalUrl += '&country=' + countryTag;
            challengeurl += '&country=' + countryTag;
            } else {
                url += '?country=' + countryTag;
            proposalUrl += '?country=' + countryTag;
            challengeurl += '?country=' + countryTag;
            }
        }
        if (regionTag != "") {

            if (seasonTag != "" || countryTag != "") {
                url += '&region=' + regionTag;

            proposalUrl += '&region=' + regionTag;
            challengeurl += '&region=' + regionTag;
            } else {

                url += '?region=' + regionTag;

            proposalUrl += '?region=' + regionTag;
            challengeurl += '?region=' + regionTag;
            }
        }
        if (weekTag != "") {

            if (seasonTag != "" || countryTag != "" || regionTag != "") {
                url += '&week=' + weekTag;

            proposalUrl += '&week=' + weekTag;
            challengeurl += '&week=' + weekTag;
            } else {
                url += '?week=' + weekTag;
            proposalUrl += '?week=' + weekTag;
            challengeurl += '?week=' + weekTag;
            }
        }
        if (ladderTag != "") {
            if (seasonTag != "" || countryTag != "" || regionTag != "" || weekTag != "") {
                url += '&ladder=' + ladderTag;
            proposalUrl += '&ladder=' + ladderTag;
            challengeurl += '&ladder=' + ladderTag;
            } else {
                url += '?ladder=' + ladderTag;
            proposalUrl += '?ladder=' + ladderTag;
            challengeurl += '?ladder=' + ladderTag;
            }

        }

        saveMatchesFilter();
        if ($.fn.DataTable.isDataTable('#matches-table')) {
            $('#matches-table').DataTable().destroy();
        }
        if ($.fn.DataTable.isDataTable('#proposals-table')) {
            $('#proposals-table').DataTable().destroy();
        }
        if ($.fn.DataTable.isDataTable('#challenges-table')) {
            $('#challenges-table').DataTable().destroy();
        }
        $('#matches-table tbody').empty();
        $('#proposals-table tbody').empty();
        $('#challenges-table tbody').empty();

        var Table = $('#matches-table').DataTable({
            dom: 'iBlfrtp',
            searching: true,
            paging: true,
            "language": {
                "info": "_START_-_END_ of _TOTAL_"
            },

            "fnInfoCallback": function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
                $('#matches_info').text(iStart + ' - ' + iEnd + " of " + iTotal);
            },
            lengthChange: false,
            order: [[0, 'desc']],
            scrollX: 900,
            processing: true,
            serverSide: true,
            ajax: url,
            columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'teamTwo',
                name: 'teamTwo'
            },
            {
                data: 'matchTime',
                name: 'matchTime'
            },
            {
                data: 'address',
                name: 'address'
            }, {
                data: 'Status',
                name: 'Status'
            },
            {
                data: 'action',
                'name': 'action'
            }
            ]
        });
         $('#matches-table tbody').on('click', 'button', function () {
        });
        $('#matchSearch').keydown(function () {
            Table.search($(this).val()).draw();
        });


        var proposalTable = $('#proposals-table').DataTable({
            dom: 'iBlfrtp',
            searching: true,
            paging: true,
            "language": {
                "info": "_START_-_END_ of _TOTAL_"
            },

            "fnInfoCallback": function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
                $('#proposals_info').text(iStart + ' - ' + iEnd + " of " + iTotal);
            },
            scrollX: 900,
            lengthChange: false,
            order: [[0, 'desc']],
            processing: true,
            serverSide: true,
            ajax: proposalUrl,
            columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'ladder',
                name: 'ladder'
            },
            {
                data: 'matchTime',
                name: 'matchTime'
            },
            {
                data: 'address',
                name: 'address'
            },
                {
                data: 'status',
                name: 'status'

            },
            {
                data: 'acceptedBy',
                name: 'acceptedBy'
            },
            {
                data: 'action',
                'name': 'action'
            }
            ]
        });
        $('#proposals-table tbody').on('click', 'button', function () {
        });
        $('#proposalSearch').keydown(function () {
            proposalTable.search($(this).val()).draw();
        });


        var challengeTable = $('#challenges-table').DataTable({
            dom: 'iBlfrtp',
            searching: true,
            paging: true,
            "language": {
                "info": "_START_-_END_ of _TOTAL_"
            },

            "fnInfoCallback": function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
                $('#challenges_info').text(iStart + ' - ' + iEnd + " of " + iTotal);
            },
            lengthChange: false,
            scrollX: 900,
            order: [[0, 'desc']],
            processing: true,
            serverSide: true,
            ajax: challengeurl,
             columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'teamTo',
                name: 'teamTo'
            },
            {
                data: 'matchTime',
                name: 'matchTime'
            },
            {
                data: 'address',
                name: 'address'
            },
            {
                data: 'status',
                name: 'status'
            },
            {
                data: 'action',
                'name': 'action'
            }
            ]
        });
        $('#challenges-table tbody').on('click', 'button', function () {
        });
        $('#challengeSearch').keydown(function () {
            challengeTable.search($(this).val()).draw();
        });

}

function editPartner(id) {
    var id = id.getAttribute("data-id");
    $('#teamId').val(id);
    $('#editPartnerModal').modal('show');
}

function deleteUser(id) {
    $('#deleteUserId').val(id);
    $('#deleteUserModal').modal('show');
}

function openBlockModal(id) {
     $('#blockUserId').val(id);
    $('#blockUserModal').modal('show');
}



 $(document).on('click','#startChatBtn',function () {
            var userId =  $('#chatUser').val();
            var adminMessage = $('#startChat').val();
            if(adminMessage==""){
                $('#errorMessage').removeClass('d-none');
                $('#myChatModal').modal('show');
            }else{
                Chat(userId,adminMessage);
            }
 });
 $(document).on('click','#closeChatModal',function () {
            var adminMessage = $('#startChat').val("");

        });
function Chat(userId,adminMessage){
            $.ajaxSetup({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
            });

            $.ajax({
                type: 'POST',
                url: '/chat/create-chat',
                beforeSend: function () {
                    $.LoadingOverlay('show');
                },
                data: {
                    'userId': userId,'adminMessage':adminMessage
                },
                success: function (response) {
                $.LoadingOverlay('hide');
            $("#closeChatModal").click()
                }
            });
        }

