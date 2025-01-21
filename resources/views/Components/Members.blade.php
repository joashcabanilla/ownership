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
                                <input type="hidden" name="report" value="staffShareCapitalGiveaway">
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
                                <th>Date Received</th>
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
                <h5 class="modal-title font-weight-bold" id="memberModalLabel">Add Member</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span class="modal-closeIcon" aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="memberForm" method="POST">
                    <input type="hidden" name="id">
                    <input type="hidden" name="updated_by">
                    <div class="row">
                            <div class="col-12">
                                <label for="memberBranch">Branch</label>
                                <div class="form-group">
                                    <select class="form-control" id="memberBranch" name="branch" required autofocus>
                                        <option value=""> -- Select Branch -- </option>
                                        @foreach($branchList as $branch)
                                            <option value=""></option>
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
                                <label for="memberStatus">Status</label>
                                <div class="form-group">
                                    <select class="form-control" id="memberStatus" name="status" required autofocus>
                                        <option value=""> -- Select Status -- </option>
                                            <option value="MIGS">MIGS</option>
                                            <option value="NON-MIGS">NON-MIGS</option>
                                    </select>
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
                                <label for="sharecapital">Share Capital</label>
                                <div class="form-group">
                                    <input type="number" step="0.01" min="0.00" class="form-control" placeholder="0.00" id="sharecapital" name="sharecapital" autocomplete="false">
                                    <div class="invalid-feedback font-weight-bold"></div>
                                </div> 
                            </div>
                    </div>

                    <button type="submit" class="d-none" id="memberSubmitBtn">Submit</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary font-weight-bold">Submit</button>
                <a type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Cancel</a>
            </div>
        </div>
    </div>
</div>
@endsection