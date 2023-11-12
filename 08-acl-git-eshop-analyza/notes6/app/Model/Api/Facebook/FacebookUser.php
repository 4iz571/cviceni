<?php

namespace App\Model\Api\Facebook;

/**
 * Class FacebookUser
 * @package App\Model\Api\Facebook
 */
class FacebookUser{
  public string $facebookUserId;
  public string $name;
  public string $email;

  /**
   * FacebookUser constructor.
   * @param string $facebookUserId
   * @param string $name
   * @param string $email
   */
  public function __construct(string $facebookUserId='',string $name='',string $email=''){
    $this->facebookUserId=$facebookUserId;
    $this->name=$name;
    $this->email=$email;
  }

}