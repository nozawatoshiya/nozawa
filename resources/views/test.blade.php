@extends('layout.app')
@section('content')

@foreach($datas as $data)
<div class="contener">
  {{$data->ユーザーid}}
</div>
@endforeach
{{$datas->links()}}
@endsection
