$(document).ready((e) => {
    var intervalData = {};
    for (let key in intervalData) {
        clearInterval(intervalId[key]);
    }
    if($(".tabTitle").text() == "DASHBOARD"){
        $("#sidebarPushmenuBtn").trigger("click");
        const getDashboardData = () => {
            $.ajax({
                type: "POST",
                url: "/admin/getDashboardData",
                success: (res) => {
                    $(".totalMembers").text(res.data.totalMembers);
                    $(".totalRegistered").text(res.data.totalRegistered);
                    let totalPerDay = res.data.totalPerDay;
                    for(let day in totalPerDay){
                        let classname = day.split(" ").join("").toLowerCase();
                        let totalDay = 0;
                        let timeData = totalPerDay[day];
                        for(let time in timeData){
                            $("."+classname+time).text(time+": "+timeData[time].toLocaleString());
                            totalDay += timeData[time];
                        }
                        $(".total"+classname).text(totalDay.toLocaleString());
                    }

                    let totalPerBranch = res.data.totalPerBranch;
                    for(let branch in totalPerBranch){
                        let dateData = totalPerBranch[branch];
                        let totalBranch = 0;
                        for(let date in dateData){
                            let timeData = dateData[date];
                            for(let time in timeData){
                                let total = timeData[time];
                                let classname = branch+date+time;
                                classname = classname.split(" ").join("").toLowerCase();
                                totalBranch += total;
                                $("."+classname).text(total.toLocaleString());
                            }
                        }
                        let totalBranchClassName = "totalbranch"+branch.split(" ").join("").toLowerCase();
                        $("."+totalBranchClassName).text(totalBranch.toLocaleString());
                    }
                    
                }
            });
        };

        getDashboardData();
        
        intervalData.interval1 = setInterval(() => {
            getDashboardData();
        },3000);

        intervalData.interval2 = setInterval(() => {
            location.reload();
        },180000);
    }
});

let userTable = $('#userTable').on('init.dt', function () {
    $(".dataTables_wrapper").prepend("<div class='dataTables_processing card font-weight-bold d-none' role='status'>Loading Please Wait...<i class='fa fa-spinner fa-spin text-warning'></i></div>");
}).DataTable({
    ordering: false,
    serverSide: true,
    dom: 'rtip',
    columnDefs: [
        { targets: 0, width: '1%', className: "text-center align-middle font-weight-bold p-2" },
        { targets: 1, width: '10%', className: "text-center align-middle font-weight-bold p-2" },
        { targets: 2, width: '20%', className: "text-left align-middle font-weight-bold p-2" },
        { targets: 3, width: '10%', className: "text-center align-middle font-weight-bold p-2" },
        { targets: 4, width: '10%', className: "text-left align-middle font-weight-bold p-2" },
        { targets: 5, width: '10%', className: "text-left align-middle font-weight-bold p-2" },
        { targets: 6, width: '5%', className: "text-center align-middle font-weight-bold p-2" },
    ],
    ajax: {
        url: '/admin/userTable',
        type: 'POST',
        data: function (d) {
            d.filterSearch = $("#filterSearch").val();
            d.filterUserType = $("#filterUserType").val();
        },
        beforeSend: () => {
            $(".dataTables_processing").removeClass("d-none");
        },
        complete: () => {
            $(".dataTables_processing").addClass("d-none");
        }
    }
});

$("#filterSearch").keyup((e) => {
    userTable.draw();
});

$("#filterUserType").change((e) => {
    userTable.draw();
});

$("#addBtn").click((e) => {
    $("#userModal").modal("show");
});

$("#showPassword").change((e) => {
    if ($(e.currentTarget).is(":checked")) {
        $("#addPassword").attr("type", "text");
    } else {
        $("#addPassword").attr("type", "password");
    }
});

$("#defaultPassword").change((e) => {
    if ($(e.currentTarget).is(":checked")) {
        $("#addPassword").val($("#defaultPassword").val());
        $("#addPassword").removeClass("is-invalid");
    } else {
        $("#addPassword").val("");
    }
});

$("#addPassword").keyup((e) => {
    $("#defaultPassword").prop("checked", false);
});

$('#userModal').on('hidden.bs.modal', function (e) {
    $("#addUserType").val("");
    $("#addName").val("");
    $("#addUsername").val("");
    $("#addPassword").val("");
    $("#userForm").find("input[type='checkbox']").prop("checked", false);
    $("#userForm").find("input[type='hidden']").val("");
    $('#userModal').find("input[name='password']").prop("required",true);
    $("#userModalLabel").text("Create New User");
});

