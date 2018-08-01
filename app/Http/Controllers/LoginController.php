<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\KintaiModel;
use App\BBSModel;
use App\BBSAModel;

class LoginController extends Controller
{
    protected $KintaiModel;
    protected $BBSModel;

    public function index(){
      \Session::forget('user');
      return view('login');
    }
    public function login(){
      $this->KintaiModel = new KintaiModel;

      $result=$this->KintaiModel->KintaiFind();
      if($result['errorCode']=='0'){

        \Session::put('result', $result);
      }
      //掲示板野内容を取得
      $time = time();
      $thisMonthM = date('m',$time);
      $thisMonthYm = date('Ym',$time);
      $thisMonthYmd = date('Y/m/d',strtotime($thisMonthYm.'01'));
      $lastMonth = date('m/d/Y',strtotime("$thisMonthYmd -1 month"));

      $date_range = $lastMonth.'...';

      $this->BBSModel = new BBSModel;

      $result=$this->BBSModel->getBBS($date_range);

      if($result['errorCode']=='0'){
        if($result['errorCode']==0){
          if(!array_key_exists("datas",$result)){
            $id=$result['DataID'];
            $data=$result;
            unset($data['errorCode']);
            unset($data['DataID']);

            $result = array('errorCode'=>0,
                            'datas'=>array($id=>$data));
          }
        }
        $bbs = $result['datas'] ;
        //日付順でソート
        array_multisort(array_column($bbs, '日付'), SORT_DESC, $bbs);
        \Session::put('bbs', $bbs);
      }


      $this->BBSAModel = new BBSAModel;
      $id=\session::get('user.id');

      $result=$this->BBSAModel->getBBS($date_range,$id);

      if($result['errorCode']=='0'){
        if($result['errorCode']==0){
          if(!array_key_exists("datas",$result)){
            $id=$result['DataID'];
            $data=$result;
            unset($data['errorCode']);
            unset($data['DataID']);

            $result = array('errorCode'=>0,
                            'datas'=>array($id=>$data));
          }
        }

        $bbsa = $result['datas'] ;
        //日付順でソート
        array_multisort(array_column($bbsa, '日付'), SORT_DESC, $bbsa);
        \Session::put('bbsa', $bbsa);
      }

      return view('mypage');
    }
}
