@extends('Layouts.Admin')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <h1 class="m-0 font-weight-bold p-2 tabTitle">DASHBOARD</h1>
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <div class="small-box bg-gradient-orange card card-primary elevation-3">
                        <div class="inner">
                            <h3 class="font-weight-bold text-white totalMembers">{{$data["totalMembers"]}}</h3>
                            <h5 class="font-weight-bold text-white">TOTAL MEMBERS</h5>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <a href="{{route("admin.members")}}" class="small-box-footer font-weight-bold">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <div class="small-box bg-gradient-teal card card-primary elevation-3">
                        <div class="inner">
                            <h3 class="font-weight-bold text-white totalRegistered">{{$data["totalRegistered"]}}</h3>
                            <h5 class="font-weight-bold text-white">TOTAL REGISTERED</h5>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <a href="{{route("admin.members")}}" class="small-box-footer font-weight-bold">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                @php
                    $bgColor = ["bg-gradient-primary","bg-gradient-danger","bg-gradient-success"];
                    $index = 0;
                @endphp
                @foreach($data["totalPerDay"] as $day => $timeData)
                    @php
                        $classname = str_replace(" ","",$day);
                        $color = $bgColor[$index++];
                        $totalDay = 0;
                        foreach ($timeData as $time => $total) {
                            $totalDay += $total;
                        }
                    @endphp
                    <div class="col-lg-2 col-md-6 col-sm-12">
                        <div class="small-box card {{$color}} card-primary elevation-3">
                            <div class="inner">
                                <h3 class="font-weight-bold text-white total{{strtolower($classname)}}">{{number_format($totalDay,0,".",",")}}</h3>
                                <h5 class="font-weight-bold text-white">{{$day}}</h5>
                            </div>
                            <div class="icon">
                                <i class="fas fa-vote-yea"></i>
                            </div>
                            <div class="small-box-footer d-flex justify-content-between pl-2 pr-2"> 
                                @foreach($timeData as $time => $total)
                                    <p class="font-weight-bold m-0 {{strtolower($classname).$time}}">{{$time}}: {{number_format($total,0,".",",")}}</p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline elevation-3 p-2">
                        <div class="table-responsive">
                            <table id="dashboardTable" class="table table-hover table-bordered table-striped m-0">
                                <thead>
                                    <tr class="bg-primary">
                                        <th rowspan="2" class="p-2 text-center align-middle">
                                            <h5 class="font-weight-bolder m-0 p-0">BRANCH</h5>
                                        </th>
                                        @php
                                            $timeList = "";
                                        @endphp
                                        @foreach($scheduleList as $date => $timeData)
                                            @foreach($timeData as $time => $description)
                                               @php
                                                    $labelDay = $description;
                                                    $timeList .= "<th class='p-2 text-center align-middle'>
                                                    <h5 class='font-weight-bolder m-0 p-0'>".date("A",strtotime($time))."</h5></th>";
                                               @endphp
                                            @endforeach
                                            <th colspan="2" class="p-2 text-center align-middle">
                                                <h5 class="font-weight-bolder m-0 p-0">{{$labelDay." - ".strtoupper(date("M j",strtotime($date)))}}</h5>
                                            </th>
                                        @endforeach
                                        <th rowspan="2" class="p-2 text-center align-middle">
                                            <h5 class="font-weight-bolder m-0 p-0">TOTAL</h5>
                                        </th>
                                    </tr>

                                    <tr class="bg-primary">
                                        @php
                                            echo $timeList;
                                        @endphp
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data["totalPerBranch"] as $branch => $dateData)
                                        @php
                                            $totalBranch = 0;
                                        @endphp
                                        <tr class="table-success">
                                            <td class="p-2 text-center align-middle">
                                                <h5 class="font-weight-bolder m-0 p-0">{{$branch}}</h5>
                                            </td>
                                            @foreach($dateData as $date => $timeData)
                                                @foreach($timeData as $time => $total)
                                                @php
                                                    $totalBranch += $total;
                                                    $classname = str_replace(" ","",$branch.$date.$time);
                                                @endphp
                                                    <td class="p-2 text-center align-middle">
                                                        <h5 class="font-weight-bolder m-0 p-0 {{strtolower($classname)}}">{{number_format($total,0,".",",")}}</h5>
                                                    </td>
                                                @endforeach
                                            @endforeach
                                            <td class="p-2 text-center align-middle">
                                                <h5 class="font-weight-bolder m-0 p-0 totalbranch{{strtolower(str_replace(" ","",$branch))}}">{{number_format($totalBranch,0,".",",")}}</h5>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
    </section>
</div>
@endsection