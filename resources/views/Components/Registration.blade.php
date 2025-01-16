@extends('Layouts.Guest')
@section('content')
    <div class="container-login hold-transition login-page">
        <div class="login-box">
            <div class="card card-outline card-primary">
                <div class="card-header text-center">
                    <img src="{{asset('image/1.png')}}" alt="logo" width="250" />
                    <div class="alert alert-danger mt-2 mb-0 error-text d-none font-weight-bold" role="alert">
                        text message error
                    </div>
                </div>
                <div class="card-body searchMemberCon">
                    <h5><b>Search for your account</b></h5>
                    <form id="SearchMemberForm" method="POST">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Member Id / PB No." id="search" name="search" autocomplete="false" autofocus>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-block font-weight-bold"><i class="fas fa-search"></i> Search</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body verifyMemberCon d-none">
                    <h5><b class = "titleVerify">Please verify your account</b></h5>
                    <div class="verifyContainer">
                    </div>
                    <div class="qrcodeContainer d-none">
                        <form id="generateQrCode" method="POST">
                            <label for="memidPbno">Pb No / Member Id</label>
                            <div class="input-group mb-3">
                                <input type="hidden" name="memberId">
                                <input type="text" class="form-control font-weight-bold" id="memidPbno" name="memidPbno" autocomplete="false" disabled>
                            </div>
    
                            <label for="birthdate">Birthdate</label>
                            <div class="input-group mb-3">
                                <input type="date" class="form-control" id="birthdate" name="birthdate" autocomplete="false" required autofocus>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-block font-weight-bold">Register</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="qrcodeModal" tabindex="-1" role="dialog" aria-labelledby="qrcodeModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="d-flex justify-content-center align-items-center flex-column">
                        <h5 class="font-weight-bold text-success">QR code successfully generated.</h5>
                        <div id="qrcodeCanvas"></div>
                        <button type="submit" class="btn btn-primary font-weight-bold w-25" id="qrCodeOkBtn">OK</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection