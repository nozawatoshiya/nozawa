<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\KintaiModel;
use App\Http\Requests\SearchRequest;

class KintaiController extends Controller
{
  protected $KintaiModel;

  public function __construct(){
    $this->KintaiModel = new KintaiModel;
  }

  public function dakoku(Request $request){

    $stime=\Session::get('result.出勤');
    $ftime=\Session::get('result.退勤');
    $id=\Session::get('result.DataID');

    $input=$request->input();
    $dakoku=$input['submit'];
    $time=date('H:i:s',strtotime($input['time']));

    if($dakoku=='出勤' and $stime=="" and $id==""){
      //通常の出勤打刻 勤怠レコード無
      $result=$this->KintaiModel->SyukkinDakoku($dakoku,$time);
      if($result['errorCode']!='0'){
        \Session::flash('message',$result['message']);
        return redirect()->back()->withInput();
      }
      \Session::flash('completeMessage', date("H:i",strtotime($time)).' で出勤打刻しました。');

    }elseif($dakoku=='出勤' and $stime=="" and $id!=""){
      //通常の出勤打刻 勤怠レコード有
      $result=$this->KintaiModel->SyukkinDakokuEdit($dakoku,$id,$time);
      if($result['errorCode']!='0'){
        \Session::flash('message',$result['message']);
        return redirect()->back()->withInput();
      }
      \Session::flash('completeMessage', date("H:i",strtotime($time)).' で出勤打刻しました。');

    }elseif($dakoku=='出勤' and $stime!=""){
      //出勤打刻済み
      \Session::flash('message', date("H:i",strtotime($stime)).' に出勤打刻されてます。');
    }elseif($dakoku=='退勤' and $stime==""){
      //出勤打刻していない
      \Session::flash('message', '出勤打刻してないっす。');
    }elseif($dakoku=='退勤' and $ftime!=""){
      //退勤打刻済
      \Session::flash('message', date("H:i",strtotime($ftime)).' に退勤打刻されてます。');
    }elseif($dakoku=='退勤' and $ftime==""){
      //通常の退勤打刻
      $result=$this->KintaiModel->TaikinDakoku($dakoku,$id,$time);
      if($result['errorCode']!='0'){
        \Session::flash('message',$result['message']);
        return redirect()->back()->withInput();
      }
      \Session::flash('completeMessage', date("H:i",strtotime($time)).' で退勤打刻しました。');

    }
    return redirect(url('/mypage'));
  }

  public function getArchives(){
    $date=date('Ym');
    return redirect(url('/archives/'.$date));
  }

  public function ArchivesUpdate(Request $request){
    $input=$request->input();
    $category=$input['submit'];
    $date=date('Y/m/d',strtotime($input['date'].'01'));

    if($category=='back'){
      $date=date('Ym', strtotime("$date -1 month"));

    }else{
      $date=date("Ym", strtotime("$date +1 month"));
    }
    return redirect(url('/archives/'.$date));
  }

  public function getArchivesList($ym){
    $date=date('m/*/Y',strtotime($ym.'01'));
    $datas=$this->KintaiModel->getMonth($date);

    $date=date('m/d/Y',strtotime($ym.'01'));

    if($datas['errorCode']==0){
      $datas = $datas['datas'];
      //日付順でソート
      array_multisort(array_column($datas, '日付'), SORT_ASC, $datas);
      $message = "";

      return view('archives.archives',compact('datas','date','message'));
    }else{
      //\Session::flash('message', date('Y年m月',strtotime($date)).' のデータは存在しません。');
      $message = date('Y年m月',strtotime($date)).' のデータは存在しません。';
      return view('archives.archives',compact('date','message'));
    }
  }
  public function kintaiEdit(){
      $datas = "" ;
      $message = "" ;
      return view('kintaiEdit',compact('datas','message'));
  }

  public function search(SearchRequest $inputs){
    $id = $inputs['id'];
    $year = $inputs['year'];
    $month = sprintf('%02d', $inputs['month']);

    return redirect(url('/search/'.$year.$month.$id));
  }

  public function searchKintai($ymid){
    $id = mb_substr($ymid, 6);
    $year = mb_substr($ymid, 0, 4);
    $month = mb_substr($ymid, 4, 2);;
    $date = date('m/*/Y',strtotime($year.$month.'01'));

    $result = $this->KintaiModel->getSearchKintai($id,$date);

    if($result['errorCode']==0){
      $datas = $result['datas'];
      //日付順でソート
      array_multisort(array_column($datas, '日付'), SORT_ASC, $datas);
      $message = "";
      $name = current($datas)['氏名'];
      $userID = current($datas)['ユーザーid'];
      $date=date('Y/m/d',strtotime($year.$month.'01'));
      return view('kintaiEdit',compact('datas','date','message','name','userID'));
    }else{
      $message ='社員番号：'.$id.'さんの'.date('Y年m月',strtotime($year.$month.'01')).' の勤怠データは存在しません。';
      $datas = "";
      return  view('kintaiEdit',compact('datas','message'));
    }
  }

  public function registKintai(Request $inputs){
    $date = $inputs['date'];
    $ryear = date('Y',strtotime($date));
    $rmonth = date('m',strtotime($date));
    $category = $inputs['category'];
    $stime = $inputs['stime'];
    $ftime = $inputs['ftime'];
    $btime = $inputs['btime'];

    $name = $inputs['name'];
    $user = $inputs['id'];
    $year = $inputs['year'];
    $month = $inputs['month'];

    if($ryear==$year and $rmonth==$month){
      //表示されている年月の勤怠しか編集できない。
      //レコードを検索する
      $result = $this->KintaiModel->registKintaiFind($user,$date);

      if($result['errorCode']=='0'){
        //レコードがある場合はレコードを編集する。
        $id = $result['RecId'];
        $result = $this->KintaiModel->registKintaiEdit($id,$stime,$ftime,$btime,$category);

        if($result['errorCode']!='0'){
          \Session::flash('message',$result['message']);
          return redirect()->back()->withInput();
        }
      }else{
        //レコードが無い場合はレコードを新規作成する
        $result = $this->KintaiModel->registKintaiCreate($user,$stime,$ftime,$btime,$category,$date);

        if($result['errorCode']!='0'){
          \Session::flash('message',$result['message']);
          return redirect()->back()->withInput();
        }
      }

    }else{
      //エラーメッセージを返す。
      \Session::flash('registErrorMessage','表示されている年月以外の勤怠の登録はできません。');
      return redirect()->back()->withInput();
    }
    \Session::flash('message', $name.'さんの'.date('Y/m/d',strtotime($inputs['date'])).'の勤怠登録が完了しました。');
    return redirect(url('/search/'.$year.$month.$user));
  }

  public function editKintai(Request $inputs){
    $id = $inputs['RecId'];
    $stime = $inputs['stime'];
    $ftime = $inputs['ftime'];
    $btime = $inputs['btime'];
    $category = $inputs['category'];

    $user = $inputs['id'];
    $name = $inputs['name'];
    $year = date('Y',strtotime($inputs['date']));
    $month = date('m',strtotime($inputs['date']));

    $result = $this->KintaiModel->editKintai($id,$stime,$ftime,$btime,$category);

    if($result['errorCode']!='0'){
      \Session::flash('message',$result['message']);
      return redirect()->back()->withInput();
    }
    \Session::flash('message', $name.'さんの'.date('Y/m/d',strtotime($inputs['date'])).'の勤怠登録が完了しました。');

    return redirect(url('/search/'.$year.$month.$user));
  }
}
