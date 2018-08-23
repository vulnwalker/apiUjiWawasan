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
class Member extends Controller
{
    var $err = "";
    var $cek = "";
    var $content = "";

    public function register()
    {
      foreach ($_REQUEST as $key => $value) {
  				  $$key = $value;
  		}
      if(empty($nama)){
					$this->err = "Isi Nama Lengkap";
			}elseif(empty($email)){
				$this->err = "Isi Email";
			}elseif(empty($password)){
				$this->err = "Isi Password";
			}elseif(filter_var($email, FILTER_VALIDATE_EMAIL) == false){
				$this->err = "Email Tidak Valid";
			}elseif(empty($no_telepon)){
				$this->err = "Isi Nomor Telepon";
			}elseif(sqlRowCount(sqlQuery("select * from member where email = '$email'")) != 0){
				$this->err = "Email sudah terdaftar";
			}elseif(sqlRowCount(sqlQuery("select * from member where device_name = '$deviceName' and imei = '$imei'")) != 0){
				$this->err = "Perangkat sudah terdaftar di server kami !";
			}else{
				$data = array(
											"email" => $email,
											"nama_lengkap" => $nama,
											"password" => $password,
											"no_telepon" => $no_telepon,
											"saldo" => "0",
											"verifikasi" => "belum",
											"status" => "registered",
											"device_name" => $deviceName,
											"imei" => $imei
										 );
				 $execute =	sqlQuery(sqlInsert("member",$data));
				 if($execute){
					 $this->cek = "Register Berhasil";
				 }else{
					 $this->cek = "Register Gagal Mungkin Email Telah Terdaftar";
				 }
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
