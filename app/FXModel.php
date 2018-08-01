<?php namespace App;

use App\Providers;
use Illuminate\Contracts\Foundation\Application;
use App\FX\fxphp\FX;

/*
 *  Name:FXModel
 *  Type:Model
 *  Intro:fxphpを使用してFileMakerとの接続を行うモデルクラス 。
 */

class FXModel
{
  //内部変数
  protected $FXAdapter;     //FX.phpのインスタンス
  protected $Layout;        //レイアウトのインスタンス
  protected $AUser;         //接続用管理ユーザー名
  protected $APass;         //接続用管理パスワード

 /**
 *  コンストラクタ
 *  クラスの初期化を行う
 */
  public function __construct(){
    $this->init();
    return $this;
  }

  //初期化
  public function init(){
    $ConnectSets = config('database.connections.fx');
    $this->FXAdapter = new FX($ConnectSets['hostname'], $ConnectSets['port'], $ConnectSets['type'],$ConnectSets['scheme']);
    $this->FXAdapter->SetDBData($ConnectSets['dbname'] ,$this->Layout,env("FM_MAX"));
    $this->FXAdapter->SetDBUserPass($ConnectSets['username'],$ConnectSets['password']);     //接続情報設定
    $this->FXAdapter->SetCharacterEncoding('utf8');                 //エンコード設定
    $this->FXAdapter->SetDataParamsEncoding('utf8');                //エンコード設定
    return $this;
  }

  //CRUD処理↓---------------------------------------------------------------------------------------------------------------------
  /*
   *【createData】
   * @Param $Params:作成するパラメータ
   * @return array(): 結果
   * Intro:引数で受け取ったパラメータでデータを作成する
   */
  public function createData($Params){
    $this->init();
    foreach ($Params as $key => $value) {
      $this->FXAdapter->AddDBParam($key,$value);
    }
    $result = $this->FXAdapter->FMNew();
    $result=$this->ToArrayData($result);
    if ($result['errorCode']!="0") {
      $result["message"]=$result['errorCode'] . ' ' . $this->getErrorDescription($result['errorCode']);
    }
    return $result;
  }

  /*
   *【readData】
   * @Param $Params:検索するパラメータ
   * @return array(): 結果
   * Intro:引数で受け取ったパラメータでデータを検索
   */
  public function readData($Params){
    $this->init();
    foreach ($Params as $key => $value) {
      $this->FXAdapter->AddDBParam($key,$value,"eq");
    }
    $result=$this->FXAdapter->FMFind();
    if($result['errorCode']==0){
      //エラーが無ければ検索結果を読みやすい配列に加工する
      if ($result['foundCount']=="1") {
        $result=$this->ToArrayData($result);
      }else {
        $result=$this->ToArrayTableData($result);
      }
    }else {
      //エラーが発生していたらエラーメッセージを追加する
      $result["message"]=$result['errorCode'] . ' ' . $this->getErrorDescription($result['errorCode']);
    }
    return $result;
  }

  /*
   *【readDataNoOpt】
   * @Param $Params:検索するパラメータ
   * @return array(): 結果
   * Intro:引数で受け取ったパラメータでデータを検索
   * 絶対検索ではなく演算子を使った検索をする。
   */
  public function readDataNoOpt($Params){
    $this->init();
    foreach ($Params as $key => $value) {
      $this->FXAdapter->AddDBParam($key,$value);
    }
    $result=$this->FXAdapter->FMFind();
    if($result['errorCode']==0){
      //エラーが無ければ検索結果を読みやすい配列に加工する
      if ($result['foundCount']=="1") {
        $result=$this->ToArrayData($result);
      }else {
        $result=$this->ToArrayTableData($result);
      }
    }else {
      //エラーが発生していたらエラーメッセージを追加する
      $result["message"]=$result['errorCode'] . ' ' . $this->getErrorDescription($result['errorCode']);
    }
    return $result;
  }

  /*
   *【updateData】
   * @Param $Params:検索するパラメータ
   *        int $id:更新するレコードのID
   * @return array(): 結果
   * Intro:レコードIDとパラメータを渡し、データを更新する
   */
  public function updateData($Param,$id){
    $this->init();
    foreach ($Param as $key => $value) {
      $this->FXAdapter->AddDBParam($key,$value);
    }
    $this->FXAdapter->SetRecordID($id);
    $result=$this->FXAdapter->FMEdit();
    $result=$this->ToArrayData($result);
    //エラーが発生していたらエラーメッセージを追加する
    if($result['errorCode']!=0){
      $result["message"]=$result['errorCode'] . ' ' . $this->getErrorDescription($result['errorCode']);
    }
    return $result;
  }

