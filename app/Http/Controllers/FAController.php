<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\FAModel;
use App\Http\Requests\FARequest;

class FAController extends Controller
{
  protected $FAModel;

  public function __construct(){
    $this->FAModel = new FAModel;
  }

  public function checkFA(FARequest $inputs){

    $date = $inputs['date'];
    $id = $inputs['id'];
    $category = $inputs['category'];
    $title = $inputs['mailtitle'];
    $content = $inputs['mailcontent'];

    return view('helpcheck',compact('date','id','category','title','content'));

  }

  public function registFA(Request $inputs){

    $date = date('m/d/Y',strtotime($inputs['date']));
    $id = $inputs['id'];
    $category = $inputs['category'];
    $title = $inputs['mailtitle'];
    $content = $inputs['mailcontent'];

    $result = $this->FAModel->registFA($id,$category,$title,$content);

    if($result['errorCode']!='0'){
      \Session::flash('errorMessage',$result['message'].'管理者に確認してください。');
      return redirect(url('/help'));
    }
    \Session::flash('message', $result['シーケンス'].'で登録されました。担当者より順次返答致します。');

    return redirect(url('/help'));


  }
}
