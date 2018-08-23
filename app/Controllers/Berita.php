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
class Berita extends Controller
{


    var $err = "";
    var $cek = "";
    var $content = "";
    public function index()
    {

        $queryBerita = sqlQuery("select * from berita");
        $dataArrayBerita = array();
        
        while ($getDataBerita = sqlArray($queryBerita)) {
             $dataArrayBerita[] = $getDataBerita;
        }
    
    $this->content = $dataArrayBerita;

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


      $this->cek = "select * from berita where  id = '".$id."'";

      if(sqlRowCount(sqlQuery("select * from berita where  id = '".$id."'")) != 0){
        $getDataBerita = sqlArray(sqlQuery("select * from berita where id='$id'"));
        $this->content = $getDataBerita;
      }else{
        $this->err = "Login Gagal";
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