  //データを削除する
  /*
   *【deleteData】
   * @Param int $id:削除するレコードのID
   * @return array(): 結果
   * Intro:引数で渡されたレコードIDのレコードを削除する
   */
  public function deleteData($id){
    $this->init();
    $this->FXAdapter->SetRecordID($id);
    $result = $this->FXAdapter->FMDelete();
    //戻り値はBoolで返されるのでそのまま帰す
    return $result;
  }
  //CRUD処理↑---------------------------------------------------------------------------------------------------------------------

  /**
  *【doScript】
  * @param string $script　スクリプト名
  * @param string[] $params,... スクリプトに引数を渡す
  * @intro FileMaker上のスクリプトを実行する
  */
  public function doScript($script,$params=''){
    $this->init();
    if (!empty($params)){
      foreach ($params as $value) {
        $this->FXAdapter->AddDBParam('-script.param',$value);
      }
    }
    $this->FXAdapter->PerformFMScript($script);
    $result = $this->FXAdapter->FMFindAll();
    $result=$this->ToArrayData($result);
    return $result;
}
  //複数のパラメータをセットする
  public function SetParams($Params){
    if (!is_array($Params)) {
      return false;
    }
    foreach ($Params as $key => $value) {
      $this->FXAdapter->AddDBParam($key,$value);
    }
    return true;
  }

  //検索結果をきれいな配列に変換する(検索結果が1つの場合)
  public function ToArrayData($data){
    //検索結果が正常値の場合
    if ($data["errorCode"]==0) {
        $ID=key($data["data"]);
        $data=current(current($data));
        $result["errorCode"]=0;
        foreach ($data as $key => $value) {
          $result[$key]=$value[0];
        }
        //末尾にデータのIDを追加
        $result["DataID"]=$ID;
    }else{
      //エラーの場合
      $result["errorCode"]=$data["errorCode"];
      $result["message"]=$this->getErrorDescription($result["errorCode"]);
    }
    return $result;
  }

  //検索結果をきれいな配列に変換する(検索結果が複数の場合)
  public function ToArrayTableData($data){
    $result["errorCode"]=$data["errorCode"];
    //検索結果が正常値の場合
    if ($result["errorCode"]==0) {
      $data=current($data);
      foreach ($data as $key => $value) {
        $datas[$key]=$value;
        foreach ($datas[$key] as $k=>$val) {
          $vals[$k]=$val[0];
        }
        $datas[$key]=$vals;
      }
      $result['datas']=$datas;
    }else {
      //エラーの場合
      $result["message"]=$this->getErrorDescription($result["errorCode"]);
    }
    return $result;
  }

  //検索結果の件数を取得する
  public function getCount($data){
    //検索結果が正常値の場合
    if ($data["errorCode"]==0) {
      $result=$data["foundCount"];
    }else{
      $result=0;
    }
    return $result;
  }

