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
class Auth extends Controller
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


      $this->cek = "select * from member where email = '$email' and password = '".$password."' and idhp = '".$IDHP."'";

      if(sqlRowCount(sqlQuery("select * from member where email = '$email' and password = '".$password."'")) != 0){
        $getDataUser = sqlArray(sqlQuery("select * from member where email='$email'"));
      	if ($IDHP == $getDataUser['idhp']) {
      		$this->content = $getDataUser;
      	}else{
      	   $this->err = "Tuyul";
      	}


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

    public function register()
    {

      $postdata = file_get_contents("php://input");
      $request = json_decode($postdata);
      foreach ($request as $key => $value) {
				  $$key = $value;
			}

		$arrayRegister =  array(
			'nama' => $nama,
			'email' => $email,
			'password' => $password,
			'alamat' => $alamat,
			'no_telpn' => $phone,
			'idhp' => $IDHP,
		);



      if(sqlRowCount(sqlQuery("select * from member where email = '$email'")) == 0){
		  $queryRegister = sqlInsert("member",$arrayRegister);
		  sqlQuery($queryRegister); 
		  $getDataUser = sqlArray(sqlQuery("select * from member where email='$email'"));
		  $this->content = $getDataUser;
      }else{
        $this->err = "Register Gagal";
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

    public function sosialFb()
    {

      $postdata = file_get_contents("php://input");
      $request = json_decode($postdata);
      foreach ($request as $key => $value) {
				  $$key = $value;
			}

		$arrayRegister =  array(
			'nama' => $nama,
			'email' => $email,
		);

      $this->cek = sqlInsert("member",$arrayRegister);

      if(sqlRowCount(sqlQuery("select * from member where email = '$email' and idhp = '$IDHP'")) == 0){
		  $this->err = "Register Lanjut ";
      }else{
        $getDataUser = sqlArray(sqlQuery("select * from member where email='$email'"));
        $this->content = $getDataUser;
        $this->err = "";
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

    public function SosialFbStep2()
    {

      $postdata = file_get_contents("php://input");
      $request = json_decode($postdata);
      foreach ($request as $key => $value) {
				  $$key = $value;
			}

		$arrayRegister =  array(
			'nama' => $nama,
			'email' => $email,
			'alamat' => $alamat,
			'no_telpn' => $phone,
			'idhp'=>$IDHP
		);


      $this->cek = sqlInsert("member",$arrayRegister);

      if(sqlRowCount(sqlQuery("select * from member where email = '$email' and idhp = '$IDHP'")) == 0){
		  $queryRegister = sqlInsert("member",$arrayRegister);
		  sqlQuery($queryRegister); 
      $getDataUser = sqlArray(sqlQuery("select * from member where email='$email'"));
      $this->content = $getDataUser;
      }else{
        $this->err = "Register Gagal";
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


   public function dataUser(){
    	      $postdata = file_get_contents("php://input");
      $request = json_decode($postdata);
      foreach ($request as $key => $value) {
				  $$key = $value;
			}


      $this->cek = "select * from member where id = '$IdUser'";

      if(sqlRowCount(sqlQuery("select * from member where id = '$IdUser'")) != 0){
        $getDataUser = sqlArray(sqlQuery("select * from member where id = '$IdUser'"));
        $this->content = $getDataUser;
      }else{
        $this->err = "User Tidak ada";
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
