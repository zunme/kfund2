<?
require_once 'MDB2.php';

  define(DBTYPE, "mysql");
  define(DBHOST, "mdb99");
  define(DBUSER, "playauto");
  define(DBPASS, "ahffkdy;;");
  define(DBNAME, "ECHost");

/*
-------------------------------------------------
��  �⺻ ���� mysql ����, query , execute , ����, Ʈ������ ���
pear ������� ���� �Ǳ� ������ �ʿ��Ҷ�����
pear ���� �����Ǵ� �޼ҵ���� �߰��ؼ� ����ϸ��.
-------------------------------------------------
*/
class Mysql{

  var $inst_mdb2;  // MDB2 �ν��Ͻ�
  var $rownum; // fetch�� ���ڵ尹��
  var $colnum;  // fetch�� �ʵ�   ����

//--------------------------------
// ������
//--------------------------------
function mysql(){

  $argc = func_num_args();
  $argv = func_get_args();

  if ($argc == 0){
    return true;
  }
  else if ($argc == 2) {
    $inifile = $argv[0];
    $dbname  = $argv[1];
    $ini = @parse_ini_file($inifile, true);
    $hostname = $ini[$dbname][hostname];
    $user     = $ini[$dbname][user];
    $passwd   = $ini[$dbname][passwd];
  }
  else if ($argc == 5) {
    $db = $argv[0];
    $hostname = $argv[1];
    $user     = $argv[2];
    $passwd   = $argv[3];
    $dbname   = $argv[4];
  }
  else {return false;}

  $dsn = array(
  'phptype' => $db,
  'username' => $user,
  'password' => $passwd,
  'hostspec' => $hostname,
  'database' => $dbname);

  // ������ �Ϸ�
  $mdb2 =& MDB2::connect($dsn);
  if(PEAR::isError($mdb2)){die($mdb2->getMessage());}

  // �ν��Ͻ� �۷ι�ȭ
  $this->inst_mdb2	 =	$mdb2;

// function - End
}

//--------------------------------
//  ���ڵ尹�� ���� (���� �ʿ���³� �׳� Ȥ�ó��ؼ� �д�)
//--------------------------------
function get_rownum($res){
  // rownum ���ϱ�
  if ($this->inst_mdb2->getOption('result_buffering')){$this->rownum = @$res->numRows();}
else{echo "result_bufferring�� Ȱ��ȭ �Ǿ����� �ʽ��ϴ�.";}
}

//--------------------------------
//  �ʵ尹�� ����  (���� �ʿ���³� �׳� Ȥ�ó��ؼ� �д�)
//--------------------------------
function get_colnum($res){
  // rownum ���ϱ�
  if ($this->inst_mdb2->getOption('result_buffering')){$this->colnum = $res->numCols();}
else{echo "result_bufferring�� Ȱ��ȭ �Ǿ����� �ʽ��ϴ�.";}
}

//--------------------------------
//  ASSOC ��ġ
//--------------------------------
function fetch($query){

  $res = &$this->inst_mdb2->query($query);
  $i=0;
  while ($row = @$res->fetchRow(MDB2_FETCHMODE_ASSOC) ){
    $data[] = $row;
    $i++;
  }


  // rownum ����
  $this->rownum = $i;

  return $data;
}

//--------------------------------
//  Ű�� ���� ��ġ
//--------------------------------
function fetch_key($query){

  $res = &$this->inst_mdb2->query($query);
  $i=0;
  while ($row = @$res->fetchRow(MDB2_FETCHMODE_ASSOC)){

    foreach ($row as $key => $val){
      $data[$key][$i] = $val;
    // foreach - End
    }

    $i++;
  // while - End
  }

  // rownum ����
  $this->rownum = $i;

  return $data;
}


//-----------------------
//  ��  ��
//-----------------------
function execute($query){
    $affected = @$this->inst_mdb2->exec($query);
    if (PEAR::isError($affected)){die($affected->getMessage());}else{return "ok";}
}

//----------------------
// Ʈ������
//----------------------
function transaction($action,$savepoint){

  // $savepoint�� ������
  if($savepoint!=""){

    // ���̺�����Ʈ ������ ������ ���ٸ�
    if(@$this->inst_mdb2->inTransaction() && $this->inst_mdb2->supports('savepoints')){

      switch($action){

        case "begin" :
          return @$this->inst_mdb2->beginTransaction($savepoint);
        break;

        case "rollback" :
          return @$this->inst_mdb2->rollback($savepoint);
        break;

        case "commit" :
          return @$this->inst_mdb2->commit($savepoint);
        break;

      // switch - End
      }

    }
    // ������ ����Ʈ ������ ������ �ִٸ�
    else{echo "savepoint ������ ������ �ֽ��ϴ�.";return false;}
  }
  // $savepoint�� ������
  else{
    switch($action){

      case "begin" :
        return $this->inst_mdb2->beginTransaction();
      break;

      case "rollback" :
        return $this->inst_mdb2->rollback();
      break;

      case "commit" :
        return $this->inst_mdb2->commit();
      break;

    // switch - End
    }

  // else - End
  }

// function - End
}


//--------------------------------
// rownum �ʱ�ȭ
//--------------------------------
function reset_rownum(){
  unset($this->inst_mdb2->rownum);
}


//--------------------------------
// ��  ��
//--------------------------------
function close(){
  $this->inst_mdb2->disconnect();
}

// class - End
}

?>
