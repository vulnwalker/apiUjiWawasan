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
class Achievement extends Controller
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


      $this->cek = "select * from achievement where id_member = '".$IdUser."'";

      if(sqlRowCount(sqlQuery("select * from achievement where id_member = '".$IdUser."'")) != 0){
        $getDataAchievement = sqlQuery("select * from achievement where id_member = '".$IdUser."'");


        while ($dataAchievement = sqlArray($getDataAchievement)) {

        $getDataSoal = sqlArray(sqlQuery("select * from soal where id = '".$dataAchievement['id_soal']."'"));
        $getDataKategori = sqlArray(sqlQuery("select * from kategori where id = '".$dataAchievement['id_kategori']."'"));

        $arrayHistorySoal[] =  array(
                                'id' => $dataAchievement['id'],
                                'judulSoal' => $getDataSoal['pertanyaan'],
                                'JudulKategori' => $getDataKategori['nama_kategori'],
                                'tanggal' => $dataAchievement['tanggal'],
                                'jam' => $dataAchievement['jam'],
                                'point' => $dataAchievement['point']
                              );      
        }

        $this->content = $arrayHistorySoal;
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


      $this->cek = "select * from achievement where id = '".$idSoal."'";

      if(sqlRowCount(sqlQuery("select * from achievement where id = '".$idSoal."'")) != 0){
        $getDataDetailSoal = sqlArray(sqlQuery("select * from achievement where id = '".$idSoal."'"));
        $this->content = $getDataDetailSoal;
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
