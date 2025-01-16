@extends('Layouts.Guest')
@section('content')
<div class="container-login hold-transition login-page">
    <div class="login-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <img src="{{asset('image/1.png')}}" alt="logo" width="250" />
            </div>
            <div class="card-body">
                @if($member["status"] != "success")
                    <h4 class="font-weight-bold text-success text-center">{{$member["message"]}}</h4>
                @else
                    <h5><b>Ownership Forum Registration</b></h5>
                    <form id="registerMemberForm" method="POST">
                        <input type="hidden" name="memberId" value="{{$member["memberId"]}}">
                        <div class='row border border-dark p-1 mb-2'>
                            <div class='col-12'>
                                <p class='font-weight-bold mb-0'><b class='text-danger'>Name:</b> {{$member["name"]}}</p>
                                
                                <p class='font-weight-bold mb-0'><b class='text-danger'>Member Id:</b> {{$member["memid"]}}</p>
                                
                                <p class='font-weight-bold mb-0'><b class='text-danger'>Pb No:</b> {{$member["pbno"]}}</p>

                                <p class='font-weight-bold mb-2'><b class='text-danger'>Branch:</b> {{$member["branch"]}}</p>

                                <button class='btn btn-primary font-weight-bold btn-block mb-1'>Register</button></div></div>
                    </form>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection