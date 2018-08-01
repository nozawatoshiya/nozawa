@extends('layout.app')
@section('content')
<div class="container">
  <div class="font">
    @if($message != "")
    <div class="alert alert-danger alert-dismissible">
      <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
      {{$message}}
    </div>
    @endif
    <div class="card">
      <div class="form-group">
        <div class="input-group">
	      <form class="" action="{{url('/archivesUpdate')}}" method="get">
          <button type="submit" class="btn btn-default" name="submit" value="back">
            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
          </button>
          <button type="submit" class="btn btn-default" name="submit" value="next">
            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
          </button>
          <input type="hidden" name="date" value="{{date(('Ym'),strtotime($date))}}">
        </form>
        </div>
        <h3>{{date(('Y年m月'),strtotime($date))}}の勤怠データ</h3>
        <div class="card-archiveslist_h">
          <table class="table">
            <thead>
              <tr>
                <td width="120">出勤日</td>
                <td width="120">区分</td>
                <td width="120">出勤</td>
                <td width="120">退勤</td>
                <td width="120">実働</td>
                <td></td>
              </tr>
            </thead>
          </table>
        </div>
        <div class="card-archiveslist_d">
          @if($message=="")
          <table class="table table-striped table-hover">
            <tbody>
              @php $i=0 @endphp
              @foreach($datas as $data)
              <tr>
                <td width="120">{{date('Y/m/d',strtotime($data['日付']))}}</td>
                <td width="120">{{$data['区分']}}</td>
                <td width="120">@if($data['出勤']!=""){{date('H:i',strtotime($data['出勤']))}}@endif</td>
                <td width="120">@if($data['退勤']!=""){{date('H:i',strtotime($data['退勤']))}}@endif</td>
                <td width="120">@if($data['退勤']!=""){{date('H:i',strtotime($data['実働']))}}@endif</td>
                <td><a href="#" data-toggle="modal" data-target="#archivesModal{{$i}}" >
                      <div class="icon-color-default">
                        <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
                      </div>
                    </a>
                    <!-- モーダル・ダイアログ -->

                    <div class="modal fade" id="archivesModal{{$i}}" tabindex="-1">
  	                   <div class="modal-dialog modal-main">
  		                     <div class="modal-content">
  			                        <div class="modal-header">
  				                            <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
  				                                <h3 class="modal-title">{{date('Y/m/d',strtotime($data['日付']))}}</h3>
  			                        </div>
  			                          <div class="modal-body">
                                    <p>区分：{{$data['区分']}}</p>
                                    <p>出勤：@if($data['出勤']!=""){{date('H:i',strtotime($data['出勤']))}}@endif</p>
                                    <p>退勤：@if($data['退勤']!=""){{date('H:i',strtotime($data['退勤']))}}@endif</p>
                                    <p>休憩：@if($data['退勤']!=""){{date('H:i',strtotime($data['休憩']))}}@endif</p>
                                    <p>実働：@if($data['退勤']!=""){{date('H:i',strtotime($data['実働']))}}@endif</p>
  			                          </div>
  			                        <div class="modal-footer">
  			                        </div>
  		                     </div>
  	                   </div>
                    </div>
                </td>
              </tr>
              @php $i++ @endphp
              @endforeach
            </tbody>
          </table>
          @endif
        </div>
    </div>
  </div>
</div>
@endsection
