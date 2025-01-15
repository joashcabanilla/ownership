@extends('Layouts.Admin')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <h1 class="m-0 font-weight-bold p-2 tabTitle">TIME DEPOSIT GIVEAWAY</h1>
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary card-outline elevation-2 p-3">
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-12">
                        <label for="TDbranchFilter">Branch</label>
                        <div class="form-group">
                            <select class="form-control" id="TDbranchFilter" name="TDbranchFilter">
                                <option value=""> -- Select Branch -- </option>
                                @foreach($branchList as $branch)
                                    <option value="{{$branch->branch}}">{{strtoupper($branch->branch)}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
        
                    <div class="col-lg-4 col-md-4 col-sm-12">
                        <label for="TDstatusFilter">Status</label>
                        <div class="form-group">
                            <select class="form-control" id="TDstatusFilter" name="TDstatus">
                                <option value=""> -- Select Status -- </option>
                                <option value="received">Received</option>
                                <option value="notreceived">Not yet received</option> 
                            </select>
                        </div>
                    </div>
        
                    <div class="col-lg-4 col-md-4 col-sm-12">
                        <label for="TDmemberClearFilter"> &nbsp;</label>
                        <div class="form-group">
                            <button class="btn btn-sm btn-primary font-weight-bold" id="TDmemberClearFilter"><i class="fas fa-filter"></i> Clear Filter</button>
                        </div> 
                    </div>
                </div>
            </div>

            <div class="card card-primary card-outline elevation-2 p-3">
                <div class="row mt-1">
                    <div class="col-lg-8 col-md-8 col-sm-12">
                        <div class="form-group">
                            <div class="input-group input-group-lg">
                                <input type="text" class="form-control" id="TDfilterSearch"  placeholder="Search">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-lg btn-default" id="TDSearchBtn">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if(Auth::user()->user_type == "admin")
                    <div class="col-lg-4 col-md-4 col-sm-12">
                        <button type="submit" class="btn btn-lg btn-primary float-lg-right font-weight-bold" id="TDAddBtn">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add Member
                        </button>
                    </div>
                    @else
                    <div class="col-lg-4 col-md-4 col-sm-12">
                        <form id="generateReport" method="POST" target="_blank" action="{{route('admin.report')}}">
                            @csrf
                            <input type="hidden" name="report" value="staffTimeDepositGiveaway">
                            <button type="submit" class="btn btn-lg btn-primary float-lg-right font-weight-bold" id="TDreport">
                                <i class="fas fa-file-alt" aria-hidden="true"></i> Generate Report
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
                <div class="table-responsive">
                    <table id="TDtable" class="table table-hover table-bordered dataTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Time Deposit</th>
                                <th>Branch</th>
                                <th>Date Received</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="tdMemberModal" tabindex="-1" role="dialog" aria-labelledby="tdMemberModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="tdMemberModalLabel">Add Member</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span class="modal-closeIcon" aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="tdMemberForm" method="POST">
                    <div class="row">
                            <div class="col-12">
                                <label for="tdMemberBranch">Branch</label>
                                <div class="form-group">
                                    <select class="form-control" id="tdMemberBranch" name="branch" required autofocus>
                                        <option value=""> -- Select Branch -- </option>
                                        @foreach($branchList as $branch)
                                            <option value="{{$branch->branch}}">{{strtoupper($branch->branch)}}</option>
                                        @endforeach
                                    </select>
                                </div>  
                            </div>

                            <div class="col-12">
                                <label for="tdMemberName">Name</label>
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Name *" id="tdMemberName" name="name" autocomplete="false" required>
                                    <div class="invalid-feedback font-weight-bold"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="timeDeposit">Time Deposit</label>
                                <div class="form-group">
                                    <input type="number" step="0.01" min="0.00" class="form-control" placeholder="0.00" id="timeDeposit" name="timedeposit" autocomplete="false" required>
                                    <div class="invalid-feedback font-weight-bold"></div>
                                </div> 
                            </div>
                    </div>

                    <button type="submit" class="d-none" id="tdMemberSubmitBtn">Submit</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary font-weight-bold">Submit</button>
                <a type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Cancel</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="tdGiftModal" tabindex="-1" role="dialog" aria-labelledby="tdGiftModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="tdGiftModalLabel">Time Deposit Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span class="modal-closeIcon" aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="tdGiftsForm" method="POST">
                    <input type="hidden" name="id">
                    <input type="hidden" name="category" value="timedeposit">
                    
                    <div class="row">
                        <div class="col-12">
                            <label for="tdGiftsName">Name</label>
                            <div class="form-group">
                                <input type="text" class="form-control font-weight-bold" placeholder="Name" id="tdGiftsName" name="name" autocomplete="false" readonly>
                            </div>
                        </div>

                        <div class="col-6">
                            <label for="tdGiftsBranch">Branch</label>
                            <div class="form-group">
                                <input type="text" class="form-control font-weight-bold" placeholder="Branch" id="tdGiftsBranch" name="branch" autocomplete="false" readonly>
                            </div>
                        </div>

                        <div class="col-6">
                            <label for="tdGiftsTimedeposit">Time Deposit</label>
                            <div class="form-group">
                                <input type="text" class="form-control font-weight-bold text-danger" placeholder="Time Deposit" id="tdGiftsTimedeposit" name="timedeposit" autocomplete="false" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row TdGiftsContainer">
                        <div class="col-12">
                            <div class="card elevation-1 card-outline card-primary p-2">
                                <div class="row">
                                    <div class="col-12">
                                        <p class="font-weight-bold mb-0">Time Deposit Gifts</p>
                                    </div>
                                </div>
                                <div class="row TdGiftsBreakdown">
                                </div>
                            </div>
                        </div>

                        <div class="col-12 TdGiftCheckBreakdownContainer">
                            <div class="card elevation-1 card-outline card-primary p-2">
                                <div class="row">
                                    <div class="col-12">
                                        <p class="font-weight-bold mb-0">Gift Check Breakdown</p>
                                    </div>
                                </div>
                                <div class="row TdGiftCheckBreakdown">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="d-none" id="tdGiftsSubmitBtn">Submit</button>
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