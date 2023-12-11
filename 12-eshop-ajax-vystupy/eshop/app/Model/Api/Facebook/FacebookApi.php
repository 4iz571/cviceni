<?php
  
namespace App\Model\Api\Facebook;

use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;

/**
 * Class FacebookApi
 * @package App\Model\Api\Facebook
 */
class FacebookApi{
  private string $appId;
  private string $appSecret;
  private Facebook $facebook;


  public function __construct(string $appId, string $appSecret){
    $this->appId=$appId;
    $this->appSecret=$appSecret;
  }

  /**
   * Metoda vracející instanci Facebook SDK
   * @return Facebook
   * @throws FacebookSDKException
   */
  public function getFacebook():Facebook{
    if (empty($this->facebook)){
      $this->facebook=new Facebook([
        'app_id'=>$this->appId,
        'app_secret'=>$this->appSecret
      ]);
    }
    return $this->facebook;
  }

  /**
   * Metoda pro získání adresy pro přihlášení na Facebook - vrátí nás na redirectUrl
   * @param string $redirectUrl
   * @return string
   * @throws FacebookSDKException
   */
  public function getLoginUrl(string $redirectUrl):string {
    //inicializujeme helper pro vytvoření odkazu
    $redirectLoginHelper=$this->getFacebook()->getRedirectLoginHelper();
    //necháme vygenerovat odkaz
    return $redirectLoginHelper->getLoginUrl($redirectUrl, ['email']);
  }

  /**
   * Metoda pro získání aktuálního uživatele z Facebooku
   * @return FacebookUser
   * @throws FacebookSDKException
   * @throws \Exception
   */
  public function getFacebookUser():FacebookUser {
    //inicializujeme helper pro vytvoření odkazu
    $redirectLoginHelper=$this->getFacebook()->getRedirectLoginHelper();
    //získáme access token z aktuálního přihlášení (když se jej nepovede získat, je vyhozena výjimka)
    $accessToken = $redirectLoginHelper->getAccessToken();

    if (!$accessToken){
      throw new \Exception('Přihlášení pomocí Facebooku se nezdařilo.');
    }

    //OAuth 2.0 client pro správu access tokenů
    $oAuth2Client = $this->getFacebook()->getOAuth2Client();
    //získáme údaje k tokenu, který jsme získali z přihlášení
    $accessTokenMetadata = $oAuth2Client->debugToken($accessToken);

    //získáme ID uživatele z Facebooku a začneme vytvářet odpověď
    $facebookUser = new FacebookUser($accessTokenMetadata->getUserId());

    //získáme jméno a e-mail uživatele
    $response=$this->getFacebook()->get('/me?fields=name,email', $accessToken);
    $graphUser=$response->getGraphUser();

    $facebookUser->email=$graphUser->getEmail();
    $facebookUser->name=$graphUser->getName();

    return $facebookUser;
  }

}