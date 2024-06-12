
//******************** load datatable ********************/
$(document).ready(function () {


        var Table = $('#users-table').DataTable({
            dom: 'iBlfrtp',
            searching: true,
            paging: true,
            "language": {
                "info": "_START_-_END_ of _TOTAL_"
            },
            "fnInfoCallback": function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
                $('#users_info').text(iStart + ' - ' + iEnd + " of " + iTotal);
            },
            lengthChange: false,
            order: [[0, 'desc']],
            scrollX: 900,
            processing: true,
            serverSide: true,
            ajax: "/users/active",
            columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'fullName',
                name: 'fullName'
            }, {
                data: 'membership',
                name: 'membership'
            },
            {
                data: 'phoneNumber',
                name: 'phoneNumber'
            },
            {
                data: 'email',
                name: 'email'
            },
            {
                data: 'memberSince',
                name: 'memberSince'
            },
            {
                data: 'action',
                name: 'action'
            }
            ]
        });
        $('#users-table tbody').on('click', 'button', function () {

        });
        $('#usersearch').keyup(function () {
            Table.search($(this).val()).draw();
        });

    // block user
    var blockTable = $('#blockusers-table').DataTable({
            dom: 'iBlfrtp',
            searching: true,
            paging: true,
            lengthChange: false,
            order: [[0, 'desc']],
            scrollX: 900,
            processing: true,
            serverSide: true,
            ajax: "/users/block",
            columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'fullName',
                name: 'fullName'
            }, {
                data: 'membership',
                name: 'membership'
            },
            {
                data: 'phoneNumber',
                name: 'phoneNumber'
            },
            {
                data: 'email',
                name: 'email'
            },
            {
                data: 'memberSince',
                name: 'memberSince'
            },
            {
                data: 'action',
                name: 'action'
            }
            ]
        });
        $('#blockusers-table tbody').on('click', 'button', function () {
        });
        $('#usearch').keyup(function () {
            blockTable.search($(this).val()).draw();
        });

    // block user
    var deleteTable = $('#deleteusers-table').DataTable({
            dom: 'iBlfrtp',
            searching: true,
            paging: true,
            lengthChange: false,
            order: [[0, 'desc']],
            scrollX: 900,
            processing: true,
            serverSide: true,
            ajax: "/users/deleted",
            columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'name',
                name: 'name'
            }, {
                data: 'createdAt',
                name: 'createdAt'
            },
            ]
        });
        $('#deleteusers-table tbody').on('click', 'button', function () {
        });
        $('#dsearch').keyup(function () {
            deleteTable.search($(this).val()).draw();
        });

});

function deleteUser(id) {
var id = id.getAttribute("data-id");
    $('#deleteUserId').val(id);
    $('#deleteUserModal').modal('show');
}
function unblockUser(id) {
    var id = id.getAttribute("data-id");
    $('#unblockUserId').val(id);
    $('#unblockUserModal').modal('show');
}

// ************** filter datatable ****************//
function changeTag() {
    var userTag = $("#userTag").val();

       var url= '/users/active';
         if (userTag != "") {
           url += '?userTag=' + userTag;
    }
       if ($.fn.DataTable.isDataTable('#users-table')) {
       $('#users-table').DataTable().destroy();
     }
     $('#users-table tbody').empty();
        var Table = $('#users-table').DataTable({
                dom: 'iBlfrtp',
                searching: true,
                paging: true,
                "language": {
                    "info": "_START_-_END_ of _TOTAL_"
                  },

                "fnInfoCallback": function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
                 $('#users').text(iStart+' - '+iEnd+" of "+iTotal);
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
            }, {
                data: 'membership',
                name: 'membership'
            },
            {
                data: 'phoneNumber',
                name: 'phoneNumber'
            },
            {
                data: 'email',
                name: 'email'
            },
            {
                data: 'memberSince',
                name: 'memberSince'
            },
            {
                data: 'action',
                name: 'action'
            }
            ]
        });
     $('#usersearch').keyup(function () {
            Table.search($(this).val()).draw();
        });
}
function viewUserDetail(id) {
    var id = id.getAttribute("data-id");

        document.getElementById("mySidebar").style.width = "250px";
        document.getElementById("main").style.marginLeft = "250px";

      $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            url: "/users/detail/"+id,
            type: "GET",
            processData: false,
            success: function (response) {

                if (response.status == 200) {
                    $('#profileId').val(response.user.id);
                    $('#profileId1').val(response.user.id);
                    $('#userName').html( response.user.fullName);
                    $('#userEmail').html( response.user.email);
                    $('#userPhone').html(response.userDetail.phoneNumber);
                    $('#available').html(response.userDetail.availableCredits);
                    $('#gender').html(response.userDetail.gender);
                    if (response.experty) {
                        if (response.experty[1]) {
                            if (response.experty[1].type == "SINGLE") {
                                $('#singleExperty').html(response.experty[1].level);
                            } else {
                                $('#doubleExperty').html(response.experty[1].level);
                            }
                        }
                        if (response.experty[0]) {
                            if (response.experty[0].type == "SINGLE") {

                                $('#singleExperty').html(response.experty[1].level);
                            } else {

                                $('#doubleExperty').html(response.experty[0].level);
                            }
                        }
                    }
                    $('#address').html(response.userDetail.completeAddress);
                    $('#city').html(response.userDetail.city);
                    $('#state').html(response.userDetail.state);
                    $('#country').html(response.userDetail.country);
                    $('#emergencyName').html(response.userDetail.emergencyContactName);
                    $('#relation').html(response.userDetail.emergencyContactRelation);
                    $('#emergencyContact').html(response.userDetail.emergencyContactNumber); $('#bio').html(response.userDetail.bio);
                    $.each(response.buyingHistory, function (dt) {
                        $('#boughtSeason').append(`<div class="col-5">
                                            <h6>${response.buyingHistory[dt].get_season_id.title+" "+response.buyingHistory[dt].get_season_id.year+" "+response.buyingHistory[dt].get_ladder_id.name}</h6>
                                        </div>
                                        <div class="col-5 text-right">
                                            <h6>${response.buyingHistory[dt].price}</h6>
                                        </div>`);

                    });
                    $('#userDetailModal').modal('show');
                }
            },
        });

}
 function closeNav() {
        document.getElementById("mySidebar").style.width = "0";
        document.getElementById("main").style.marginLeft = "0";
    }
$(document).ready(function () {

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
                    location.reload();
                }
            },
        });
    });
     $('#unblockUserForm').submit(function (e) {
          e.preventDefault();
        let formData = $('#unblockUserForm').serialize();
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
                    $("#unblockUserModal").modal("hide");
                    location.reload();
                }
            },
        });
     });

});

function createChat(id) {
    var name = id.getAttribute("data-name");
    var id = id.getAttribute("data-id");
    $('#chatUserId').val(id);
    $('#chatUserName').val(name);
    $('#myChatModal').modal('show');
}
 $(document).on('click','#startChat',function () {
            var userId =  $('#chatUserId').val();
            var adminMessage = $('#startMessage').val();
            if(adminMessage==""){
                $('#errorMessage').removeClass('d-none');
                $('#myChatModal').modal('show');
            }else{
                Chat(userId,adminMessage);
            }
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


            $("#closeModal").click()
                }
            });
        }
 $(document).on('click','#closeModal',function () {
            var adminMessage = $('#startMessage').val("");

        });
