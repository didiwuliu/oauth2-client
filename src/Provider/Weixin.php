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

    public $responseType = 'json';

    public function urlAuthorize() {
        return 'https://open.weixin.qq.com/connect/oauth2/authorize';
    }

    public function getAuthorizationUrl($options = array("scope" => "snsapi_base")) {
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

    public function getAccessToken($grant = 'authorization_code', $params = array()) {
        if (is_string($grant)) {
            // PascalCase the grant. E.g: 'authorization_code' becomes 'AuthorizationCode'
            $className = str_replace(' ', '', ucwords(str_replace(array('-', '_'), ' ', $grant)));
            $grant = 'League\\OAuth2\\Client\\Grant\\' . $className;
            if (! class_exists($grant)) {
                throw new \InvalidArgumentException('Unknown grant "'.$grant.'"');
            }
            $grant = new $grant;
        } elseif (! $grant instanceof GrantInterface) {
            $message = get_class($grant).' is not an instance of League\OAuth2\Client\Grant\GrantInterface';
            throw new \InvalidArgumentException($message);
        }

        $defaultParams = array(
            'appid'     => $this->clientId,
            'secret' => $this->clientSecret,
            'redirect_uri'  => $this->redirectUri,
            'grant_type'    => $grant,
        );

        $requestParams = $grant->prepRequestParams($defaultParams, $params);

        try {
            switch (strtoupper($this->method)) {
                case 'GET':
                    // @codeCoverageIgnoreStart
                    // No providers included with this library use get but 3rd parties may
                    $client = $this->getHttpClient();
                    $client->setBaseUrl($this->urlAccessToken() . '?' . $this->httpBuildQuery($requestParams, '', '&'));
                    $request = $client->send();
                    $response = $request->getBody();
                    break;
                // @codeCoverageIgnoreEnd
                case 'POST':
                    $client = $this->getHttpClient();
                    $client->setBaseUrl($this->urlAccessToken());
                    $request = $client->post(null, null, $requestParams)->send();
                    $response = $request->getBody();
                    break;
                // @codeCoverageIgnoreStart
                default:
                    throw new \InvalidArgumentException('Neither GET nor POST is specified for request');
                // @codeCoverageIgnoreEnd
            }
        } catch (BadResponseException $e) {
            // @codeCoverageIgnoreStart
            $raw_response = explode("\n", $e->getResponse());
            $response = end($raw_response);
            // @codeCoverageIgnoreEnd
        }

        switch ($this->responseType) {
            case 'json':
                $result = json_decode($response, true);
                break;
            case 'string':
                parse_str($response, $result);
                break;
        }

        if (isset($result['error']) && ! empty($result['error'])) {
            // @codeCoverageIgnoreStart
            throw new IDPException($result);
            // @codeCoverageIgnoreEnd
        }

        return $grant->handleResponse($result);
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
        return $token->uid;
    }

    public function userScreenName($response, AccessToken $token) {
        return $response->nickname;
    }
}