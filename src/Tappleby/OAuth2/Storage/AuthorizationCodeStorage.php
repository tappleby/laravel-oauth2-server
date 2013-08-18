<?php


namespace Tappleby\OAuth2\Storage;


class AuthorizationCodeStorage implements \OAuth2_Storage_AuthorizationCodeInterface {

  /** @var \Tappleby\OAuth2\Repositories\AuthorizationCodeRepositoryInterface */
  protected $repo;


  function __construct($repo)
  {
    $this->repo = $repo;
  }

  public function getAuthorizationCode($code)
  {
    $retData = null;

    $code = $this->repo->find($code);

    if($code) {
      $scope = null;

      if(is_array($code->getScopes())) {
        $scope = implode(' ', $code->getScopes());
      }

      $retData = array(
        'client_id' => $code->getClientId(),
        'expires' => $code->getExpires(),
        'redirect_uri' => $code->getRedirectUri(),
        'scope' => $scope
      );
    }

    return $retData;
  }

  public function setAuthorizationCode($code, $client_id, $user_id, $redirect_uri, $expires, $scope = null)
  {
    if($scope) {
      $scope = explode(' ', $scope);
    }

    $code = $this->repo->create(array(
      'id' => $code,
      'client_id' => $client_id,
      'user_id' => $user_id,
      'redirect_uri' => $redirect_uri,
      'expires' => $expires,
      'scope' => $scope
    ));

    return $code;
  }

  public function expireAuthorizationCode($code)
  {
    return $this->repo->delete($code);
  }

}