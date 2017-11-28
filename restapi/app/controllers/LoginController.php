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
      //echo "login!!";

      //TwitterOAuth をインスタンス化
      $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);

      //コールバックURLをここでセット
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

        //セッションIDをリジェネレート
        session_regenerate_id();

        //マイページへリダイレクト
        header('location: http://localhost/restapi/mypage');
      }else{
        die('oauth_verifier error!');
      }


    }


    //マイページ表示
    public function showUserPageAction()
    {
      $response = new Response();

      $access_token = $this->session->get('access_token');

      //var_dump($access_token);


      //OAuthトークンとシークレットも使って TwitterOAuth をインスタンス化
      $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);

      //ユーザー情報をGET
      $user = $connection->get("account/verify_credentials");

      if(isset($user->errors)){
        echo($user->errors[0]->message);
      }else{

              echo 'マイページ</br>';
              echo('　ユーザー名:'.htmlspecialchars($user->name).'</br>');
              echo('　説明文:'.$user->description.'</br>');
              echo('　最新のツイート:'.$user->status->text.'</br>');
              echo('　画像:</br>');


              $response->setContent($this->tag->image($user->profile_image_url)
                                   .$this->tag->linkTo("logout", "LOGOUT"));
              $response->send();
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

      header('location: http://localhost/restapi/login');

    }

}
