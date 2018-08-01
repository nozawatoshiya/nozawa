<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\FXModel;

class KintaiModel extends FXModel
{
  public function __construct(){
    $this->Layout = '勤怠';
  }

  public function KintaiFind(){
    $user=\Session::get('user');

    $Params=array('ユーザーid'=>$user['id'],
                  '日付'=>date("m/d/Y"));
    $result=$this->readData($Params);
    return $result;
  }

  public function SyukkinDakoku($dakoku,$time){
    $user=\Session::get('user');

    $Params=array('ユーザーid'=>$user['id'],
                  '日付'=>date("m/d/Y"),
                  '出勤'=>$time);
    $result=$this->createData($Params);
    return $result;
  }

  public function SyukkinDakokuEdit($dakoku,$id,$time){
    $user=\Session::get('user');
    //dd($user);
    $Param=array('出勤'=>$time);
    $result=$this->updateData($Param,$id);
    return $result;
  }

  public function TaikinDakoku($dakoku,$id,$time){
    $user=\Session::get('user');
    //dd($user);
    $Param=array('退勤'=>$time);
    $result=$this->updateData($Param,$id);
    return $result;
  }

  public function getMonth($date){
    $user=\Session::get('user');
    $Params=array('ユーザーid' => $user['id'],
                  '日付' => $date);
    $result = $this->readData($Params);

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
    return $result;
  }

  public function getSearchKintai($id,$date){
    $Params=array('ユーザーid' => $id,
                  '日付' => $date);
    $result = $this->readData($Params);

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
    return $result;
  }

  public function editKintai($id,$stime,$ftime,$btime,$category){
    $Param = array('出勤' => $stime,
                   '退勤' => $ftime,
                   '休憩' => $btime,
                   '区分' => $category);
    $id = $id;

    $result = $this->updateData($Param,$id);

    return $result;
  }

  public function registKintaiFind($user,$date){

    $Params=array('ユーザーid'=>$user,
                  '日付'=>date("m/d/Y",strtotime($date)));
    $result=$this->readData($Params);
    return $result;
  }

  public function registKintaiEdit($id,$stime,$ftime,$btime,$category){
    $Param = array('出勤' => $stime,
                   '退勤' => $ftime,
                   '休憩' => $btime,
                   '区分' => $category);
    $id = $id;

    $result = $this->updateData($Param,$id);

    return $result;
  }

  public function registKintaiCreate($user,$stime,$ftime,$btime,$category,$date){
    $Params=array('ユーザーid'=>$user,
                  '日付' => date("m/d/Y",strtotime($date)),
                  '出勤' => $stime,
                  '退勤' => $ftime,
                  '休憩' => $btime,
                  '区分' => $category);
    $result=$this->createData($Params);
    return $result;
  }


}
