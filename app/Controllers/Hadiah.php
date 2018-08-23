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
class Hadiah extends Controller
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

    public function All()
    {

      $postdata = file_get_contents("php://input");
      $request = json_decode($postdata);
      foreach ($request as $key => $value) {
				  $$key = $value;
			}


      $this->cek = "select * from hadiah where  id_session = '".$IdHadiah."'";
      $dataArraySession = array();

      if(sqlRowCount(sqlQuery("select * from hadiah where  id_session = '".$IdHadiah."'")) != 0){
        $queryHadiah = sqlQuery("select * from hadiah where id_session='$IdHadiah'");
        while ($getDataHadiah = sqlArray($queryHadiah)) {
             $dataArrayHadiah[] = $getDataHadiah;
        }

        $this->content = $dataArrayHadiah;
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


      $this->cek = "select * from hadiah where id = '".$idHadiah."'";

      if(sqlRowCount(sqlQuery("select * from hadiah where id = '".$idHadiah."'")) != 0){
        $getDataDetailHadiah = sqlArray(sqlQuery("select * from hadiah where id = '".$idHadiah."'"));
        $this->content = $getDataDetailHadiah;
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
