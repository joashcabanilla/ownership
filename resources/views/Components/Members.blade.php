@extends('Layouts.Admin')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <h1 class="m-0 font-weight-bold p-2 tabTitle">MEMBERS</h1>
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary card-outline elevation-2 p-3">
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-12">
                        <label for="branchFilter">Branch</label>
                        <div class="form-group">
                            <select class="form-control" id="branchFilter" name="branchFilter">
                                <option value=""> -- Select Branch -- </option>
                                @foreach($branchList as $branch)
                                    <option value="{{$branch}}">{{strtoupper($branch)}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
        
                    <div class="col-lg-4 col-md-4 col-sm-12">
                        <label for="statusFilter">Status</label>
                        <div class="form-group">
                            <select class="form-control" id="statusFilter" name="status">
                                <option value=""> -- Select Status -- </option>
                                <option value="registered">REGISTERED</option>
                                <option value="Unregistered">UNREGISTERED</option> 
                            </select>
                        </div>
                    </div>
        
                    <div class="col-lg-4 col-md-4 col-sm-12">
                        <label for="memberClearFilter"> &nbsp;</label>
                        <div class="form-group">
                            <button class="btn btn-sm btn-primary font-weight-bold" id="memberClearFilter"><i class="fas fa-filter"></i> Clear Filter</button>
                        </div> 
                    </div>
                </div>
            </div>

            <div class="card card-primary card-outline elevation-2 p-3">
                <div class="row mt-1">
                    <div class="col-lg-8 col-md-8 col-sm-12">
                        <div class="form-group">
                            <div class="input-group input-group-lg">
                                <input type="text" class="form-control" id="memberfilterSearch"  placeholder="Search">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-lg btn-default" id="memberSearchBtn">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if(Auth::user()->user_type == "admin")
                        <div class="col-lg-4 col-md-4 col-sm-12">
                            <button type="submit" class="btn btn-lg btn-primary float-lg-right font-weight-bold" id="memberAddBtn">
                                <i class="fa fa-plus" aria-hidden="true"></i> Add Member
                            </button>
                        </div>
                    @else
                        <div class="col-lg-4 col-md-4 col-sm-12">
                            <form id="generateReport" method="POST" target="_blank" action="{{route('admin.report')}}">
                                @csrf
                                <input type="hidden" name="report" value="staffRegisteredMembersList">
                                <button type="submit" class="btn btn-lg btn-primary float-lg-right font-weight-bold" id="memberReport">
                                    <i class="fas fa-file-alt" aria-hidden="true"></i> Generate Report
                                </button>
                            </form>
                        </div>
                    @endif                    
                </div>
                <div class="table-responsive">
                    <table id="memberTable" class="table table-hover table-bordered dataTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Memid</th>
                                <th>Pbno</th>
                                <th>Name</th>
                                <th>Branch</th>
                                <th>Date & Time</th>
                                <th>Registered By</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="memberModal" tabindex="-1" role="dialog" aria-labelledby="memberModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="memberModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span class="modal-closeIcon" aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body addModal">
                <form id="addMemberForm" method="POST">
                    <div class="row">
                            <div class="col-12">
                                <label for="memberBranch">Branch</label>
                                <div class="form-group">
                                    <select class="form-control" id="memberBranch" name="branch" required autofocus>
                                        <option value=""> -- Select Branch -- </option>
                                        @foreach($branchList as $branch)
                                            <option value="{{$branch}}">{{strtoupper($branch)}}</option>
                                        @endforeach
                                    </select>
                                </div>  
                            </div>

                            <div class="col-6">
                                <label for="memid">Memid</label>
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Memid" id="memid" name="memid" autocomplete="false">
                                    <div class="invalid-feedback font-weight-bold"></div>
                                </div>
                            </div>

                            <div class="col-6">
                                <label for="pbno">Pbno</label>
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Pbno" id="pbno" name="pbno" autocomplete="false">
                                    <div class="invalid-feedback font-weight-bold"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="firstname">First Name</label>
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="First Name *" id="firstname" name="firstname" autocomplete="false" required>
                                    <div class="invalid-feedback font-weight-bold"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="middlename">Middle Name</label>
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Middle Name" id="middlename" name="middlename" autocomplete="false">
                                    <div class="invalid-feedback font-weight-bold"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="lastname">Last Name</label>
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Last Name *" id="lastname" name="lastname" autocomplete="false" required>
                                    <div class="invalid-feedback font-weight-bold"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="lastname">Birthdate</label>
                                <div class="form-group">
                                    <input type="date" class="form-control" id="birthdate" name="birthdate" autocomplete="false" required>
                                    <div class="invalid-feedback font-weight-bold"></div>
                                </div>
                            </div>
                    </div>
                    <button class="d-none">Submit</button>
                </form>
            </div>
            <div class="modal-body editModal">
                <form id="editMemberForm" method="POST">
                    <input type="hidden" name="id">
                    <div class="row">
                        <div class="col-6">
                            <label for="editMemid">Memid</label>
                            <div class="form-group">
                                <input type="text" class="form-control font-weight-bold" id="editMemid" name="memid" autocomplete="false" readonly>
                            </div>
                        </div>

                        <div class="col-6">
                            <label for="editPbno">Pbno</label>
                            <div class="form-group">
                                <input type="text" class="form-control font-weight-bold" id="editPbno" name="pbno" autocomplete="false" readonly>
                            </div>
                        </div>
                        <div class="col-12">
                            <label for="editName">Name</label>
                            <div class="form-group">
                                <input type="text" class="form-control font-weight-bold" id="editName" name="name" autocomplete="false" readonly>
                            </div>
                        </div>

                        <div class="col-6">
                            <label for="editBranch">Branch</label>
                            <div class="form-group">
                                <input type="text" class="form-control font-weight-bold" id="editBranch" name="branch" autocomplete="false" readonly>
                            </div>
                        </div>
                        <div class="col-4">
                            <label for="editBirthdate">Birthdate</label>
                            <div class="form-group">
                                <input type="date" class="form-control font-weight-bold" id="editBirthdate" name="birthdate" autocomplete="false" required autofocus>
                            </div>
                        </div>
                        <div class="col-2">
                            <label for="saveBirthdateBtn">&nbsp;</label>
                            <button class="form-control btn btn-sm btn-primary" id="saveBirthdateBtn"><i class="fas fa-save" aria-hidden="true"></i></button>
                        </div>
                    </div>
                    <button class="d-none">Submit</button>
                </form>
                <div class="row mt-1">
                    <div class="col-12">
                        <label for="giveawayItem1" class="font-weight-bold">Ownership Giveaway</label>
                    </div>
                    <div class="col-6">
                        <div class="icheck-success">
                            <input type="checkbox" checked class=" form-control giveawayItems" id="giveawayItem1">
                            <label class="font-weight-bold" for="giveawayItem1">1 Kg of rice</label>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="icheck-success">
                            <input type="checkbox" checked class=" form-control giveawayItems" id="giveawayItem2">
                            <label class="font-weight-bold" for="giveawayItem2">100 Food Stub</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary font-weight-bold" id="memberSubmitBtn">Submit</button>
                <a type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Cancel</a>
            </div>
        </div>
    </div>
</div>
@endsection