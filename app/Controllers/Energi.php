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
class Energi extends Controller
{


    var $err = "";
    var $cek = "";
    var $content = "";
    public function Video()
    {

        $queryVideo = sqlQuery("select * from type_iklan where id='3'");
		$dataArrayVideo = array();
        
        while ($getDataVideo = sqlArray($queryVideo)) {
             $dataArrayVideo[] = $getDataVideo;
        }
		
		$this->content = $dataArrayVideo;

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


    public function VideoChange()
    {

      $postdata = file_get_contents("php://input");
      $request = json_decode($postdata);
      foreach ($request as $key => $value) {
          $$key = $value;
      }
      $getDataTypeIklan = sqlArray(sqlQuery("SELECT * FROM  type_iklan where id ='3'"));
      $getMaxId = sqlArray(sqlQuery("SELECT max(id) as maxId FROM  charge_energi where id_member ='$IdUser'"));
      $getDataEnergi= sqlArray(sqlQuery("SELECT * FROM  charge_energi where id ='".$getMaxId['maxId']."'"));
      
      $selisiWaktu = $this->convertTimeToInteger(date("Y-m-d").";".date("H:i")) - $this->convertTimeToInteger($getDataEnergi['tanggal'].";".$getDataEnergi['jam']);
      if($selisiWaktu >  $getDataTypeIklan['delay'] ){

      $getDataMember= sqlArray(sqlQuery("SELECT * FROM  member where id ='".$IdUser."'"));
      $arrayEnergi =  array(
        'id_member' => $IdUser,
        'tanggal' => date('Y-m-d'),
        'jam' => date('H:i'),
        'type_iklan' => '3',
      );
      
      $explodeEnergi = explode(',',$getDataTypeIklan['energi']);
      $arrayMember =  array(
        'energi' => $getDataMember['energi']+rand($explodeEnergi[0],$explodeEnergi[1]),
      );

      $queryEnergi = sqlInsert("charge_energi",$arrayEnergi);
      sqlQuery($queryEnergi); 
      sqlQuery(sqlUpdate('member', $arrayMember,"id='$IdUser'"));
      }else{
        $this->err = "Maaf anda harus menunggu selama ".$getDataTypeIklan['delay']." menit";
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


    function convertTimeToInteger($concatTanggalJam){
      $explodeTanggalJamConcat = explode(";",$concatTanggalJam);
      $integerTanggal = $this->dateToInteger($this->generateDate($explodeTanggalJamConcat[0]));
      $integerJam = $this->timeToInteger($explodeTanggalJamConcat[1]);
      $integerValue = $integerTanggal + $integerJam;
      return $integerValue;
    }
    function dateToInteger($date){
      $explodeTanggal = explode("-",$date);
      $hari = $explodeTanggal[2] * (24 * 60 ) ;
      $bulan = $explodeTanggal[1] * (30 * (24 * 60) ) ;
      $tahun = $explodeTanggal[0] * (365 * (30 * (24 * 60) ) ) ;
      $integerValue = $tahun + $bulan + $hari;
      return $integerValue;
    }
    function timeToInteger($time){
      $explodeJam = explode(":",$time);
      $jam = $explodeJam[0] * 60;
      $menit = $explodeJam[1] ;
      $integerValue = $jam + $menit;
      return $integerValue;
    }

    function generateDate($tanggal){
          $tanggal = explode('-',$tanggal);
          $tanggal = $tanggal[2]."-".$tanggal[1]."-".$tanggal[0];
          return $tanggal;
    }
    

}
