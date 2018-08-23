<?php
/**
 * Created by O2System Framework File Generator.
 * DateTime: 24/06/2018 13:06
 */

// ------------------------------------------------------------------------

namespace App\Controllers;

// ------------------------------------------------------------------------

use O2System\Framework\Http\Controllers\Restful as Controller;


/**
 * Class Login
 *
 * @package \App\Controllers
 */
class Soal extends Controller
{


    var $err = "";
    var $cek = "";
    var $content = "";
    public function index()
    {

      $postdata = file_get_contents("php://input");
      $request = json_decode($postdata);
      foreach ($request as $key => $value) {
          $$key = $value;
      }




      if(sqlRowCount(sqlQuery("select * from soal where kategori = '$idKategori'")) != 0){
        $getDataAchievement = sqlQuery("SELECT * from achievement where id_member ='$idMember' And id_kategori ='$idKategori'");
        while ($dataAchievement = sqlArray($getDataAchievement)) {
          $arrayAchievement[] = "id = ".$dataAchievement['id_soal']." DESC";
          $arraySuccess[] = $dataAchievement['id_soal'];
        }

        if (sizeof($arrayAchievement) == 0) {
          $getDataSoal = sqlQuery("select * from soal where kategori = '$idKategori' ORDER BY RAND()");
        }else{
          $getDataSoal = sqlQuery("select * from soal where kategori = '$idKategori' ORDER BY ".implode(',',$arrayAchievement).",RAND()");
        }


        while ($dataSoal = sqlArray($getDataSoal)) {

        $this->cek = "SELECT * from achievement where id_member ='$idMember' And id_soal = '".$dataSoal['id']."'";
        if (in_array($dataSoal['id'], $arraySuccess)) {
          $status = 'udah';
        }else{
          $status = '';
        }
        $arraySoal[] =  array(
                                'id' => $dataSoal['id'],
                                'status' => $status,
                                'energi' => $dataSoal['energi'],
                                'point' => $dataSoal['point']
                              );      
        }
        $this->content = $arraySoal;
      }else{
        $this->err = "Data Tidak Ada";
      }
      $this->sendPayload(
          [
              'request' => [
                  'method' => $_SERVER[ 'REQUEST_METHOD' ],
                  'time'   => $_SERVER[ 'REQUEST_TIME' ],
                  'uri'    => $_SERVER[ 'REQUEST_URI' ],
                  'agent'  => $_SERVER[ 'HTTP_USER_AGENT' ],
              ],
              'cek'  => $this->cek,
              'content'  => $this->content,
              'err' => $this->err
          ]
      );
    }


    public function Detail()
    {

      $postdata = file_get_contents("php://input");
      $request = json_decode($postdata);
      foreach ($request as $key => $value) {
				  $$key = $value;
			}


      $this->cek = "select count(id) as jumlahSoal,sum(point) as totalPoint,sum(energi) as totalEnergi 
      				from soal where kategori = '$idKategori'";

      if(sqlRowCount(sqlQuery("select count(id) as jumlahSoal,sum(point) as totalPoint,sum(energi) as totalEnergi 
      				from soal where kategori = '$idKategori'")) != 0){
        $getDataUser = sqlArray(sqlQuery("select count(id) as jumlahSoal,sum(point) as totalPoint,sum(energi) as totalEnergi 
        			from soal where kategori = '$idKategori'"));
    
        $this->content = $getDataUser;
      }else{
        $this->err = "Data Tidak Ada";
      }
      $this->sendPayload(
          [
              'request' => [
                  'method' => $_SERVER[ 'REQUEST_METHOD' ],
                  'time'   => $_SERVER[ 'REQUEST_TIME' ],
                  'uri'    => $_SERVER[ 'REQUEST_URI' ],
                  'agent'  => $_SERVER[ 'HTTP_USER_AGENT' ],
              ],
              'cek'  => $this->cek,
              'content'  => $this->content,
              'err' => $this->err
          ]
      );
    }


    public function clikSoal()
    {

      $postdata = file_get_contents("php://input");
      $request = json_decode($postdata);
      foreach ($request as $key => $value) {
				  $$key = $value;
			}



      
      $getDataSoal= sqlArray(sqlQuery("select * from soal where kategori = '$idKategori' and id = '$id'"));
      $getDataUser = sqlArray(sqlQuery("select * from member where id = '$idMember'"));

      if($getDataUser['energi'] >= $getDataSoal['energi']){
      	$arrayDataUpdate = array('energi' => $getDataUser['energi'] - $getDataSoal['energi']);
      	sqlQuery(sqlUpdate("member",$arrayDataUpdate,"id ='$idMember'"));
 	    $this->cek = sqlUpdate("member",$arrayDataUpdate,"id ='$idMember'");
      }else{
        $this->err = "Energi Tidak Cukup";
      }
      $this->sendPayload(
          [
              'request' => [
                  'method' => $_SERVER[ 'REQUEST_METHOD' ],
                  'time'   => $_SERVER[ 'REQUEST_TIME' ],
                  'uri'    => $_SERVER[ 'REQUEST_URI' ],
                  'agent'  => $_SERVER[ 'HTTP_USER_AGENT' ],
              ],
              'cek'  => $this->cek,
              'content'  => $this->content,
              'err' => $this->err
          ]
      );
    }

    

    

}
