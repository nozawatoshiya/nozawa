@extends('layout.app')
@section('content')

<div class="container">
  <div class="row sidebar">
		<div class="col-md-2">
      <div class="font">
			  <div class="sidebar-body">

		      <!-- SIDEBAR USERPIC -->
          <!-- END SIDEBAR USERPIC -->
          <!-- SIDEBAR USER TITLE -->
				  <div class="sidebar-title">
					  <div class="sidebar-title-name">
						  <p id="Realdate">　</p>
              <p id="Realtime">　</p>
					  </div>
  				</div>
 				  <!-- END SIDEBAR TITLE -->
				  <!-- SIDEBAR BUTTONS -->
				  <div class="sidebar-buttons">
            <form class="form-group" action="{{url('/dakoku')}}" method="post" name="time">
              {{csrf_field()}}
              <input type="hidden" name="time" value="">
              <button type="submit" class="btn btn-primary" name="submit" value="出勤" onclick="getTime()">出勤</button>
              <button type="submit" class="btn btn-primary" name="submit" value="退勤" onclick="getTime()">退勤</button>
            </form>
          </div>

          <div class="sidebar-disp">
            <p>出勤:@if(Session::has('result.出勤')){{session('result.出勤')}}@endif</p>
            <p>退勤:@if(Session::has('result.出勤')){{session('result.退勤')}}@endif</p>
          </div>

				  <!-- END SIDEBAR BUTTONS -->
				  <!-- SIDEBAR MENU -->
				  <div class="sidebar-menulist">

					  <ul class="nav">
						  <li>
							  <a href="#" data-toggle="modal" data-target="#passModal" >
							  <i class="glyphicon glyphicon-wrench"></i>
							  パスワード変更 </a>
						  </li>
               <!--
	  					<li class="active">
		  					<a href="#">
			  				<i class="glyphicon glyphicon-user"></i>
				  			リストメニュー1 </a>
					  	</li>
  						<li>
   							<a href="#" target="_blank">
		  					<i class="glyphicon glyphicon-ok"></i>
			  				リストメニュー2 </a>
				  		</li>
					  	<li>
						  	<a href="#">
							  <i class="glyphicon glyphicon-flag"></i>
							  リストメニュー3 </a>
						  </li>
              --->
					  </ul>

            <!-- パスワード変更のモーダル・ダイアログ -->
            <div class="modal fade" id="passModal" tabindex="-1">
              <div class="modal-dialog modal-main">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
                    <h3 class="modal-title">パスワード変更</h3>
                  </div>
                  <div class="modal-body">
                    <form class="form-group" action="{{url('/changepass')}}" method="post">
                      {{csrf_field()}}
                      <input type="password" class="form-control" name="dummy2" value="" style="display:none" disabled>
                      <p>現在のパスワード：<input type="password" class="form-control" name="pass" value=""></p>
                      <p>新しいパスワード：<input type="password" class="form-control" name="newpass" value=""></p>
                      <p>新しいパスワード（確認用）：<input type="password" class="form-control" name="checkpass" value=""></p>
                      <input type="hidden" name="id" value="{{session('user.id')}}">
                      <input type="hidden" name="RecId" value="{{session('user.RecId')}}">
                      <input type="hidden" name="password" value="{{session('user.password')}}">
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
  				</div>
          <!-- END MENU -->
      　</div>

        <div class="sidebar-msg">
          @if(Session::has('message'))
            <div class="alert alert-danger alert-dismissible" id="alertfadeout">
              <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                {{session('message')}}
                <button type="button" class="close" data-dismiss="alert" aria-label="閉じる"><span aria-hidden="true">×</span></button>
            </div>
          @endif
          @if(Session::has('completeMessage'))
            <div class="alert alert-success alert-dismissible" id="alertfadeout">
              <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                {{session('completeMessage')}}
                <button type="button" class="close" data-dismiss="alert" aria-label="閉じる"><span aria-hidden="true">×</span></button>
            </div>
          @endif

          @if($errors->has('pass') or $errors->has('newpass') or $errors->has('checkpass'))
            <div class="alert alert-danger alert-dismissible" id="alertfadeout">
              @if($errors->has('pass'))
                <p>
                  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                  {{$errors->first('pass')}}
                </p>
              @endif
              @if($errors->has('newpass'))
                <p>
                  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                  {{$errors->first('newpass')}}
                </p>
              @endif
              @if($errors->has('checkpass'))
                <p>
                  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                  {{$errors->first('checkpass')}}
                </p>
              @endif
              <button type="button" class="close" data-dismiss="alert" aria-label="閉じる"><span aria-hidden="true">×</span></button>
            </div>
          @endif
        </div>
			</div>
		</div>
		<div class="col-md-10">
      <div class="main-content">
        <div class="font">
          <h4>{{session('user.name')}} さんへのメッセージ</h4>
            <div class="card-memo_p">

            @if(session('user.memo_p')!="")
              {!!nl2br(session('user.memo_p'))!!}
            @else
              お知らせはありません。
            @endif
            </div>
            <h4>{{session('user.name')}} さんへのお知らせ</h4>
            <div class="card-memo_ah">
              <table class="table">
                <thead>
                  <tr>
                    <th width="150">日付</th>
                    <th width="150">区分</th>
                    <th width="200">表題</th>
                    <th>内容</th>
                  </tr>
                </thead>
              </table>
            </div>
            <div class="card-memo_a">

            @if(Session::has('bbsa'))
            <table class="table table-striped table-hover">
              @php $i=0 @endphp
              @foreach(session('bbsa') as $data)
              <tbody>
                <tr>
                  <td width="150">{{date('Y/m/d',strtotime($data['日付']))}}</td>
                  <td width="150">{{$data['区分']}}</td>
                  <td width="200">{{$data['トピックス']}}</td>
                  <td><a href="#" data-toggle="modal" data-target="#messageModal{{$i}}" >
                        {{substr($data['内容'],0,40)}}.....
                      </a>
                      <!-- 個別のお知らせモーダル・ダイアログ -->
                      <div class="modal fade" id="messageModal{{$i}}" tabindex="-1">
                        <div class="modal-dialog modal-main">
                          <div class="modal-content">
                            <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
                              <p>
                                <h5 class="modal-title">{{date('Y/m/d',strtotime($data['日付']))}}</h5>
                                <h5>題名 ： {{$data['トピックス']}}</h5>
                              </p>
                            </div>
                            <div class="modal-body">
                              <p>内容：</p>
                              <p>{!!nl2br($data['内容'])!!}</p>
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
           @else
           お知らせはありません。
           @endif
          </div>

          <h4>全体へのお知らせ</h4>
            <div class="card-memo_ah">
              <table class="table">
                <thead>
                  <tr>
                    <th width="150">日付</th>
                    <th width="150">区分</th>
                    <th width="200">表題</th>
                    <th>内容</th>
                  </tr>
                </thead>
              </table>
            </div>
            <div class="card-memo_a">

            @if(Session::has('bbs'))
            <table class="table table-striped table-hover">
              @php $i=0 @endphp
              @foreach(session('bbs') as $data)
              <tbody>
                <tr>
                  <td width="150">{{date('Y/m/d',strtotime($data['日付']))}}</td>
                  <td width="150">{{$data['区分']}}</td>
                  <td width="200">{{$data['トピックス']}}</td>
                  <td><a href="#" data-toggle="modal" data-target="#messageModal{{$i}}" >
                        {{substr($data['内容'],0,40)}}.....
                      </a>
                      <!-- 全体のお知らせモーダル・ダイアログ -->
                      <div class="modal fade" id="messageModal{{$i}}" tabindex="-1">
                        <div class="modal-dialog modal-main">
                          <div class="modal-content">
                            <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
                              <p>
                                <h5 class="modal-title">{{date('Y/m/d',strtotime($data['日付']))}}</h5>
                                <h5>題名 ： {{$data['トピックス']}}</h5>
                              </p>
                            </div>
                            <div class="modal-body">
                              <p>内容：</p>
                              <p>{!!nl2br($data['内容'])!!}</p>
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
           @else
             お知らせはありません。
           @endif
          </div>
        </div>
      </div>
	  </div>
  </div>
</div>

@endsection
