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
class topScore extends Controller
{


    var $err = "";
    var $cek = "";
    var $content = "";
    public function index()
    {

        $querySession = sqlQuery("select * from sesion");
        $dataArraySession = array();
        
        while ($getDataSession = sqlArray($querySession)) {
             $dataArraySession[] = $getDataSession;
        }
    
    $this->content = $dataArraySession;

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


      $this->cek = "select * from top_score where id_session='".$idSession."'";

      if(sqlRowCount(sqlQuery("select * from top_score where id_session='".$idSession."'")) != 0){
        $getDataTopScore = sqlQuery("select * from top_score where id_session='".$idSession."'");


        while ($dataTopScore = sqlArray($getDataTopScore)) {

        $getDataSession = sqlArray(sqlQuery("select * from sesion where id = '".$dataTopScore['id_session']."'"));
        $getDataMember = sqlArray(sqlQuery("select * from member where id = '".$dataTopScore['id_member']."'"));

        $arrayTopScore[] =  array(
                                'id' => $dataTopScore['id'],
                                'namaUser' => $getDataMember['nama'],
                                'jumlah_point' => $dataTopScore['jumlah_point'],
                              );      
        }

        $this->content = $arrayTopScore;
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



    

}
