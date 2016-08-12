<?php

namespace UserBundle\Controller;

use HWI\Bundle\OAuthBundle\Controller\ConnectController as BaseConnectController;
use HWI\Bundle\OAuthBundle\Security\Core\Exception\AccountNotLinkedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use UserBundle\Security\Core\Authentication\Token\OAuthToken;

class ConnectController extends BaseConnectController
{
    public function registrationAction(Request $request, $key)
    {
        $connect = $this->getParameter('hwi_oauth.connect');
        if (!$connect) {
            throw $this->createNotFoundException();
        }

        $hasUser = $this->isGranted('IS_AUTHENTICATED_REMEMBERED');
        if ($hasUser) {
            $this->addFlash('info', 'Votre compte est déjà enregistré !');
            return $this->redirect($this->generateUrl('homepage'));
        }

        $session = $request->getSession();
        $error = $session->get('_hwi_oauth.registration_error.'.$key);

        if (!$error instanceof AccountNotLinkedException || time() - $key > 300) {
            throw new \Exception('Nous ne sommes pas en mesure d\'enregistrer votre compte... Veuillez-nous excuser.');
        }

        $resourceOwnerName = $error->getResourceOwnerName();
        $rawToken = $error->getRawToken();
        $userInformation = $this->getResourceOwnerByName($resourceOwnerName)->getUserInformation($rawToken);

        $formFactory = $this->get('fos_user.registration.form.factory');
        $userManager = $this->get('fos_user.user_manager');

        $user = $userManager->createUser();
        $user->setEnabled(true);
        $user->setUsername($userInformation->getRealName());
        $user->setEmail($userInformation->getEmail());

        $setter = 'set' . ucfirst($resourceOwnerName) . 'Id';
        $user->$setter($userInformation->getUsername());

        $form = $formFactory->createForm();
        $form->setData($user);

        if($form->handleRequest($request)->isValid()){
            $userManager->updateUser($user);
            $this->authenticateUser($request, $user, $resourceOwnerName, $rawToken);
            $session->remove('_hwi_oauth.registration_error.'.$key);

            return $this->redirect($this->generateUrl('fos_user_registration_confirmed'));
        }

        return $this->render('FOSUserBundle:Registration:register.html.twig', array(
            'form' => $form->createView(),
            'dataFromSocial' => true
        ));
    }

    public function connectServiceAction(Request $request, $service)
    {
        $connect = $this->getParameter('hwi_oauth.connect');
        if (!$connect) {
            throw $this->createNotFoundException();
        }

        $hasUser = $this->isGranted('IS_AUTHENTICATED_REMEMBERED');
        if (!$hasUser) {
            throw $this->createAccessDeniedException('Désolé, une erreur est survenue lors de la connexion.');
        }

        $resourceOwner = $this->getResourceOwnerByName($service);
        $accessToken = null;

        if ($resourceOwner->handles($request)) {
            $accessToken = $resourceOwner->getAccessToken(
                $request,
                $this->get('hwi_oauth.security.oauth_utils')->getServiceAuthUrl($request, $resourceOwner)
            );
        }

        if (null === $accessToken) {
            return $this->redirectToRoute($this->getParameter('hwi_oauth.failed_auth_path'));
        }

        $currentToken = $this->getToken();
        $currentUser = $currentToken->getUser();
        $userInformation = $resourceOwner->getUserInformation($accessToken);

        $this->get('hwi_oauth.account.connector')->connect($currentUser, $userInformation);
        $this->authenticateUser($request, $currentUser, $service, $accessToken, false);

        return $this->redirect($this->generateUrl('homepage'));
    }

    /**
     * Authenticate a user with Symfony Security.
     *
     * @param Request       $request
     * @param UserInterface $user
     * @param string        $resourceOwnerName
     * @param string        $accessToken
     * @param bool          $fakeLogin
     */
    protected function authenticateUser
    (
        Request $request,
        UserInterface $user,
        $resourceOwnerName,
        $accessToken,
        $fakeLogin = true
    )
    {
        try {
            $this->get('hwi_oauth.user_checker')->checkPreAuth($user);
            $this->get('hwi_oauth.user_checker')->checkPostAuth($user);
        } catch (AccountStatusException $e) {
            // Don't authenticate locked, disabled or expired users
            return;
        }

        $providerKey = $this->getProviderKey($request->getSession());

        $token = new OAuthToken($accessToken, $user->getRoles(), $providerKey);
        $token->setResourceOwnerName($resourceOwnerName);
        $token->setUser($user);
        $token->setAuthenticated(true);

        $this->setToken($token);

        if ($fakeLogin) {
            $this->get('event_dispatcher')->dispatch(
                SecurityEvents::INTERACTIVE_LOGIN,
                new InteractiveLoginEvent($request, $token)
            );
        }
    }

    /**
     * @param SessionInterface $session
     *
     * @return string|null
     */
    private function getProviderKey(SessionInterface $session)
    {
        foreach ($this->getParameter('hwi_oauth.firewall_names') as $providerKey) {
            $sessionKey = '_security.'.$providerKey.'.target_path';

            if ($session->has($sessionKey)) {
                return $providerKey;
            }
        }

        return null;
    }
}