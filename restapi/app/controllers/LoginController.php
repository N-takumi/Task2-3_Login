<?php

require_once '../twitteroauth-0.7.4/autoload.php';
use Phalcon\Http\Response;
use Phalcon\Http\Request;
use Abraham\TwitterOAuth\TwitterOAuth;

class LoginController extends ControllerBase
{
    public function indexAction()
    {

    }

    //ログイン処理
    public function loginAction()
    {
      //TwitterOAuth をインスタンス化
      $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
      $request_token = $connection->oauth('oauth/request_token');
      $this->session->set('oauth_token', $request_token['oauth_token']);
      $this->session->set('oauth_token_secret', $request_token['oauth_token_secret']);

      //Twitter.com 上の認証画面のURLを取得
      $url = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));

      //Twitter.com の認証画面へリダイレクト
      header( 'location: '. $url );
    }


    //コールバック処理
    public function callbackAction()
    {
      $request_token = [];
      $request_token['oauth_token'] = $this->session->get('oauth_token');
      $request_token['oauth_token_secret'] = $this->session->get('oauth_token_secret');

      //Twitterから返されたOAuthトークンと、あらかじめloginActionで入れておいたセッション上のものと一致するかをチェック
      if (isset($_REQUEST['oauth_token']) && $request_token['oauth_token'] !== $_REQUEST['oauth_token']) {
        die( 'Error!' );
      }

      if(isset($_REQUEST['oauth_verifier'])){
        //OAuth トークンも用いて TwitterOAuth をインスタンス化
        $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $request_token['oauth_token'], $request_token['oauth_token_secret']);

        $this->session->set('access_token', $connection->oauth("oauth/access_token", array("oauth_verifier" => $_REQUEST['oauth_verifier'])));

        //トークン認証用のトークン作成及び格納
        $this->session->set('login_token',hash('sha256', session_id()));
        $this->session->set('login_token_id',session_id());

        //セッションIDをリジェネレート
        session_regenerate_id();

        //マイページへリダイレクト
        header('location: http://'.$_SERVER['HTTP_HOST'].'/restapi/mypage');
      }else{
        die('oauth_verifier error!');
      }


    }


    //マイページ表示
    public function showUserPageAction()
    {
      $response = new Response();

      $access_token = $this->session->get('access_token');

      $login_token = $this->session->get('login_token');

      if($login_token == NULL){
        echo'ログイン情報なし</br>';
      }else{
        echo'ログイン済み</br>';
      }



      //OAuthトークンとシークレットも使って TwitterOAuth をインスタンス化
      $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);

      //ユーザー情報をGET
      $user = $connection->get("account/verify_credentials");

      if(isset($user->errors)){
        //エラー時の処理
        //ステータスメッセージを送る
        $response->setStatusCode(404, 'NOT FOUND');
        $response->setJsonContent(
          [
            'status' => 'Error',
            'message'=>($user->errors[0]->message),
          ],JSON_UNESCAPED_UNICODE
        );
        return $response;
      }else{
        $response->setStatusCode(200, 'OK');

        $response->setJsonContent(
          [
            'ユーザー名' => htmlspecialchars($user->name),
            '説明文'=>htmlspecialchars($user->description),
            '最新のツイート'=> htmlspecialchars($user->status->text),
            '画像url'=>htmlspecialchars($user->profile_image_url),
            'ログアウトurl'=>'http://'.$_SERVER['HTTP_HOST'].'/restapi/logout',
          ],JSON_UNESCAPED_UNICODE
        );
        return $response;
      }


    }



    //ログアウト
    public function logoutAction()
    {
      echo 'ログアウト';
      //$this->session->destroy();
      if($this->session->destroy()){
        echo'ログアウト完了';
      }else{
        echo'エラー';
      }
      header('location: http://'.$_SERVER['HTTP_HOST'].'/restapi/Login');


    }

}
