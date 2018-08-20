<?php

namespace AppBundle\Utils\Newsletter;

use AppBundle\Entity\Newsletter;
use AppBundle\Entity\Post;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;

class NewsletterManager implements NewsletterManagerInterface
{
    /**
     * @var ContactProviderInterface
     */
    private $contactProvider;

    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * @var string
     */
    private $from;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var EmailSenderInterface
     */
    private $emailSender;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var boolean
     */
    private $isSuperAdmin;

    /**
     * Constructor
     */
    public function __construct(
        ContactProviderInterface $contactProvider,
        EngineInterface $templating,
        $from,
        TranslatorInterface $translator,
        EmailSenderInterface $emailSender,
        LoggerInterface $logger
    )
    {
        $this->contactProvider = $contactProvider;
        $this->templating = $templating;
        $this->from = $from;
        $this->translator = $translator;
        $this->emailSender = $emailSender;
        $this->logger = $logger;
        $this->isSuperAdmin = false;
    }

    public function setIsSuperAdmin($isSuperAdmin)
    {
        $this->isSuperAdmin = $isSuperAdmin;
        $this->contactProvider->setIsSuperAdmin($isSuperAdmin);
    }

    /**
     * {@inheritdoc}
     */
    public function share(Post $post)
    {
        if(!$post->getPublished()){
            return null;
        }

        $contacts = $this->contactProvider->getContacts();
        $now = new \DateTime();
        $from = $this->from;
        $subject = $this->getSubject($now);

        foreach($contacts as $contact){
            if(!$contact instanceof ContactInterface){
                throw new \InvalidArgumentException(sprintf("%s must implement %s.", get_class($contact), ContactInterface::class));
            }

            $template = $this->templating->render('@App/Admin/Post/newsletter.html.twig', array(
                'post' => $post,
                'contact' => $contact
            ));
            $to = array($contact->getEmail());

            try {
                if($this->emailSender->send($from, $to, $subject, $template) && !$this->isSuperAdmin){
                    $post->setSharedNewsletter($now);
                }
            }catch (\Exception $e) {
                $this->logger->error(sprintf('Newsletter not sent to %s', $contact->getEmail()), array(
                    'exception' => $e->getMessage()
                ));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function shareList(array $posts)
    {
        $contacts = $this->contactProvider->getContacts();
        $now = new \DateTime();
        $from = $this->from;
        $subject = $this->getSubject($now);

        $response = array(
            'sended' => false,
            'errors' => array()
        );

        foreach($posts as $key => $post){
            if(!$post->getPublished()){
                $response['errors'][] = $this->translator->trans('newsletter_post_not_published', array(
                    '%post_title%' => $post->getTitle()
                ), 'PostAdmin');
            }
        }

        if(empty($response['errors'])){
            foreach($contacts as $contact){
                if(!$contact instanceof ContactInterface){
                    throw new \InvalidArgumentException(sprintf("%s must implement %s.", get_class($contact), ContactInterface::class));
                }

                $template = $this->templating->render('@Admin/Batch/Newsletter/share_posts.html.twig', array(
                    'posts' => $posts,
                    'contact' => $contact
                ));
                $to = array($contact->getEmail());

                try {
                    $this->emailSender->send($from, $to, $subject, $template);
                    $response['sended'] = true;
                } catch (\Exception $e) {
                    $response['errors'][] = $this->translator->trans('flash_batch_newsletter_error_specific_email', array(
                        '%contact_email%' => $contact->getEmail()
                    ), 'PostAdmin');
                    $this->logger->error(sprintf('Newsletter not sent to %s', $contact->getEmail()), array(
                        'exception' => $e->getMessage()
                    ));
                }
            }

            if(!$this->isSuperAdmin){
                foreach($posts as $post){
                    $post->setSharedNewsletter($now);
                }
            }
        }

        return $response;
    }

    public function alertActivate(Newsletter $newsletter)
    {
        $from = $this->from;
        $subject = 'Activer votre abonnement';

        $contact = (new Contact())
            ->setEmail($newsletter->getMail())
            ->setToken($newsletter->getToken())
            ->setUnSubscribable(true);

        $template = $this->templating->render('@Admin/Batch/Newsletter/alert_activate_newsletter.html.twig', array(
            'contact' => $contact
        ));

        try {
            $this->emailSender->send($from, array($contact->getEmail()), $subject, $template);
        } catch (\Exception $e) {
            $this->logger->error(sprintf('Activate Newsletter not sent to %s', $contact->getEmail()), array(
                'exception' => $e->getMessage()
            ));
        }
    }

    private function getSubject(\DateTime $date)
    {
        return sprintf("[%s] Des nouveautés sur le site du BCP", $date->format('d-m-Y'));
    }
}
