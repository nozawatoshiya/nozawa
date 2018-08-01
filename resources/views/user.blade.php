@extends('layout.app')
@section('content')
<div class="container">
  <div class="font">
    @if($errors->has('registAccount')or$errors->has('registName')or$errors->has('registPassword'))
    <div class="alert alert-danger alert-dismissible">
      @if($errors->has('registAccount'))
      <p>
        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
        {{$errors->first('registAccount')}}
      </p>
      @endif
      @if($errors->has('registName'))
      <p>
        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
        {{$errors->first('registName')}}
      </p>
      @endif
      @if($errors->has('registPassword'))
      <p>
        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
        {{$errors->first('registPassword')}}
      </p>
      @endif
      <button type="button" class="close" data-dismiss="alert" aria-label="閉じる"><span aria-hidden="true">×</span></button>
    </div>
    @endif
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
    <h3>登録ユーザー一覧
    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#registUserModal">
    	<span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span>
      ユーザー 登録
    </button>
    </h3>
        <!-- モーダル・ダイアログ -->
        <div class="modal fade" id="registUserModal" tabindex="-1">
           <div class="modal-dialog modal-main">
               <div class="modal-content">
                    <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
                          <h4 class="modal-title">新規登録</h4>
                    </div>
                    <div class="modal-body">
                    <form class="form-group" action="{{url('/registuser')}}" method="post">
                        {{csrf_field()}}
                        <p>社員番号：※必須<input type="text" class="form-control" name="registAccount" value=""></p>
                        <input type="password" class="form-control" name="dummy2" value="" style="display:none" disabled>
                        <p>氏名：※必須<input type="text" class="form-control" name="registName" value=""></p>
                        <p>パスワード：※必須<input type="password" class="form-control" name="registPassword" value=""></p>
                        <p>権限：入力が無い場合、「一般」での登録となります。
                          <select name="registpermission" class="form-control">
                            @foreach(config('listarchives.permission') as $permissionIndex => $permissionName)
                              <option value="{{ $permissionIndex }}">{{$permissionName}}</option>
                            @endforeach
                          </select>
                        </p>
                    </div>
                    <div class="modal-footer">
                      <button type="submit" class="btn btn-default" name="submit" value="">
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
            <td width="120">社員番号</td>
            <td width="120">氏名</td>
            <td width="120">権限</td>
            <td width="120">削除</td>
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
            <td width="120">{{$data['アカウント']}}</td>
            <td width="120">{{$data['氏名']}}</td>
            <td width="120">{{$data['権限']}}</td>
            <td width="120">{{$data['フラグ_削除']}}</td>
            <td width="60"><a href="#" data-toggle="modal" data-target="#UserModal{{$i}}" >
                  <div class="icon-color-default">
                    <span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
                  </div>
                </a>
                <!-- モーダル・ダイアログ -->
                <div class="modal fade" id="UserModal{{$i}}" tabindex="-1">
                   <div class="modal-dialog modal-main">
                       <div class="modal-content">
                            <div class="modal-header">
                                  <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
                                  <h3 class="modal-title">{{$data['アカウント'].' : '.$data['氏名']}}</h3>
                            </div>
                              <div class="modal-body">
                                <form class="form-group" action="{{url('/edituser')}}" method="post">
                                  {{csrf_field()}}
                                  <p>氏名：<input type="text" class="form-control" name="editUserName" value="{{$data['氏名']}}"></p>
                                  <p>権限：
                                    <select name="editUserPermission" class="form-control" value="">
                                      <option value="{{$data['権限']}}">{{$data['権限']}}</option>
                                      @foreach(config('listarchives.permission') as $permissionIndex => $permissionName)
                                        <option value="{{ $permissionIndex }}">{{$permissionName}}</option>
                                      @endforeach
                                    </select>
                                  </p>
                                  <p>削除：
                                    <select name="editUserDelete" class="form-control" value="">
                                      <option value="{{$data['フラグ_削除']}}">{{$data['フラグ_削除']}}</option>
                                      @foreach(config('listarchives.delete') as $deleteIndex => $deleteName)
                                        <option value="{{ $deleteIndex }}">{{$deleteName}}</option>
                                      @endforeach
                                    </select>
                                  </p>
                                  <input type="hidden" name="account" value="{{$data['アカウント']}}">
                                  <input type="hidden" name="RecId" value="{{$data['RecId']}}">
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
            <td>
              @if($data['フラグ_削除']=="")
              <a href="{{url('/deluser/'.$data['RecId'])}}">
                <div class="icon-color-danger">
                  <span class="glyphicon glyphicon-minus-sign" aria-hidden="true" onClick="return dispname('{{$data['氏名']}}')"></span>
                </div>
              </a>
              @endif
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
