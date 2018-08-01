@extends('layout.app')
@section('content')
<div class="container">
  <div class="font">
    <div class="card">
      <p>
        <h3>お問い合わせ内容確認画面</h3>
      </p>
      <div class="form-group">
        <form class="form-group" action="{{url('/registFA')}}" method="post">
            {{csrf_field()}}
            <p><h5>内容に問題なければ送信ボタンを押下してください。</h5></p>
            <p>日付：<input type="date" class="form-control" name="date" value="{{$date}}" readonly></p>
            <p>社員番号：<input type="text" class="form-control" name="id" value="{{$id}}" readonly></p>
            <p>問い合わせ区分：<input type="text" class="form-control" name="category" value="{{$category}}" readonly></p>
            <p>題名：<input type="text" class="form-control" name="mailtitle" value="{{$title}}" readonly></p>
            <p>本文：<textarea class="form-control"  name="mailcontent" cols="50" rows="5" value="" readonly>{{$content}}</textarea></p>
            <button type="submit" class="btn btn-default" name="submit" value="">
              <span class="glyphicon glyphicon-envelope" aria-hidden="true"></span>
              送信
            </button>
            <button type="button" class="btn btn-default" name="back" value="" onclick="history.back()">
              <span class="glyphicon glyphicon-envelope" aria-hidden="true"></span>
              戻る
            </button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
