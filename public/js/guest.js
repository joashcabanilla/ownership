$(document).ready((e) => {
    $(".searchMemberCon").removeClass("d-none");
    $(".verifyMemberCon").addClass("d-none");
    $(".qrcodeContainer").addClass("d-none");
});

$("#SearchMemberForm").submit((e) => {
    e.preventDefault();
    $.LoadingOverlay("show");
    $.ajax({
        type: "POST",
        url: "searchAccount",
        data: $(e.currentTarget).serializeArray(),
        success: (res) => {
            $.LoadingOverlay("hide");
            if(res.status != "success"){
                Swal.fire({
                    title: "Error",
                    text: res.message,
                    icon: "error",
                    confirmButtonText: "OK",
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });
            }else{
                $(".searchMemberCon").addClass("d-none");
                $(".qrcodeContainer").addClass("d-none");
                $(".verifyMemberCon").removeClass("d-none");
                $(".verifyContainer").empty();
                $(".titleVerify").text("Please verify your account");
                res.member.forEach(member => {
                    let name = member.firstname + " " + member.middlename + " " + member.lastname;
                    let pbno = member.pbno != null ? member.pbno : "No Data";
                    let memid = member.memid != null ? member.memid : "No Data";
                    let verifyElement = $("<div class='row border border-dark p-1 mb-2'><div class='col-12'><p class='font-weight-bold mb-0'><b class='text-danger'>Name:</b> "+name+"</p><p class='font-weight-bold mb-0'><b class='text-danger'>Member Id:</b> "+memid+"</p><p class='font-weight-bold mb-0'><b class='text-danger'>Pb No:</b> "+pbno+"</p><button class='btn btn-primary font-weight-bold float-right'>Verify</button></div></div>");
                    $(verifyElement).find("button").click((e) => {
                        let memidPbno = member.memid != null ? member.memid : member.pbno;
                        $("#generateQrCode").find("input[name='memberId']").val(member.id);
                        $("#generateQrCode").find("input[name='memidPbno']").val(memidPbno);
                        $(".qrcodeContainer").removeClass("d-none");
                        $(".verifyContainer").empty();
                        $(".titleVerify").text("Please input your birthdate");
                    });
                    $(".verifyContainer").append(verifyElement);
                });
            }
        }
    });
});

$("#generateQrCode").submit((e) => {
    e.preventDefault();
    $.LoadingOverlay("show");
    $.ajax({
        type: "POST",
        url: "saveQrCode",
        data: $(e.currentTarget).serializeArray(),
        success: (res) => {
            $.LoadingOverlay("hide");
            if(res.status != "success"){
                Swal.fire({
                    title: "Error",
                    text: res.message,
                    icon: "error",
                    confirmButtonText: "OK",
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });
            }else{
                const qrCode = new QRCodeStyling(res.qrcode.data);
                qrCode.append(document.getElementById("qrcodeCanvas"));
                qrCode.download({ name: res.qrcode.name, extension: "jpeg" });
                $("#qrcodeModal").modal("show");
            }
        }
    });
});

$("#qrCodeOkBtn").click((e) => {
    location.reload();
});

$("#loginForm").submit((e) => {
    e.preventDefault();
    $.ajax({
        type: "POST",
        url: "userLogin",
        data: $(e.currentTarget).serializeArray(),
        success: (res) => {
            if(res.status == "failed"){
                $(".error-text").removeClass("d-none").text(res.message);
                setTimeout(() => {
                    $(".error-text").addClass("d-none");
                },3000);
            }else{
                location.reload();
            }
        }
    });
});

$("#showPassword").change((e)  => {
    if($(e.currentTarget).is(":checked")){
        $("#password").attr("type", "text");
    }else{
        $("#password").attr("type", "password");
    }
});

$("#registerMemberForm").submit((e) => {
    e.preventDefault();
    $.LoadingOverlay("show");
    $.ajax({
        type: "POST",
        url: "/admin/registerMember",
        data: $(e.currentTarget).serializeArray(),
        success: (res) => {
            $.LoadingOverlay("hide");
            Swal.fire({
                title: "You have successfully registered.",
                icon: res.status,
                confirmButtonText: "OK",
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((result) => {
                location.reload();
            });
        }
    });
});