  /**
   * 詳細：ファイルメーカのコードの詳細を取得
   * @param parameter
   */
  protected function getErrorDescription($error_code,$lang = 'jp'){
      if($lang=='jp'){
          $FM_ERROR = array(
              '-1' => '原因不明のエラー',
              '0' => 'エラーなし',
              '1' => 'ユーザによるキャンセル',
              '2' => 'メモリエラー',
              '3' => 'コマンドが使用できません（たとえば誤ったオペレーティングシステム、誤ったモードなど）',
              '4' => 'コマンドが見つかりません',
              '5' => 'コマンドが無効です（たとえば、[フィールド設定] スクリプトステップに計算式が指定されていない場合など）',
              '6' => 'ファイルが読み取り専用です',
              '7' => 'メモリ不足',
              '8' => '空白の結果',
              '9' => 'アクセス権が不十分です',
              '10' => '要求されたデータが見つかりません',
              '11' => '名前が有効ではありません',
              '12' => '名前がすでに存在します',
              '13' => 'ファイルまたはオブジェクトが使用中です',
              '14' => '範囲外',
              '15' => '０で割ることができません',
              '16' => '処理に失敗したため、再試行が必要です（たとえば、ユーザクエリーなど）',
              '17' => '外国語の文字セットの UTF-16 への変換に失敗しました',
              '18' => '続行するには、クライアントはアカウント情報を指定する必要があります',
              '19' => '文字列に A から Z、a から z、0 から 9（ASCII）以外の文字が含まれています',
              '20' => 'コマンドまたは操作がスクリプトトリガによってキャンセルされました',
              '100' => 'ファイルが見つかりません',
              '101' => 'レコードが見つかりません',
              '102' => 'フィールドが見つかりません',
              '103' => 'リレーションシップが見つかりません',
              '104' => 'スクリプトが見つかりません',
              '105' => 'レイアウトが見つかりません',
              '106' => 'テーブルが見つかりません',
              '107' => '索引が見つかりません',
              '108' => '値一覧が見つかりません',
              '109' => 'アクセス権セットが見つかりません',
              '110' => '関連テーブルが見つかりません',
              '111' => 'フィールドの繰り返しが無効です',
              '112' => 'ウインドウが見つかりません',
              '113' => '関数が見つかりません',
              '114' => 'ファイル参照が見つかりません',
              '115' => 'メニューセットが見つかりません',
              '116' => 'レイアウトオブジェクトが見つかりません',
              '117' => 'データソースが見つかりません',
              '118' => 'テーマが見つかりません',
              '130' => 'ファイルが損傷しているか見つからないため、再インストールする必要があります',
              '131' => '言語パックファイルが見つかりません（テンプレートなど）',
              '200' => 'レコードアクセスが拒否されました',
              '201' => 'フィールドを変更できません',
              '202' => 'フィールドアクセスが拒否されました',
              '203' => 'ファイルに印刷するレコードがないか、入力したパスワードでは印刷できません',
              '204' => 'ソート優先順位に指定されたフィールドにアクセスできません',
              '205' => 'ユーザに新規レコードを作成するアクセス権がありません。 既存のデータはインポートしたデータで上書きされます',
              '206' => 'ユーザにパスワードの変更アクセス権がないか、変更可能なファイルではありません',
              '207' => 'ユーザにデータベーススキーマを変更する十分なアクセス権がないか、変更可能なファイルではありません',
              '208' => 'パスワードに十分な文字が含まれていません',
              '209' => '既存のパスワードと新規パスワードを同一にすることはできません',
              '210' => 'ユーザアカウントが非アクティブです',
              '211' => 'パスワードが期限切れです',
              '212' => 'ユーザアカウントまたはパスワードが無効です。再試行してください',
              '213' => 'ユーザアカウントまたはパスワードが存在しません',
              '214' => 'ログイン試行回数が多すぎます',
              '215' => '管理者権限は複製できません',
              '216' => 'ゲストアカウントは複製できません',
              '217' => 'ユーザに管理者アカウントを変更する十分なアクセス権がありません',
              '218' => 'パスワードとパスワードの確認が一致しません',
              '300' => 'ファイルがロックされているか、使用中です',
              '301' => '別のユーザがレコードを使用中です',
              '302' => '別のユーザがテーブルを使用中です',
              '303' => '別のユーザがデータベーススキーマを使用中です',
              '304' => '別のユーザがレイアウトを使用中です',
              '306' => 'レコード修正 ID が一致しません',
              '307' => 'ホストとの通信エラーのため、トランザクションをロックできませんでした',
              '308' => '別のユーザがテーマを使用中です',
              '400' => '検索条件が空です',
              '401' => '検索条件に一致するレコードがありません',
              '402' => '選択したフィールドはルックアップの照合フィールドではありません',
              '403' => '評価版の FileMaker Pro に設定されている最大レコード数の制限を超過しています',
              '404' => 'ソート順が無効です',
              '405' => '指定したレコード数が除外可能なレコード数を超過しています',
              '406' => '全置換またはシリアル番号の再入力に指定された条件が無効です',
              '407' => '片方または両方の照合フィールドが欠けています（無効なリレーションシップ)',
              '408' => '指定されたフィールドのデータが不適切なため、この処理を実行できません',
              '409' => 'インポート順が無効です',
              '410' => 'エスクポート順が無効です',
              '412' => 'ファイルの修復に、誤ったバージョンの FileMaker Pro が使用されました',
              '413' => '指定されたフィールドのフィールドタイプが不適切です',
              '414' => 'レイアウトに結果を表示できません',
              '415' => '１つまたは複数の必要な関連レコードが使用できません',
              '416' => 'データソーステーブルからプライマリキーが必要です',
              '417' => 'データベースが、サポートされているデータソースではありません',
              '500' => '日付の値が入力値の制限を満たしていません',
              '501' => '時刻の値が入力値の制限を満たしていません',
              '502' => '数字が入力値の制限を満たしていません',
              '503' => 'フィールドの値が入力値の制限オプションに指定されている範囲内に入っていません',
              '504' => 'フィールドの値が入力値の制限オプションで要求されているようにユニークな値になっていません',
              '505' => 'フィールドの値が入力値の制限オプションで要求されているようにデータベースファイル内の既存値になっていません',
              '506' => 'フィールドの値が入力値の制限オプションに指定されている値一覧に含まれていません',
              '507' => 'フィールドの値が入力値の制限オプションに指定されている計算式を満たしません',
              '508' => '検索モードに無効な値が入力されました',
              '509' => 'フィールドに有効な値が必要です',
              '510' => '関連する値が空であるか、使用できません',
              '511' => 'フィールド内の値が最大文字数を超過しました',
              '512' => 'レコードがすでに別のユーザによって変更されています',
              '600' => '印刷エラーが発生しました',
              '601' => 'ヘッダとフッタの高さを加算するとページの高さを超えます',
              '602' => '現在の段数設定ではボディ部分がページ内に収まりません',
              '603' => '印刷接続が遮断されました',
              '700' => 'インポートできないファイルタイプです',
              '706' => 'EPSF ファイルにプレビューイメージがありません',
              '707' => 'グラフィックの変換ファイルが見つかりません',
              '708' => 'ファイルをインポートできないか、ファイルをインポートするにはカラーモニタが必要です',
              '709' => 'QuickTime ムービーのインポートに失敗しました',
              '710' => 'データベースファイルが読み取り専用になっているためQuickTime ファイルの参照を更新できません',
              '711' => 'インポートの変換ファイルが見つかりません',
              '714' => '入力したパスワードでは設定されている権限が不足しているためこの操作は認められていません',
              '715' => '指定された Excel ワークシートまたは名前の付いた範囲がありません',
              '716' => 'ODBC インポートでは、DELETE、INSERT、または UPDATE を使用する SQL クエリーは使用できません',
              '717' => 'インポートまたはエクスポートを続行するための十分な XML/XSL 情報がありません',
              '718' => '（Xerces からの）XML ファイルの解析エラーです',
              '719' => '（Xalan からの）XSL を使用した XML 変換エラーです',
              '720' => 'エクスポート時のエラー。対象のドキュメントフォーマットでは繰り返しフィールドはサポートされていません',
              '721' => 'パーサまたはトランスフォーマで原因不明のエラーが発生しました',
              '722' => 'フィールドのないファイルにデータをインポートすることはできません',
              '723' => 'インポート先のテーブルでレコードを追加または変更する権限がありません',
              '724' => 'インポート先のテーブルにレコードを追加する権限がありません',
              '725' => 'インポート先のテーブルでレコードを変更する権限がありません',
              '726' => 'インポートファイルのレコードの方がインポート先のテーブルのレコードよりも多くなっています。 一部のレコードはインポートされませんでした',
              '727' => 'インポート先のテーブルのレコードの方がインポートファイルのレコードよりも多くなっています。 一部のレコードは更新されません',
              '729' => 'インポート中にエラーが発生しました。 レコードをインポートすることができません',
              '730' => 'サポートされていない Excel のバージョンです（ファイルを Excel 2000 のフォーマット、またはそれ以降のサポートされているバージョンに変換して、もう一度実行してください）',
              '731' => 'インポート元のファイルにデータが含まれていません',
              '732' => 'このファイルには内部に他のファイルが含まれているため、挿入できません',
              '733' => 'テーブルをテーブル自体にインポートすることはできません',
              '734' => 'このファイルタイプをピクチャとして表示することはできません',
              '735' => 'このファイルタイプをピクチャとして表示することはできません。 ファイルとして挿入および表示されます',
              '736' => 'この形式にエクスポートするにはデータが大きすぎます。 入らないデータは切り捨てられます',
              '737' => 'インポート元の Bento テーブルがありません',
              '800' => 'ファイルをディスク上に作成できません',
              '801' => 'システムディスクにテンポラリファイルを作成できません',
              '802' => 'ファイルを開くことができません',
              '803' => 'ファイルが単独使用に設定されているか、またはホストが見つかりません',
              '804' => 'ファイルは現在の状態では読み取り専用として開くことができません',
              '805' => 'ファイルが損傷しています。修復コマンドを使用してください',
              '806' => 'このバージョンの FileMaker Pro ではファイルを開くことができません',
              '807' => 'ファイルが FileMaker Pro のファイルではないか、重大な損傷があります',
              '808' => 'アクセス権情報が壊れているため、ファイルを開くことができません',
              '809' => 'ディスク/ ボリュームがいっぱいです',
              '810' => 'ディスク/ ボリュームがロックされています',
              '811' => 'テンポラリファイルを FileMaker Pro ファイルとして開くことができません',
              '813' => 'ネットワーク上でレコードの同期エラーが発生しました',
              '814' => '最大数のファイルがすでに開いているため、ファイルを開くことができません',
              '815' => 'ルックアップファイルを開くことができません',
              '816' => 'ファイルを変換できません',
              '817' => 'このソリューションに属していないため、ファイルを開くことができません',
              '819' => 'リモートファイルのローカルコピーを保存できません',
              '820' => 'ファイルを閉じる途中です',
              '821' => 'ホストによって接続解除されました',
              '822' => 'FMI ファイルが見つかりません。見つからないファイルを再インストールしてください',
              '823' => 'ファイルをシングルユーザに設定できません。 ゲストが接続しています',
              '824' => 'ファイルが損傷しているか、FileMaker のファイルではありません',
              '825' => 'ファイルには保護ファイルを参照する権限がありません',
              '826' => '指定したファイルパスは有効なパスではありません',
              '850' => 'パスがオペレーティングシステムで有効ではありません',
              '851' => 'ディスクから外部ファイルを削除できません',
              '852' => '外部格納にファイルを書き込めません',
              '900' => 'スペルチェックのエンジンにエラーが発生しています',
              '901' => 'スペルチェック用のメイン辞書がインストールされていません',
              '902' => 'ヘルプシステムを起動できませんでした',
              '903' => '共有ファイルではコマンドを使用できません',
              '905' => 'アクティブなフィールドが選択されていません。 アクティブなフィールドが存在する場合のみコマンドを使用することができます',
              '906' => '現在のファイルは共有されていません。コマンドは、ファイルが共有されている場合のみ使用することができます',
              '920' => 'スペルチェックエンジンを初期化できません',
              '921' => '編集するユーザ辞書をロードできません',
              '922' => 'ユーザ辞書が見つかりません',
              '923' => 'ユーザ辞書が読み取り専用です',
              '951' => '予期しないエラーが発生しました（*）',
              '954' => 'サポートされていない XML 文法です（*）',
              '955' => 'データベース名がありません（*）',
              '956' => 'データベースセッションが最大数を超過しました（*）',
              '957' => 'コマンドが競合しています（*）',
              '958' => 'クエリーに引数がありません（*）',
              '959' => 'カスタム Web 公開テクノロジーが無効です',
              '960' => '引数が無効です',
              '1200' => '一般的な計算エラーです',
              '1201' => '関数の引数が足りません',
              '1202' => '関数の引数が多すぎます',
              '1203' => '計算式が未完了です',
              '1204' => '数字、テキスト、フィールド名、または「(」を入れてください',
              '1205' => 'コメントは「*/」で終了できません',
              '1206' => 'テキスト定数は半角のダブルクォーテーションマークで終わらなければなりません',
              '1207' => 'カッコが一致していません',
              '1208' => '演算子または関数が見つからないか、「(」は指定できません',
              '1209' => '名前（フィールド名またはレイアウト名）が見つかりません',
              '1210' => 'プラグイン関数はすでに登録されています',
              '1211' => 'この関数では一覧を使用できません',
              '1212' => '演算子（+、-、* など）を入れてください',
              '1213' => 'この変数はすでに Let 関数で定義されています',
              '1214' => 'AVERAGE、COUNT、EXTEND、GETREPETITION、MAX、MIN、NPV、STDEV、SUM、および GETSUMMARY 関数で、フィールドの値を指定できない部分に式が使われています',
              '1215' => 'この引数は取得関数の無効な引数です',
              '1216' => 'GetSummary 関数の１番目の引数は、集計フィールドのみに限られます',
              '1217' => '区分けフィールドが無効です',
              '1218' => '数字を評価できません',
              '1219' => 'フィールド固有の式にフィールドは使用できません',
              '1220' => 'フィールドタイプは標準にするか、計算する必要があります',
              '1221' => 'データタイプは数字、日付、時刻、またはタイムスタンプでなければなりません',
              '1222' => '計算式を保存できません',
              '1223' => '指定された関数はまだ実装されていません',
              '1224' => '指定された関数は存在しません',
              '1225' => '指定された関数は、このコンテキストではサポートされていません',
              '1300' => '指定された名前は使用できません',
              '1400' => 'ODBC クライアントドライバの初期化に失敗しました。ODBC クライアントドライバが適切にインストールされていることを確認してください',
              '1401' => '環境の割り当てに失敗しました（ODBC）',
              '1402' => '環境の解放に失敗しました（ODBC）',
              '1403' => '切断に失敗しました（ODBC）',
              '1404' => '接続の割り当てに失敗しました（ODBC）',
              '1405' => '接続の解放に失敗しました（ODBC）',
              '1406' => 'SQL API のチェックに失敗しました（ODBC）',
              '1407' => 'ステートメントの割り当てに失敗しました（ODBC）',
              '1408' => '拡張エラー（ODBC）',
              '1409' => 'エラー（ODBC）',
              '1413' => '通信リンクに失敗しました（ODBC）',
              '1414' => 'SQL ステートメントが長すぎます',
              '1450' => 'PHP アクセス権を拡張する操作が必要です（*）',
              '1451' => '現在のファイルをリモートにする操作が必要です',
              '1501' => 'SMTP の認証に失敗しました',
              '1502' => 'SMTP サーバーによって接続が拒否されました',
              '1503' => 'SSL でエラーが発生しました',
              '1504' => 'SMTP サーバーの接続を暗号化する必要があります',
              '1505' => '指定された認証方法は SMTP サーバーではサポートされていません',
              '1506' => 'E メールは正常に送信されませんでした',
              '1507' => 'SMTP サーバーにログインできませんでした',
              '1550' => 'プラグインをロードできないか、プラグインが有効なプラグインではありません',
              '1551' => 'プラグインをインストールできません。既存のプラグインを削除できないか、フォルダまたはディスクに書き込めません',
              '1626' => 'プロトコルがサポートされていません',
              '1627' => '認証に失敗しました',
              '1628' => 'SSL でエラーが発生しました',
              '1629' => '接続がタイムアウトになりました。タイムアウトの値は 60 秒です',
              '1630' => 'URL書式が正しくありません',
              '1631' => '接続に失敗しました',
          );
      }else {
          $FM_ERROR = array(
              '-1'=>'Unknown error',
              '0'=>'No error',
              '1'=>'User canceled action',
              '2'=>'Memory error',
              '3'=>'Command is unavailable (for example, wrong operating system or mode)',
              '4'=>'Command is unknown',
              '5'=>'Command is invalid (for example, a Set Field script step does not have a calculation specified)',
              '6'=>'File is read-only',
              '7'=>'Running out of memory',
              '8'=>'Empty result',
              '9'=>'Insufficient privileges',
              '10'=>'Requested data is missing',
              '11'=>'Name is not valid',
              '12'=>'Name already exists',
              '13'=>'File or object is in use',
              '14'=>'Out of range',
              '15'=>'Can\'t divide by zero',
              '16'=>'Operation failed; request retry (for example, a user query)',
              '17'=>'Attempt to convert foreign character set to UTF-16 failed',
              '18'=>'Client must provide account information to proceed',
              '19'=>'String contains characters other than A-Z, a-z, 0-9 (ASCII)',
              '20'=>'Command/operation canceled by triggered script',
              '21'=>'Request not supported (for example, when creating a hard link on a file system that does not support hard links)',
              '100'=>'File is missing',
              '101'=>'Record is missing',
              '102'=>'Field is missing',
              '103'=>'Relationship is missing',
              '104'=>'Script is missing',
              '105'=>'Layout is missing',
              '106'=>'Table is missing',
              '107'=>'Index is missing',
              '108'=>'Value list is missing',
              '109'=>'Privilege set is missing',
              '110'=>'Related tables are missing',
              '111'=>'Field repetition is invalid',
              '112'=>'Window is missing',
              '113'=>'Function is missing',
              '114'=>'File reference is missing',
              '115'=>'Menu set is missing',
              '116'=>'Layout object is missing',
              '117'=>'Data source is missing',
              '118'=>'Theme is missing',
              '130'=>'Files are damaged or missing and must be reinstalled',
              '131'=>'Language pack files are missing (such as Starter Solutions)',
              '200'=>'Record access is denied',
              '201'=>'Field cannot be modified',
              '202'=>'Field access is denied',
              '203'=>'No records in file to print, or password doesn\'t allow print access',
              '204'=>'No access to field(s) in sort order',
              '205'=>'User does not have access privileges to create new records; import will overwrite existing data',
              '206'=>'User does not have password change privileges, or file is not modifiable',
              '207'=>'User does not have privileges to change database schema, or file is not modifiable',
              '208'=>'Password does not contain enough characters',
              '209'=>'New password must be different from existing one',
              '210'=>'User account is inactive',
              '211'=>'Password has expired',
              '212'=>'Invalid user account and/or password; please try again',
              '213'=>'User account and/or password does not exist',
              '214'=>'Too many login attempts',
              '215'=>'Administrator privileges cannot be duplicated',
              '216'=>'Guest account cannot be duplicated',
              '217'=>'User does not have sufficient privileges to modify administrator account',
              '218'=>'Password and verify password do not match',
              '300'=>'File is locked or in use',
              '301'=>'Record is in use by another user',
              '302'=>'Table is in use by another user',
              '303'=>'Database schema is in use by another user',
              '304'=>'Layout is in use by another user',
              '306'=>'Record modification ID does not match',
              '307'=>'Transaction could not be locked because of a communication error with the host',
              '308'=>'Theme is locked and in use by another user',
              '400'=>'Find criteria are empty',
              '401'=>'No records match the request',
              '402'=>'Selected field is not a match field for a lookup',
              '403'=>'Exceeding maximum record limit for trial version of FileMaker Pro',
              '404'=>'Sort order is invalid',
              '405'=>'Number of records specified exceeds number of records that can be omitted',
              '406'=>'Replace/reserialize criteria are invalid',
              '407'=>'One or both match fields are missing (invalid relationship)',
              '408'=>'Specified field has inappropriate data type for this operation',
              '409'=>'Import order is invalid',
              '410'=>'Export order is invalid',
              '412'=>'Wrong version of FileMaker Pro used to recover file',
              '413'=>'Specified field has inappropriate field type',
              '414'=>'Layout cannot display the result',
              '415'=>'One or more required related records are not available',
              '416'=>'A primary key is required from the data source table',
              '417'=>'File is not a supported data source',
              '418'=>'Internal failure in INSERT operation into a field',
              '500'=>'Date value does not meet validation entry options',
              '501'=>'Time value does not meet validation entry options',
              '502'=>'Number value does not meet validation entry options',
              '503'=>'Value in field is not within the range specified in validation entry options',
              '504'=>'Value in field is not unique, as required in validation entry options',
              '505'=>'Value in field is not an existing value in the file, as required in validation entry options',
              '506'=>'Value in field is not listed in the value list specified in validation entry option',
              '507'=>'Value in field failed calculation test of validation entry option',
              '508'=>'Invalid value entered in Find mode',
              '509'=>'Field requires a valid value',
              '510'=>'Related value is empty or unavailable',
              '511'=>'Value in field exceeds maximum field size',
              '512'=>'Record was already modified by another user',
              '513'=>'No validation was specified but data cannot fit into the field',
              '600'=>'Print error has occurred',
              '601'=>'Combined header and footer exceed one page',
              '602'=>'Body doesn\'t fit on a page for current column setup',
              '603'=>'Print connection lost',
              '700'=>'File is of the wrong file type for import',
              '706'=>'EPSF file has no preview image',
              '707'=>'Graphic translator cannot be found',
              '708'=>'Can\'t import the file, or need color monitor support to import file',
              '711'=>'Import translator cannot be found',
              '714'=>'Password privileges do not allow the operation',
              '715'=>'Specified Excel worksheet or named range is missing',
              '716'=>'A SQL query using DELETE, INSERT, or UPDATE is not allowed for ODBC import',
              '717'=>'There is not enough XML/XSL information to proceed with the import or export',
              '718'=>'Error in parsing XML file (from Xerces)',
              '719'=>'Error in transforming XML using XSL (from Xalan)',
              '720'=>'Error when exporting; intended format does not support repeating fields',
              '721'=>'Unknown error occurred in the parser or the transformer',
              '722'=>'Cannot import data into a file that has no fields',
              '723'=>'You do not have permission to add records to or modify records in the target table',
              '724'=>'You do not have permission to add records to the target table',
              '725'=>'You do not have permission to modify records in the target table',
              '726'=>'Source file has more records than the target table; not all records were imported',
              '727'=>'Target table has more records than the source file; not all records were updated',
              '729'=>'Errors occurred during import; records could not be imported',
              '730'=>'Unsupported Excel version; convert file to the current Excel format and try again',
              '731'=>'File you are importing from contains no data',
              '732'=>'This file cannot be inserted because it contains other files',
              '733'=>'A table cannot be imported into itself',
              '734'=>'This file type cannot be displayed as a picture',
              '735'=>'This file type cannot be displayed as a picture; it will be inserted and displayed as a file',
              '736'=>'Too much data to export to this format; data will be truncated',
              '738'=>'The theme you are importing already exists',
              '800'=>'Unable to create file on disk',
              '801'=>'Unable to create temporary file on System disk',
              '802'=>'Unable to open file',
              '803'=>'File is single-user, or host cannot be found',
              '804'=>'File cannot be opened as read-only in its current state',
              '805'=>'File is damaged; use Recover command',
              '806'=>'File cannot be opened with this version of FileMaker Pro',
              '807'=>'File is not a FileMaker Pro file or is severely damaged',
              '808'=>'Cannot open file because access privileges are damaged',
              '809'=>'Disk/volume is full',
              '810'=>'Disk/volume is locked',
              '811'=>'Temporary file cannot be opened as FileMaker Pro file',
              '812'=>'Exceeded host’s capacity',
              '813'=>'Record synchronization error on network',
              '814'=>'File(s) cannot be opened because maximum number is open',
              '815'=>'Couldn’t open lookup file',
              '816'=>'Unable to convert file',
              '817'=>'Unable to open file because it does not belong to this solution',
              '819'=>'Cannot save a local copy of a remote file',
              '820'=>'File is being closed',
              '821'=>'Host forced a disconnect',
              '822'=>'FMI files not found; reinstall missing files',
              '823'=>'Cannot set file to single-user; guests are connected',
              '824'=>'File is damaged or not a FileMaker file',
              '825'=>'File is not authorized to reference the protected file',
              '826'=>'File path specified is not a valid file path',
              '827'=>'File was not created because the source contained no data or is a reference',
              '850'=>'Path is not valid for the operating system',
              '851'=>'Cannot delete an external file from disk',
              '852'=>'Cannot write a file to the external storage',
              '853'=>'One or more containers failed to transfer',
              '900'=>'General spelling engine error',
              '901'=>'Main spelling dictionary not installed',
              '902'=>'Could not launch the Help system',
              '903'=>'Command cannot be used in a shared file',
              '905'=>'Command requires a field to be active',
              '906'=>'Current file is not shared; command can be used only if the file is shared',
              '920'=>'Cannot initialize the spelling engine',
              '921'=>'User dictionary cannot be loaded for editing',
              '922'=>'User dictionary cannot be found',
              '923'=>'User dictionary is read-only',
              '951'=>'An unexpected error occurred (*)',
              '954'=>'Unsupported XML grammar (*)',
              '955'=>'No database name (*)',
              '956'=>'Maximum number of database sessions exceeded (*)',
              '957'=>'Conflicting commands (*)',
              '958'=>'Parameter missing (*)',
              '959'=>'Custom Web Publishing technology is disabled',
              '960'=>'Parameter is invalid',
              '1200'=>'Generic calculation error',
              '1201'=>'Too few parameters in the function',
              '1202'=>'Too many parameters in the function',
              '1203'=>'Unexpected end of calculation',
              '1204'=>'Number, text constant, field name, or "(" expected',
              '1205'=>'Comment is not terminated with "*/"',
              '1206'=>'Text constant must end with a quotation mark',
              '1207'=>'Unbalanced parenthesis',
              '1208'=>'Operator missing, function not found, or "(" not expected',
              '1209'=>'Name (such as field name or layout name) is missing',
              '1210'=>'Plug-in function has already been registered',
              '1211'=>'List usage is not allowed in this function',
              '1212'=>'An operator (for example, +, -, *) is expected here',
              '1213'=>'This variable has already been defined in the Let function',
              '1214'=>'AVERAGE, COUNT, EXTEND, GETREPETITION, MAX, MIN, NPV, STDEV, SUM, and GETSUMMARY: expression found where a field alone is needed',
              '1215'=>'This parameter is an invalid Get function parameter',
              '1216'=>'Only summary fields are allowed as first argument in GETSUMMARY',
              '1217'=>'Break field is invalid',
              '1218'=>'Cannot evaluate the number',
              '1219'=>'A field cannot be used in its own formula',
              '1220'=>'Field type must be normal or calculated',
              '1221'=>'Data type must be number, date, time, or timestamp',
              '1222'=>'Calculation cannot be stored',
              '1223'=>'Function referred to is not yet implemented',
              '1224'=>'Function referred to does not exist',
              '1225'=>'Function referred to is not supported in this context',
              '1300'=>'The specified name can\'t be used',
              '1301'=>'A parameter of the imported or pasted function has the same name as a function in the file',
              '1400'=>'ODBC client driver initialization failed; make sure ODBC client drivers are properly installed',
              '1401'=>'Failed to allocate environment (ODBC)',
              '1402'=>'Failed to free environment (ODBC)',
              '1403'=>'Failed to disconnect (ODBC)',
              '1404'=>'Failed to allocate connection (ODBC)',
              '1405'=>'Failed to free connection (ODBC)',
              '1406'=>'Failed check for SQL API (ODBC)',
              '1407'=>'Failed to allocate statement (ODBC)',
              '1408'=>'Extended error (ODBC)',
              '1409'=>'Error (ODBC)',
              '1413'=>'Failed communication link (ODBC)',
              '1414'=>'SQL statement is too long',
              '1450'=>'Action requires PHP privilege extension (*)',
              '1451'=>'Action requires that current file be remote',
              '1501'=>'SMTP authentication failed',
              '1502'=>'Connection refused by SMTP server',
              '1503'=>'Error with SSL',
              '1504'=>'SMTP server requires the connection to be encrypted',
              '1505'=>'Specified authentication is not supported by SMTP server',
              '1506'=>'Email message(s) could not be sent successfully',
              '1507'=>'Unable to log in to the SMTP server',
              '1550'=>'Cannot load the plug-in, or the plug-in is not a valid plug-in',
              '1551'=>'Cannot install the plug-in; cannot delete an existing plug-in or write to the folder or disk',
              '1626'=>'Protocol is not supported',
              '1627'=>'Authentication failed',
              '1628'=>'There was an error with SSL',
              '1629'=>'Connection timed out; the timeout value is 60 seconds',
              '1630'=>'URL format is incorrect',
              '1631'=>'Connection failed',
              '1632'=>'Certificate cannot be authenticated by a supported certificate authority',
              '1633'=>'Certificate is valid but still causes an error (for example, the certificate has expired)',
          );
      }
      return $FM_ERROR[$error_code];
  }
}
