<?php
  
namespace App\Model\Api\Facebook;

use League\OAuth2\Client\Provider\Facebook;
use Nette\Http\Session;
use Nette\Http\SessionSection;

/**
 * Class FacebookApi
 * @package App\Model\Api\Facebook
 */
class FacebookApi{
  public string $redirectUri;
  private string $appId;
  private string $appSecret;
  private SessionSection $facebookSessionSection;


  public function __construct(string $appId, string $appSecret, Session $session){
    $this->appId=$appId;
    $this->appSecret=$appSecret;
    $this->facebookSessionSection=$session->getSection('FacebookApi');
  }

  private function getFacebook(): Facebook{
    return new Facebook([
      'clientId'=>$this->appId,
      'clientSecret'=>$this->appSecret,
      'graphApiVersion'=>'v21.0',
      'redirectUri'=>$this->redirectUri
    ]);
  }

  /**
   * Metoda pro získání adresy pro přihlášení na Facebook - vrátí nás na redirectUrl
   * @param string $redirectUrl
   * @return string
   * @throws \InvalidArgumentException
   */
  public function getLoginUrl():string {
    $facebook = $this->getFacebook();

    //vygenerujeme URL pro FB
    $url=$facebook->getAuthorizationUrl(['scope'=>['email']]);

    //uložíme do session bezpečnostní kód proti CSRF
    $this->facebookSessionSection->set('apiState',$facebook->getState());

    //vrátíme URL pro přesměrování na FB
    return $url;
  }

  /**
   * Metoda pro získání aktuálního uživatele z Facebooku
   * @param string $authorizationCode
   * @param string $facebookApiState
   * @return FacebookUser
   * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
   */
  public function getFacebookUser(string $authorizationCode, string $facebookApiState):FacebookUser {
    $facebook = $this->getFacebook();
    if (empty($facebookApiState) || ($facebookApiState!=$this->facebookSessionSection->get('apiState'))){
      throw new \Exception('Přihlášení pomocí Facebooku se nezdařilo, zkuste to prosím znovu.');
    }

    $accessToken=$facebook->getAccessToken('authorization_code',['code' => $authorizationCode]);
    if (!$accessToken){
      throw new \Exception('Přihlášení pomocí Facebooku se nezdařilo.');
    }

    //získáme údaje o uživateli FB a vygenerujeme instanci FacebookUser
    $resourceOwner = $this->getFacebook()->getResourceOwner($accessToken);
    return new FacebookUser($resourceOwner->getId(), $resourceOwner->getName(), $resourceOwner->getEmail());
  }

}