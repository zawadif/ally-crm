// var seasonTag = $("#compared").val();
var ctx = document.getElementById("barChart").getContext('2d');
var barChart;
$(document).ready(function () {
    var Table = $('#dashboard-table').DataTable({
        dom: 'lrt',
        searching: false,
        paging: false,
        lengthChange: false,
        ordering:false,
        info: false,
        scrollY: 170,
        processing: true,
        serverSide: true,
        ajax: '/dashboard',
        columns: [
        {
            data: 'fullName',
            name: 'fullName'
        },
        {
            data: 'phoneNumber',
            name: 'phoneNumber'
        },
        {
            data: 'gender',
            name: 'gender'
        },
        {
            data: 'state',
            name: 'state'
        },
        {
            data: 'country',
            name: 'country'
        },
        ]
    });
    function loadChart() {
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        $.ajax({
            url: "/chart",
            type: "GET",
            beforeSend: function () {
                $.LoadingOverlay('show');
            },
            processData: false,
            success: function (response) {
                $.LoadingOverlay('hide');
                if (response.status == 200) {
                    barChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: response.weekLabel,
                            datasets: [{
                                label: 'Current Season',
                                data: response.seasonWeekSum,
                                backgroundColor: "#51337a",
                                barThickness: 10
                            }, {
                                label: 'Previous Season',
                                data: response.previosSeasonWeekSum,
                                backgroundColor: "#9170be",
                                barThickness: 10
                                }],


                        },
                        options: {

                            scales: {
                                xAxes: [{
                                    barPercentage: 0.2,
                                    categoryPercentage: 0.5,
                                     scaleLabel: {
                                         display: true,
                                         labelString: 'Week'
                                        },
                                    gridLines: {
                                        display: false
                                    },

                                    ticks: {
                                        beginAtZero: true
                                    }
                                }],
                                yAxes: [{
                                    scaleLabel: {
                                     display: true,
                                     labelString: 'Sales'
                                        },
                                    gridLines: {
                                        display: false
                                    },

                                    ticks: {

                                        type:'integer',
                                        beginAtZero: true,
                                        callback: function (value, index) {
                                            if (value == 0) {
                                                return value;
                                            } else {

                                                return "$"+value.toFixed(2);
                                            }
                                        }
                                    }
                                }]
                            }
                        }
                    });
                }

            },
        });
    }
    loadChart();
});
function changeTag() {
    seasonTag = $("#compared").val();

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        $.ajax({
            url: "/chart/" + seasonTag,
            type: "GET",
            beforeSend: function () {
                $.LoadingOverlay('show');
            },
            processData: false,
            success: function (response) {
                $.LoadingOverlay('hide');
                if (response.status == 200) {
                    barChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: response.weekLabel,
                            datasets: [{
                                label: 'Current Season',
                                data: response.seasonWeekSum,
                                backgroundColor: "#51337a",
                                barThickness: 30,

                            }, {
                                label: 'Previous Season',
                                data: response.previosSeasonWeekSum,
                                backgroundColor: "#9170be",
                                barThickness: 30,

                            }],

                        },
                        options: {
                            responsive: true,
                            scales: {
                                xAxes: [{
                                    barPercentage: 0.7,
                                    categoryPercentage: 0.4,
                                    offset: true,
                                    scaleLabel: {
                                        display: true,
                                        labelString: 'Week'
                                    },
                                    gridLines: {
                                        display: false
                                    },
                                    ticks: {
                                        beginAtZero: true
                                    },

                                }],
                                yAxes: [{
                                    scaleLabel: {
                                        display: true,
                                        labelString: 'Sales'
                                    },
                                    gridLines: {
                                        display: false
                                    },
                                    ticks: {
                                        type: 'integer',
                                        beginAtZero: true,
                                        callback: function (value, index) {
                                            if (value == 0) {
                                                return value;
                                            } else {

                                                return value.toFixed(2) + "x";
                                            }
                                        }
                                    },

                                }]
                            },


                        }
                    }
                    );
                }

            },
        });
    }

$('.viewAllBtn').on('click', function(){
       window.location.href = "/users/active";
});

$('.seasonSale').on('click', function(){
    window.location.href = "/purchases";
});

$('.paidPlayer').on('click', function(){

       window.location.href = "/users/active";
});

$('.totalUser').on('click', function(){

       window.location.href = "/users/active";
});

$('.supportRequest').on('click', function(){
     window.location.href = "/chat/inbox";
});
