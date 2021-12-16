<?php

namespace App\Model\Api\Facebook;

/**
 * Class FacebookUser
 * @package App\Model\Api\Facebook
 */
class FacebookUser{
  /** @var string $facebookUserId */
  public $facebookUserId;
  /** @var string $name */
  public $name;
  /** @var string $email */
  public $email;

  /**
   * FacebookUser constructor.
   * @param string $facebookUserId
   * @param string $name
   * @param string $email
   */
  public function __construct(string $facebookUserId='',string $name='',string $email=''){
    $this->facebookUserId=$facebookUserId;
  }

}