<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Larafm;
use App\Test;

class TestController extends Controller
{
    function __construct(){
      new Test;
    }

    function index(){
      $inputs = ['日付'=>'1/1/2000','ユーザーid'=>'1000007'];
      $datas = Test::findOrFail(1833)->delete();
      //$data = $datas->data;
      //$date = $data->日付;
      //$name = $data->氏名;
      return view('test',compact('datas'));
    }
}
