@extends('layout.app')
@section('content')
  <!--
      you can substitue the span of reauth email for a input with the email and
      include the remember me checkbox
      -->
      <div class="container">
          <div class="card card-container">
              <!-- <img class="profile-img-card" src="//lh3.googleusercontent.com/-6V8xOA6M7BA/AAAAAAAAAAI/AAAAAAAAAAA/rzlHcD0KYwo/photo.jpg?sz=120" alt="" /> -->
              <img id="profile-img" class="profile-img-card" src="//ssl.gstatic.com/accounts/ui/avatar_2x.png" />
              <p id="profile-name" class="profile-name-card"></p>
              @if(Session::has('Error'))
              <div class="alert alert-danger alert-dismissible">
                <div class="fontsize-loginerror">
                  {{Session('Error')}}
                </div>
              </div>
              @endif
              <form class="form-area" role="form" action="{{url('/check')}}" method="post">
                {{csrf_field()}}
                  <div class="form-group{{$errors->has('ID')?'has-error':''}}">
                    <div class="font">
                      <label for="id">社員番号</label>
                      <input type="text" class="form-control" name="ID" value="">
                      @if($errors->has('ID'))
                      <span class="help-box">
                        <strong>{{$errors->first('ID')}}</strong>
                      </span>
                      @endif
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="font">
                      <label for="password">パスワード</label>
                      <input type="password" class="form-control" name="password" value="">
                      @if($errors->has('password'))
                      <span class="help-box">
                        <strong>{{$errors->first('password')}}</strong>
                      </span>
                      @endif
                    </div>
                  </div>
                  <button type="submit" class="btn btn-primary"name="button">ログイン</button>
              </form><!-- /form -->
          </div><!-- /card-container -->
      </div><!-- /container -->
@endsection
