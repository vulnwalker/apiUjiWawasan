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
class IdUnit extends Controller
{


    var $err = "";
    var $cek = "";
    var $content = "";
    public function Video()
    {

    $queryIdUnit = sqlQuery("select * from type_iklan where id = '3' ");
		$dataArrayIdUnit = array();
        
        while ($getDataIdUnit = sqlArray($queryIdUnit)) {
             $dataArrayIdUnit[] = $getDataIdUnit;
        }
		
		$this->content = $dataArrayIdUnit;

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

    public function Banner()
    {

    $queryIdUnit = sqlQuery("select * from type_iklan where id = '1' ");
    $dataArrayIdUnit = array();
        
        while ($getDataIdUnit = sqlArray($queryIdUnit)) {
             $dataArrayIdUnit[] = $getDataIdUnit;
        }
    
    $this->content = $dataArrayIdUnit;

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


    public function PopUp()
    {

    $queryIdUnit = sqlQuery("select * from type_iklan where id = '4' ");
    $dataArrayIdUnit = array();
        
        while ($getDataIdUnit = sqlArray($queryIdUnit)) {
             $dataArrayIdUnit[] = $getDataIdUnit;
        }
    
    $this->content = $dataArrayIdUnit;

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
