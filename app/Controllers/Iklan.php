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
class Iklan extends Controller
{
    var $err = "";
    var $cek = "";
    var $content = "";

    public function adsRequest()
    {
      foreach ($_REQUEST as $key => $value) {
  				  $$key = $value;
  		}
      $dataIklan = array(
								"email" => $email,
								"tanggal" => date("Y-m-d"),
								"jam" => date("H:i"),
								"jenis_iklan" => $jenis_iklan,
								);
			sqlQuery(sqlInsert("histori_request",$dataIklan));
      $this->content = $dataIklan;
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