$("#userForm").submit((e) => {
    e.preventDefault();
    $.LoadingOverlay("show");
    $.ajax({
        type: "POST",
        url: "/admin/createUpdateUser",
        data: $(e.currentTarget).serializeArray(),
        success: (res) => {
            $.LoadingOverlay("hide");
            if(res.status == "failed"){
                for(let errorKey in res.error){
                    $("#userForm").find("input[name='"+errorKey+"']").addClass("is-invalid").focus().next().text(res.error[errorKey]);
                }
            }else{
                $("#userModal").modal("hide");
                Swal.fire({
                    title: "Successfully Saved.",
                    icon: res.status,
                    confirmButtonText: "OK",
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then((result) => {
                    userTable.ajax.reload(null, false);
                });
            }
        }
    });
});

$("#userForm").find("input").keyup((e) => {
    $(e.currentTarget).removeClass("is-invalid");
});

$('#userTable').on('click', '.editBtn', (e) => {
    let userId = $(e.currentTarget).data("id");
    $("#userModalLabel").text("Update User Info");
    $.LoadingOverlay("show");
    $.ajax({
        type: "POST",
        url: "/admin/getUser",
        data: {id:userId},
        success: (res) => {
            $.LoadingOverlay("hide");
            $('#userModal').find("input[name='id']").val(res.id);
            $('#userModal').find("select[name='userType']").val(res.user_type);
            $('#userModal').find("input[name='name']").val(res.name);
            $('#userModal').find("input[name='username']").val(res.username);
            $('#userModal').find("input[name='password']").prop("required",false);
            $("#userModal").modal("show");
        }
    });
});

$('#userTable').on('click', '.deactivateBtn', (e) => {
    let userId = $(e.currentTarget).data("id");
    $.ajax({
        type: "POST",
        url: "/admin/getUser",
        data: {id:userId},
        success: (res) => {
            $.LoadingOverlay("hide");
            Swal.fire({
                title: "Deactivate Account",
                text: "Are you sure you want to deactivate " + res.name + " account?",
                icon: "question",
                showCancelButton: true,
                showConfirmButton: false,
                showDenyButton:true,
                denyButtonText: "Deactivate",
                iconColor:"#ea5455",
                willOpen: (e) => {
                    $(".swal2-actions").addClass("w-100").css("justify-content","flex-end");
                }
            }).then((result) => {
                if(result.isDenied){
                    $.ajax({
                        type: "POST",
                        url: "/admin/deactivateUser",
                        data: {id:userId},
                        success: (res) => {
                            userTable.ajax.reload(null, false);
                        }
                    });
                }
            });
        }
    });
});

$('#userTable').on('click', '.activateBtn', (e) => {
    let userId = $(e.currentTarget).data("id");
    $.ajax({
        type: "POST",
        url: "/admin/getUser",
        data: {id:userId},
        success: (res) => {
            $.LoadingOverlay("hide");
            Swal.fire({
                title: "Activate Account",
                text: "Are you sure you want to Activate " + res.name + " account?",
                icon: "question",
                showCancelButton: true,
                showConfirmButton: true,
                confirmButtonText: "Activate",
                iconColor:"#2b7d62",
                willOpen: (e) => {
                    $(".swal2-actions").addClass("w-100").css("justify-content","flex-end");
                }
            }).then((result) => {
                if(result.isConfirmed){
                    $.ajax({
                        type: "POST",
                        url: "/admin/deactivateUser",
                        data: {
                            id:userId,
                            status:"activate"
                        },
                        success: (res) => {
                            userTable.ajax.reload(null, false);
                        }
                    });
                }
            });
        }
    });
});

let memberTable = $('#memberTable').on('init.dt', function () {
    $(".dataTables_wrapper").prepend("<div class='dataTables_processing card font-weight-bold d-none' role='status'>Loading Please Wait...<i class='fa fa-spinner fa-spin text-warning'></i></div>");
}).DataTable({
    ordering: false,
    serverSide: true,
    dom: 'rtip',
    columnDefs: [
        { targets: 0, width: '1%', className: "text-center align-middle font-weight-bold p-2" },
        { targets: 1, width: '5%', className: "text-center align-middle font-weight-bold p-2" },
        { targets: 2, width: '5%', className: "text-center align-middle font-weight-bold p-2" },
        { targets: 3, width: '15%', className: "text-left align-middle font-weight-bold p-2" },
        { targets: 4, width: '10%', className: "text-center align-middle font-weight-bold p-2" },
        { targets: 5, width: '10%', className: "text-center align-middle font-weight-bold p-2" },
        { targets: 6, width: '10%', className: "text-center align-middle font-weight-bold p-2" },
        { targets: 7, width: '5%', className: "text-center align-middle font-weight-bold p-2" },
    ],
    ajax: {
        url: '/admin/memberTable',
        type: 'POST',
        data: function (d) {
            d.filterBranch = $("#branchFilter").val();
            d.filterStatus = $("#statusFilter").val();
            d.filterSearch = $("#memberfilterSearch").val();
        },
        beforeSend: () => {
            $(".dataTables_processing").removeClass("d-none");
        },
        complete: () => {
            $(".dataTables_processing").addClass("d-none");
        }
    }
});

$("#branchFilter").change((e) => {
    memberTable.draw();
});

$("#statusFilter").change((e) => {
    memberTable.draw();
});

$("#memberfilterSearch").keyup((e) => {
    memberTable.draw();
});

$("#memberSearchBtn").click((e) => {
    memberTable.draw();
});

$("#memberClearFilter").click((e) => {
    $("#branchFilter,#statusFilter,#memberfilterSearch").val("");
    memberTable.draw();
});

const memberModal = (action) => {
    if(action == "add"){
        $("#memberModalLabel").text("Add Member");
        $('#addMemberForm')[0].reset();
        $("#memberModal").find(".addModal").removeClass("d-none");
        $("#memberModal").find(".editModal").addClass("d-none");
    }else{
        $("#memberModalLabel").text("Member Registration");
        $('#editMemberForm')[0].reset();
        $("#memberModal").find(".editModal").removeClass("d-none");
        $("#memberModal").find(".addModal").addClass("d-none");
    }
};

$("#memberAddBtn").click((e) => {
    memberModal("add");
    $("#memberModal").modal("show");
});

$('#memberTable').on('click', '.editBtn', (e) => {
    memberModal("edit");
    $.LoadingOverlay("show");
    let id = $(e.currentTarget).data("id");
    $.ajax({
        type: "POST",
        url: "/admin/getMember",
        data: {id:id},
        success: (res) => {
            $.LoadingOverlay("hide");
            $("#editMemberForm").find("input").each((key,element) => {
                let keyName = $(element).attr("name");
                if(keyName == "name"){
                    let firstname = res.firstname != null ? res.firstname : "";
                    let middlename = res.middlename != null ? res.middlename : "";
                    let lastname = res.lastname != null ? res.lastname : "";
                    $(element).val(firstname + " " + middlename + " " + lastname);
                }else{
                    $(element).val(res[keyName]);
                }
            });
            $("#memberModal").modal("show");
        }
    });
});

$("#memberSubmitBtn").click((e) => {
    if($("#memberModalLabel").text() == "Add Member"){
        $('#addMemberForm').find("button").trigger("click");
    }else{
        $('#editMemberForm').find("button").trigger("click");
    }
});

$("#addMemberForm").submit((e) => {
    e.preventDefault();
    $.LoadingOverlay("show");
    $.ajax({
        type: "POST",
        url: "/admin/createUpdateMember",
        data: $(e.currentTarget).serializeArray(),
        success: (res) => {
            $.LoadingOverlay("hide");
            $("#memberModal").modal("hide");
            Swal.fire({
                title: "Successfully Saved.",
                icon: res.status,
                confirmButtonText: "OK",
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((result) => {
                memberTable.ajax.reload(null, false);
            });
        }
    });
});

$("#editMemberForm").submit((e) => {
    e.preventDefault();
    $.LoadingOverlay("show");
    $.ajax({
        type: "POST",
        url: "/admin/registerMember",
        data: {memberId:$("#editMemberForm").find("input[name='id']").val()},
        success: (res) => {
            $.LoadingOverlay("hide");
            $("#memberModal").modal("hide");
            Swal.fire({
                title: "Successfully Registered.",
                icon: res.status,
                confirmButtonText: "OK",
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((result) => {
                memberTable.ajax.reload(null, false);
            });
            
        }
    });
});

$("#saveBirthdateBtn").click((e) => {
    e.preventDefault();
    $.LoadingOverlay("show");
    $.ajax({
        type: "POST",
        url: "/admin/createUpdateMember",
        data: $("#editMemberForm").serializeArray(),
        success: (res) => {
            $.LoadingOverlay("hide");
            Swal.fire({
                title: "Birthdate successfully saved.",
                icon: res.status,
                confirmButtonText: "OK",
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((result) => {
                memberTable.ajax.reload(null, false);
            });
        }
    });
});

$("#editBirthdate").keypress((e) => {
    if(e.keyCode == 13){
        $("#saveBirthdateBtn").trigger("click");
    }
});

$(".giveawayItems").click((e) => {
    $(".giveawayItems").prop("checked", true);
});