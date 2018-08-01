@extends('layout.app')
@section('content')
<div class="container">
  <div class="font">
    @if(Session::has('message'))
    <div class="alert alert-success alert-dismissible" id="alertfadeout">
      <span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span>
      {{session('message')}}
      <button type="button" class="close" data-dismiss="alert" aria-label="閉じる"><span aria-hidden="true">×</span></button>
    </div>
    @endif

    @if(Session::has('errorMessage'))
    <div class="alert alert-warning alert-dismissible" id="alertfadeout">
      <span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span>
      {{session('errorMessage')}}
      <button type="button" class="close" data-dismiss="alert" aria-label="閉じる"><span aria-hidden="true">×</span></button>
    </div>
    @endif


    @if($errors->has('category') or $errors->has('mailtitle') or $errors->has('mailcontent'))
    <div class="alert alert-danger alert-dismissible">
      @if($errors->has('category'))
      <p>
        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
        {{$errors->first('category')}}
      </p>
      @endif
      @if($errors->has('mailtitle'))
      <p>
        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
        {{$errors->first('mailtitle')}}
      </p>
      @endif
      @if($errors->has('mailcontent'))
      <p>
        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
        {{$errors->first('mailcontent')}}
      </p>
      @endif
      <button type="button" class="close" data-dismiss="alert" aria-label="閉じる"><span aria-hidden="true">×</span></button>
    </div>
    @endif
    <div class="card">
      <p>
        <h3>お問い合わせ</h3>
      </p>
      <div class="form-group">
        <form class="form-group" action="{{url('/checkFA')}}" method="post">
            {{csrf_field()}}
            <p><h5>内容を入力し送信ボタンを押下してください。</h5></p>
            <p>日付：<input type="date" class="form-control" name="date" value="{{date('Y/m/d')}}" readonly></p>
            <p>社員番号：<input type="text" class="form-control" name="id" value="{{session('user.id')}}" readonly></p>
            @if($errors->has('category'))
            <div class="form-group has-error">
            @endif
            <p>問い合わせ区分：
              <select name="category" class="form-control" style="@if($errors->has('category')){{'background-color:#FED1CF'}}@endif">
                <option value=""></option>
                <option value="テスト2">テスト2</option>
                <option value="テスト1">テスト1</option>
                <option value="その他">その他</option>
              </select>
            </p>
            @if($errors->has('category'))
            </div>
            @endif
            @if($errors->has('mailtitle'))
            <div class="form-group has-error">
            @endif
            <p>題名：
                  <input type="text" class="form-control" name="mailtitle" value="" style="@if($errors->has('mailtitle')){{'background-color:#FED1CF'}}@endif">
            </p>
            @if($errors->has('mailtitle'))
            </div>
            @endif
            @if($errors->has('mailcontent'))
            <div class="form-group has-error">
            @endif
            <p>本文：
                  <textarea class="form-control"  name="mailcontent" cols="50" rows="5" style="@if($errors->has('mailcontent')){{'background-color:#FED1CF'}}@endif"></textarea>
            </p>
            @if($errors->has('mailcontent'))
            </div>
            @endif
          <button type="submit" class="btn btn-default" name="submit" value="">
            <span class="glyphicon glyphicon-envelope" aria-hidden="true"></span>
            送信
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
