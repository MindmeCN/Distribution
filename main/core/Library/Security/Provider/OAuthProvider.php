<?php

/*
 * This file is part of the FOSOAuthServerBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Claroline\CoreBundle\Library\Security\Provider;

use FOS\OAuthServerBundle\Security\Authentication\Token\OAuthToken;
use OAuth2\OAuth2;
use OAuth2\OAuth2AuthenticateException;
use OAuth2\OAuth2ServerException;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * OAuthProvider class.
 * Copied from FOS\OAuthServerBundle\Security\Authentication\Provider
 * class is defined in ClarolineCoreBundle/Resources/config/service.yml.
 *
 * @author  Arnaud Le Blanc <arnaud.lb@gmail.com>
 */
class OAuthProvider implements AuthenticationProviderInterface
{
    /**
     * @var UserProviderInterface
     */
    protected $userProvider;
    /**
     * @var OAuth2
     */
    protected $serverService;
    /**
     * @var UserCheckerInterface
     */
    protected $userChecker;

    /**
     * @param UserProviderInterface $userProvider  the user provider
     * @param OAuth2                $serverService the OAuth2 server service
     * @param UserCheckerInterface  $userChecker   The Symfony User Checker for Pre and Post auth checks
     */
    public function __construct(UserProviderInterface $userProvider, OAuth2 $serverService, UserCheckerInterface $userChecker)
    {
        $this->userProvider = $userProvider;
        $this->serverService = $serverService;
        $this->userChecker = $userChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(TokenInterface $token)
    {
        if (!$this->supports($token)) {
            return;
        }
        try {
            $tokenString = $token->getToken();

            if ($accessToken = $this->serverService->verifyAccessToken($tokenString)) {
                $scope = $accessToken->getScope();
                $user = $accessToken->getUser();

                if (null !== $user) {
                    try {
                        $this->userChecker->checkPreAuth($user);
                    } catch (AccountStatusException $e) {
                        throw new OAuth2AuthenticateException(OAuth2::HTTP_UNAUTHORIZED,
                            OAuth2::TOKEN_TYPE_BEARER,
                            $this->serverService->getVariable(OAuth2::CONFIG_WWW_REALM),
                            'access_denied',
                            $e->getMessage()
                        );
                    }

                    $token->setUser($user);
                }

                $roles = (null !== $user) ? $user->getRoles() : [];

                /*
                 * This is the only modification from the base class.
                 * We only add scopes if we're not connected as user.
                 * Otherwise, if we support the scope admin, everyone will be admin if no scope are requested because fos-oauth2-lib
                 * doesn't support different scope by clients (https://github.com/FriendsOfSymfony/FOSOAuthServerBundle/issues/201)
                 * This way, we can bypass this by creating 2 clients: 1 wich grant the password (and refresh) types
                 * (and will require a user authentication)
                 * One that grant pretty much all the rest.
                 */
                if (!$user) {
                    if (!empty($scope)) {
                        foreach (explode(' ', $scope) as $role) {
                            $roles[] = 'ROLE_'.strtoupper($role);
                        }
                    }
                }

                $roles = array_unique($roles, SORT_REGULAR);

                $token = new OAuthToken($roles);
                $token->setAuthenticated(true);
                $token->setToken($tokenString);

                if (null !== $user) {
                    try {
                        $this->userChecker->checkPostAuth($user);
                    } catch (AccountStatusException $e) {
                        throw new OAuth2AuthenticateException(OAuth2::HTTP_UNAUTHORIZED,
                            OAuth2::TOKEN_TYPE_BEARER,
                            $this->serverService->getVariable(OAuth2::CONFIG_WWW_REALM),
                            'access_denied',
                            $e->getMessage()
                        );
                    }

                    $token->setUser($user);
                }

                return $token;
            }
        } catch (OAuth2ServerException $e) {
            if (!method_exists('Symfony\Component\Security\Core\Exception\AuthenticationException', 'setToken')) {
                // Symfony 2.1
                throw new AuthenticationException('OAuth2 authentication failed', null, 0, $e);
            }

            throw new AuthenticationException('OAuth2 authentication failed', 0, $e);
        }

        throw new AuthenticationException('OAuth2 authentication failed');
    }

    /**
     * {@inheritdoc}
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof OAuthToken;
    }
}
