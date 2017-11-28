<?php
use Store\Models\Products;
use Phalcon\Http\Response;
use Phalcon\Http\Request;

class ProductsController extends ControllerBase
{
    public function indexAction()
    {


    }


    //データの取得 全件取得
    public function getAllAction()
    {
      echo "全件取得";

      $data = [];//レスポンスデータの格納 配列

      $products = Products::find(
        ['order' => 'id',]//id順で
      );

      $response = new Response();

      if ($products === false)
      {
          $response->setStatusCode(404, 'NOT-FOUND');
          $response->setJsonContent(
            [
            'status' => 'NOT-FOUND'
            ]
          );
      }else{
          foreach ($products as $product)
          {
                $data[] = [[
                  'id'   => $product->id,
                  'name' => $product->name,
                  'description'=>$product->description,
                  'price'=> $product->price,
                  'imgFileName'=> $product->imgFileName,
                ],'imgUrl' => 'http://'.$_SERVER['HTTP_HOST'].'/restapi/products/img/'.$product->imgFileName
                ];
          }
          $response->setJsonContent(
            [
            'status' => 'FOUND',
            'data'   => $data
            ],JSON_UNESCAPED_UNICODE
          );
      }
      return $response;
    }



    //データの個別取得(IDで指定)
    public function getPieceAction()
    {
      echo'個別取得';
      $id = $this->dispatcher->getParam('int');

      $product = Products::findFirst($id);

      $response = new Response();
      if ($product === false)
      {
          $response->setStatusCode(404, 'NOT-FOUND');
          $response->setJsonContent(
            [
            'status' => 'NOT-FOUND'
            ]
          );
      } else {
          $response->setJsonContent(
            [
            'status' => 'FOUND',
            'data'   => [
              'id'   => $product->id,
              'name' => $product->name,
              'description' => $product->description,
              'price'=> $product->price,
              'imgFileName'=> $product->imgFileName
              ],
            'imgUrl' =>  'http://'.$_SERVER['HTTP_HOST'].'/restapi/products/img/'.$product->imgFileName
            ],JSON_UNESCAPED_UNICODE
          );
      }
      return $response;
    }



    //データの検索
    public function searchAction()
    {
      echo'検索処理';
      $name = $this->dispatcher->getParam('name');

      $products = Products::findFirstByName($name);

      $response = new Response();

      if ($products === false)
      {
          $response->setStatusCode(404, 'NOT-FOUND');
          $response->setJsonContent(
            [
            'status' => 'NOT-FOUND'
            ]
          );
      } else {
          $response->setStatusCode(200, 'OK');
          $response->setJsonContent(
            [
            'status' => 'FOUND',
            'data'   => [
                'id'   => $products->id,
                'name' => $products->name,
                'description'=> $products->description,
                'price'=> $products->price,
                'imgFileName'=> $products->imgFileName
              ],
            'imgUrl' =>  'http://'.$_SERVER['HTTP_HOST'].'/restapi/products/img/'.$product->imgFileName
              ],JSON_UNESCAPED_UNICODE
          );
      }
      return $response;
    }



    //データの挿入
    public function addAction()
    {
      echo'データの挿入</br>';

      $response = new Response();

      if ($this->request->isPost())
      {
        $result = $this->request->getJsonRawBody();
        //echo(var_dump($result));
        $product = new Products();
        $product->name = $result->name;
        $product->description = $result->description;
        $product->price = $result->price;
        $product->imgFileName = $result->imgFileName;
        $product->save();
      }else{
        $response->setStatusCode(400, 'Bad Request');
        return $response;
      }

      if($product->save() == true)
      {
        $response->setStatusCode(201, 'Created');
        $response->setJsonContent(
          [
            'status' => 'OK',
            'data'   => $result,
          ],JSON_UNESCAPED_UNICODE
        );
      }else{
        $response->setStatusCode(409, 'Conflict');
        $errors = [];

        foreach ($product->getMessages() as $message){
          $errors[] = $message->getMessage();
        }

        $response->setJsonContent(
          [
            'status'   => 'ERROR',
            'messages' => $errors,
          ]
        );
      }
      return $response;
    }



    //データの変更(更新)
    public function updateAction()
    {
      echo'データの変更';
      $id = $this->dispatcher->getParam('int');
      echo($id);

      $response = new Response();

      $product = Products::findFirst($id);

      if($product === false)
      {
        $response->setStatusCode(404, 'NOT-FOUND');
        $response->setJsonContent(
          [
          'status' => 'NOT-FOUND'
          ]
        );
        return $response;
      }else{
        $result = $this->request->getJsonRawBody();
        $product->name = $result->name;
        $product->description = $result->description;
        $product->price = $result->price;
        $product->imgFileName = $result->imgFileName;
        $product->update();
      }

      if($product->save() == true)
      {
        $response->setStatusCode(200, 'OK');
        $response->setJsonContent(
          [
            'status' => 'OK',
            'data'   => $result,
          ],JSON_UNESCAPED_UNICODE
        );
      }else{
        $response->setStatusCode(409, 'Conflict');
        $errors = [];

        foreach ($product->getMessages() as $message){
          $errors[] = $message->getMessage();
        }
        $response->setJsonContent(
          [
            'status'   => 'ERROR',
            'messages' => $errors,
          ]
        );
      }
      return $response;
    }



    //データの削除
    public function deleteAction()
    {
      echo'データの削除';
      $id = $this->dispatcher->getParam('int');
      echo($id);

      $response = new Response();

      $product = Products::findFirst($id);
      if($product === false)
      {
        $response->setStatusCode(404, 'NOT-FOUND');
        $response->setJsonContent(
          [
          'status' => 'NOT-FOUND'
          ]
        );
        return $response;
      }else{
        $product->delete();
      }

      if($product->delete() == true)
      {
        $response->setStatusCode(200, 'OK');
        $response->setJsonContent(
          [
            'status' => 'DELETED',
            'data'   => $product,
          ],JSON_UNESCAPED_UNICODE
        );
      }else{
        $response->setStatusCode(409, 'Conflict');
        $errors = [];

        foreach ($product->getMessages() as $message)
        {
          $errors[] = $message->getMessage();
        }

        $response->setJsonContent(
          [
            'status'   => 'ERROR',
            'messages' => $errors,
          ]
        );
      }
      return $response;
    }

}
