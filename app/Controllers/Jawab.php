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
class Jawab extends Controller
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


      $this->cek = "select * from soal where id = '$idSoal'";

      if(sqlRowCount(sqlQuery("select * from soal where id = '$idSoal'")) != 0){
        $getDataSoal = sqlArray(sqlQuery("select * from soal where id = '$idSoal'"));
        $this->content = $getDataSoal;
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

    public function RandomHuruf($length)
    {

      	    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		    $charactersLength = strlen($characters);
		    $randomString = '';
		    for ($i = 0; $i < $length; $i++) {
		        $randomString .= $characters[rand(0, $charactersLength - 1)];
		    }
			return $randomString;
    }

    public function JawabanSoal()
    {

      $postdata = file_get_contents("php://input");
      $request = json_decode($postdata);
      foreach ($request as $key => $value) {
          $$key = $value;
      }


      $this->cek = "select * from soal where id = '$idSoal'";

      if(sqlRowCount(sqlQuery("select * from soal where id = '$idSoal'")) != 0){
			$getDataSoal = sqlArray(sqlQuery("select * from soal where id = '$idSoal'"));
            $explodeJawaban = explode('.', $getDataSoal['jawaban']);
      	    $characters = $explodeJawaban['0'].$explodeJawaban['1'].$explodeJawaban['2'];
      	    $charactersLength = strlen($characters);
			$random_position = rand(0,strlen($characters)-1);
			$RandomHuruf = $this->RandomHuruf(24 - $charactersLength);
			$random_char = $RandomHuruf[rand(0,strlen($RandomHuruf)-1)];
			$randomString = str_shuffle($characters.$RandomHuruf);
			$arrayContent = array();
             $no=1;

			for ($i=0; $i <strlen($randomString) ; $i++) { 
				$arrayContent[] = array(
					'no' => "$no",
					'jawaban' => substr($randomString,$i, 1)
				);
			 $no++;
			}

        $this->content = $arrayContent;
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

    public function Prosses()
    {

      $postdata = file_get_contents("php://input");
      $request = json_decode($postdata);
      foreach ($request as $key => $value) {
          $$key = $value;
      }


 

      if(sqlRowCount(sqlQuery("select * from soal where id = '$idSoal'")) != 0){
        $getDataSoal = sqlArray(sqlQuery("select * from soal where id = '$idSoal'"));
        $getDataMember = sqlArray(sqlQuery("select * from member where id = '$idMembers'"));
        $explodeJawaban = $value1.$value2.$value3;
        $jawabanDB = str_replace('.','',$getDataSoal['jawaban']);
        if ($explodeJawaban == $jawabanDB) {
        	$arrayDataAchievement = array( 
							        		'id_soal' => $idSoal,
							        		'id_kategori' => $getDataSoal['kategori'],
							        		'point' => $getDataSoal['point'],
							        		'id_member' => $idMembers,
							        		'jam' => date('H:i'),
							        		'tanggal' => date('Y-m-d'),
							        				 
									);

        	$arrayDataMember =  array(
        								'point' =>$getDataMember['point'] + $getDataSoal['point'], 
        							 );
         
         sqlQuery(sqlInsert('achievement',$arrayDataAchievement));
         sqlQuery(sqlUpdate('member',$arrayDataMember,"id='$idMembers'"));
        	 $this->cek = sqlInsert('achievement',$arrayDataAchievement);
        	 $this->err = '1';
        }else{
        	$this->err = '0';
        }
        
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
