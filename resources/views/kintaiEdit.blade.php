@extends('layout.app')
@section('content')
<div class="container">
  <div class="font">
    @if($message != "")
    <div class="alert alert-danger alert-dismissible">
      <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
      {{$message}}
      <button type="button" class="close" data-dismiss="alert" aria-label="閉じる"><span aria-hidden="true">×</span></button>
    </div>
    @endif
    @if($errors->has('id') or $errors->has('year') or $errors->has('month'))
    <div class="alert alert-danger alert-dismissible">
      @if($errors->has('id'))
      <p>
        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
        {{$errors->first('id')}}
      </p>
      @endif
      @if($errors->has('year'))
      <p>
        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
        {{$errors->first('year')}}
      </p>
      @endif
      @if($errors->has('month'))
      <p>
        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
        {{$errors->first('month')}}
      </p>
      @endif
      <button type="button" class="close" data-dismiss="alert" aria-label="閉じる"><span aria-hidden="true">×</span></button>
    </div>
    @endif
    <div class="card">
      <p>
        <h3>勤怠検索</h3>
      </p>
      <div class="form-group">
	        <form class="form-inline" action="{{url('/search')}}" method="post">
           {{csrf_field()}}
           @if($errors->has('id'))
           <div class="form-group has-error">
           @endif
           <label for="id" class="sr-only">社員番号</label>
           <input type="text" class="form-control" name="id" value="@if($datas!=""){{$userID}}@endif" placeholder="社員番号" style="@if($errors->has('id')){{'background-color:#FED1CF'}}@endif">
           @if($errors->has('id'))
           </div>
           @endif
           @if($errors->has('year'))
           <div class="form-group has-error">
           @endif
           <label for="year" class="sr-only">年</label>
           <input type="text" class="form-control" name="year" value="@if($datas!=""){{date('Y',strtotime($date))}}@else{{date('Y')}}@endif" placeholder="年 yyyy" style="@if($errors->has('year')){{'background-color:#FED1CF'}}@endif">
           @if($errors->has('year'))
           </div>
           @endif
           @if($errors->has('month'))
           <div class="form-group has-error">
           @endif
           <label for="month" class="sr-only">月</label>
           <input type="text" class="form-control" name="month" value="@if($datas!=""){{date('m',strtotime($date))}}@else{{date('m')}}@endif" placeholder="月 mm" style="@if($errors->has('month')){{'background-color:#FED1CF'}}@endif">
           @if($errors->has('month'))
           </div>
           @endif
           <button type="submit" class="btn btn-default" name="submit" value="search">
             <span class="glyphicon glyphicon glyphicon-search" aria-hidden="true"></span>
           </button>
          </form>
      </div>
      <p>検索したい勤怠の社員番号、年月を入力し、ボタンを押下してください。</p>
    </div>

    @if(Session::has('message'))
    <div class="alert alert-success alert-dismissible">
      <span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span>
      {{session('message')}}
      <button type="button" class="close" data-dismiss="alert" aria-label="閉じる"><span aria-hidden="true">×</span></button>
    </div>
    @endif

    @if(Session::has('registErrorMessage'))
    <div class="alert alert-warning alert-dismissible">
      <span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span>
      {{session('registErrorMessage')}}
      <button type="button" class="close" data-dismiss="alert" aria-label="閉じる"><span aria-hidden="true">×</span></button>
    </div>
    @endif

    @if($message =="" and $datas !="")
    <h3>{{$name}}さんの{{date(('Y年m月'),strtotime($date))}}の勤怠データ
    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#registModal">
      <span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span>
    	勤怠 登録
    </button>
    </h3>
        <!-- モーダル・ダイアログ -->
        <div class="modal fade" id="registModal" tabindex="-1">
           <div class="modal-dialog modal-main">
               <div class="modal-content">
                    <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
                          <h4 class="modal-title">新規登録</h4>
                    </div>
                    <div class="modal-body">
                    <form class="form-group" action="{{url('/registkintai')}}" method="post">
                        {{csrf_field()}}
                        <p><h5>日付を指定して勤怠を登録できます。</h5></p>
                        <p>日付：<input type="date" class="form-control datepicker" name="date" value=""></p>
                        <p>区分：
                          <select name="category" class="form-control">
                            <option value=""></option>
                            <option value="通常">通常</option>
                            <option value="有給">有給</option>
                            <option value="特別休暇">特別休暇</option>
                          </select>
                        </p>
                        <p>出勤：
                              <input type="time" class="form-control" name="stime" value="">
                        </p>
                        <p>退勤：
                              <input type="time" class="form-control" name="ftime" value="">
                        </p>
                        <p>休憩：<input type="time" class="form-control" name="btime" value=""></p>
                        <input type="hidden" name="id" value="{{$userID}}">
                        <input type="hidden" name="name" value="{{$name}}">
                        <input type="hidden" name="year" value="{{date(('Y'),strtotime($date))}}">
                        <input type="hidden" name="month" value="{{date(('m'),strtotime($date))}}">
                    </div>
                    <div class="modal-footer">
                      <button type="submit" class="btn btn-default" name="submit" value="">
                        <span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span>
                        登録
                      </button>
                    </form>
                    </div>
               </div>
           </div>
        </div>
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
      <table class="table table-striped table-hover">
        <tbody>
          @php $i=0 @endphp
          @foreach($datas as $data)
          <tr>
            <td width="120">{{date('Y/m/d',strtotime($data['日付']))}}</td>
            <td width="120">{{$data['区分']}}</td>
            <td width="120">@if($data['出勤']!=""){{date('H:i',strtotime($data['出勤']))}}@endif</td>
            <td width="120">@if($data['退勤']!=""){{date('H:i',strtotime($data['退勤']))}}@endif</td>
            <td width="120">@if($data['実働']!=""){{date('H:i',strtotime($data['実働']))}}@endif</td>
            <td><a href="#" data-toggle="modal" data-target="#kintaiModal{{$i}}" >
                  <div class="icon-color-default">
                    <span class="glyphicon glyphicon-edit" aria-hidden="true" style="font-size: 20px"></span>
                  </div>
                </a>
                <!-- モーダル・ダイアログ -->

                <div class="modal fade" id="kintaiModal{{$i}}" tabindex="-1">
                   <div class="modal-dialog modal-main">
                       <div class="modal-content">
                            <div class="modal-header">
                                  <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
                                  <h3 class="modal-title">{{date('Y/m/d',strtotime($data['日付']))}}</h3>
                            </div>
                              <div class="modal-body">
                                <form class="form-group" action="{{url('/editkintai')}}" method="post">
                                  {{csrf_field()}}
                                  <p>区分：
                                    <select name="category" class="form-control" value="">
                                      <option value="{{$data['区分']}}">{{$data['区分']}}</option>
                                      <option value="通常">通常</option>
                                      <option value="有給">有給</option>
                                      <option value="特別休暇">特別休暇</option>
                                    </select>
                                  </p>
                                  <p>出勤：<input type="time" class="form-control" name="stime" value="@if($data['出勤']!=""){{date('H:i',strtotime($data['出勤']))}}@endif"></p>
                                  <p>退勤：<input type="time" class="form-control" name="ftime" value="@if($data['退勤']!=""){{date('H:i',strtotime($data['退勤']))}}@endif"></p>
                                  <p>休憩：<input type="time" class="form-control" name="btime" value="@if($data['休憩']!=""){{date('H:i',strtotime($data['休憩']))}}@endif"></p>
                                  <input type="hidden" name="RecId" value="{{$data['RecId']}}">
                                  <input type="hidden" name="id" value="{{$data['ユーザーid']}}">
                                  <input type="hidden" name="name" value="{{$name}}">
                                  <input type="hidden" name="date" value="{{$data['日付']}}">
                              </div>
                              <div class="modal-footer">
                                  <button type="submit" class="btn btn-default" name="submit" value="">
                                    <span class="glyphicon glyphicon-refresh" aria-hidden="true"></span>
                                    更新
                                  </button>
                                </form>
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
    </div>
    @endif

  </div>
</div>
@endsection
