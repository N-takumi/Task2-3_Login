<?php
use Store\Models\Products;
use Phalcon\Http\Response;
use Phalcon\Http\Request;
class ImgsController extends ControllerBase
{
    public function indexAction()
    {

    }


        //画像保存
        public function uploadImgAction()
        {
          $response = new Response();
            // Check if the user has uploaded files
          if ($this->request->hasFiles())
          {
              $files = $this->request->getUploadedFiles();
              // Print the real file names and sizes
              foreach ($files as $file) {
                // Print file details
                echo $file->getName(), ' ', $file->getSize(), '\n';
                echo $file->getTempName();

                //アップロードファイルの拡張子チェック
                if (!$ext = array_search(
                    mime_content_type($file->getTempName()),
                    array(
                        'gif' => 'image/gif',
                        'jpg' => 'image/jpeg',
                        'png' => 'image/png',
                    ),
                    true
                )){
                  $response->setStatusCode(400, 'Bad Request');
                  $response->setJsonContent(
                    [
                      'status' => 'Bad Request',
                      'message'=>$file->getType().'は認められないファイル形式です',
                    ],JSON_UNESCAPED_UNICODE
                  );
                  return $response;
                }


                if($file->moveTo('./img/' . $file->getName()) === false)
                {
                  $response->setStatusCode(409, 'Conflict');
                }else{
                  $response->setStatusCode(201, 'Created');
                  $response->setJsonContent(
                    [
                      'status' => 'OK',
                      'fileSize'=>$file->getType(),
                      'imgUrl'   => 'http://'.$_SERVER['HTTP_HOST'].'/restapi/products/img/'.$file->getName(),
                    ],JSON_UNESCAPED_UNICODE
                  );
                }

              }
          }else{
            $response->setStatusCode(400, 'Bad Request');
          }
            //$response->send();
            return $response;
        }



        //画像表示
        public function showImgAction()
        {
          $name = $this->dispatcher->getParam('name');
          echo'画像表示</br>';

          $response = new Response();

          if(file_exists('img/'.$name) === false)
          {
            $response->setStatusCode(404, 'NOT-FOUND');
            echo $name.'は現在登録されていません</br>';
          }else{
            $response->setStatusCode(200, 'OK');
            echo '画像あり</br>';
          }
          $response->setContent($this->tag->image('img/'.$name));
          $response->send();
        }



}
