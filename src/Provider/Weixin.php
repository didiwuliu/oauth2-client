<?php
/**
 * @title weixin
 * @description
 * weixin
 * @author zhangchunsheng423@gmail.org
 * @version V1.0
 * @date 2014-07-30
 * @copyright  Copyright (c) 2014-2014 Luomor Inc. (http://www.luomor.com)
 */
namespace League\OAuth2\Client\Provider;

use League\OAuth2\Client\Token\AccessToken as AccessToken;

class Weixin extends AbstractProvider {
    public $scopes = array(
        'snsapi_base',
        'snsapi_userinfo'
    );

    public $responseType = 'string';

    public function urlAuthorize() {
        return 'https://open.weixin.qq.com/connect/oauth2/authorize';
    }

    public function getWeixinAuthorizationUrl($options = array("scope" => "snsapi_base")) {
        $this->state = md5(uniqid(rand(), true));

        $params = array(
            'appid' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'state' => $this->state,
            'scope' => isset($options['scope']) ? $options['scope'] : 'snsapi_base',
            'response_type' => isset($options['response_type']) ? $options['response_type'] : 'code',
        );

        return $this->urlAuthorize() . '?' . $this->httpBuildQuery($params, '', '&') . "#wechat_redirect";
    }

    public function urlAccessToken() {
        return 'https://api.weixin.qq.com/sns/oauth2/access_token';
    }

    public function urlUserDetails(AccessToken $token) {
        return 'https://api.weixin.qq.com/cgi-bin/user/info?' . http_build_query([
            'access_token' => $token->accessToken,
            'openid' => $this->getUserUid($token),
        ]);
    }

    public function userDetails($response, AccessToken $token) {
        $response = (array) $response;

        $user = new User;
        $uid = $this->getUserUid($token);
        $name = $response['nickname'];
        $imageUrl = (isset($response['headimgurl'])) ? $response['headimgurl'] : null;

        $user->exchangeArray(array(
            'uid' => $uid,
            'name' => $name,
            'imageurl' => $imageUrl,
        ));

        return $user;
    }

    public function getUserUid(AccessToken $token) {
        return $this->userUid($token);
    }

    public function userUid(AccessToken $token) {
        $token->uid = $token->openid;
        return $token->openid;
    }

    public function userScreenName($response, AccessToken $token) {
        return $response->nickname;
    }
}