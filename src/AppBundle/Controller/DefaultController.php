<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Newsletter;
use AppBundle\Entity\Page;
use AppBundle\Entity\Post;
use AppBundle\Form\Type\ReportFeedbackType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Email as EmailConstraint;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template("@App/default/index.html.twig")
     */
    public function indexAction(Request $request)
    {
        $query = $this->getDoctrine()->getRepository('AppBundle:Post')->getIndexQuery();
        $paginator = $this->get('knp_paginator');
        $page = $request->query->getInt('page', 1);
        $limit = 10;
        $posts = $paginator->paginate($query, $page, $limit);

        return array('posts' => $posts);
    }

    /**
     * @Route("/categorie/{slug}", name="category")
     * @ParamConverter("category", options={"slug": "slug"})
     * @Template("@App/default/category/index.html.twig")
     */
    public function categoryAction(Request $request, Category $category)
    {
        $query = $this->getDoctrine()->getRepository('AppBundle:Post')->getCategoryQuery($category->getId());
        $paginator = $this->get('knp_paginator');
        $page = $request->query->getInt('page', 1);
        $limit = 10;
        $posts = $paginator->paginate($query, $page, $limit);

        return array('posts' => $posts, 'category' => $category);
    }

    /**
     * @Route("/page/listing", name="listing")
     * @Template("@App/default/listing.html.twig")
     */
    public function listingAction()
    {
        $url = "http://www.ffbsq.org/bowling/listing/listing-ws.php?output=xml&asfile=false&num_licence=&departement=&region=&club=poitevin&nom=";

        $crawler = new Crawler();
        $crawler->addXmlContent(file_get_contents($url));
        $listing = array();
        $exclude = array('club');

        foreach($crawler as $xListing){
            foreach($xListing->childNodes as $joueur){
                $tmpJoueur = array();

                foreach($joueur->attributes as $attr){
                    if(!in_array($attr->name, $exclude)){
                        $tmpJoueur[$attr->name] = $joueur->getAttribute($attr->name);
                    }
                }

                $listing[] = $tmpJoueur;
            }
        }

        $dynamicArray = $this->getDynamicArray($listing);
        array_multisort($dynamicArray['moyenne'], SORT_DESC, $dynamicArray['nom'], SORT_ASC, $listing);

        return array('listing' => $listing);
    }

    /**
     * @Route("/page/gallerie", name="gallery")
     * @Template("@App/default/gallery.html.twig")
     */
    public function galleryAction(Request $request)
    {
        $query = $this->getDoctrine()->getRepository('AppBundle:Image')->getGalleryQuery();
        $paginator = $this->get('knp_paginator');
        $page = $request->query->getInt('page', 1);
        $limit = 10;
        $images = $paginator->paginate($query, $page, $limit);

        return array('images' => $images, 'limit' => $limit);
    }

    /**
     * @Route("/post/{slug}", name="post_view")
     * @Template("@App/default/post/view.html.twig")
     * @ParamConverter("post", options={"slug": "slug"})
     */
    public function postViewAction(Post $post){}

    /**
     * @Route("/page/{slug}", name="page_view")
     * @Template("@App/default/page/view.html.twig")
     * @ParamConverter("page", options={"slug": "slug"})
     */
    public function pageViewAction(Page $page){}

    /**
     * @Route("/archives/archive_bcp_{years}", name="archives")
     */
    public function archivesAction($years)
    {
        $template = '@App/default/archives/bcp_' . $years . '.html.twig';

        if(!$this->get('templating')->exists($template)){
            throw $this->createNotFoundException();
        }

        return $this->render($template);
    }

    /**
     * @Route("/newsletter/register", name="newsletter_register")
     * @Method("POST")
     */
    public function registerNewsletterAction(Request $request)
    {
        $mail = $request->request->get('mail_address');
        $reCaptchaResponse = $request->request->get('g-recaptcha-response');

        $emailConstraint = new EmailConstraint();
        $emailConstraint->message = 'Votre adresse mail est invalide...';
        $errors = $this->get('validator')->validate($mail, $emailConstraint);
        $isHuman = $this->get('bcp.recaptcha')->isHuman($reCaptchaResponse);

        $em = $this->getDoctrine()->getManager();
        $newsletterAlreadyExist = $em->getRepository('AppBundle:Newsletter')->findOneBy(array('mail' => $mail));
        $userMailAlreadyExist = $em->getRepository('UserBundle:User')->findOneBy(array('email' => $mail));

        if(null !== $newsletterAlreadyExist || null !== $userMailAlreadyExist){
            $this->addFlash('danger', 'Cette adresse mail a déjà été ajoutée.');
        } elseif($mail !== '' && $isHuman && $errors->count() === 0){
            $newsletter = new Newsletter();
            $newsletter->setMail($mail);
            $newsletter->setToken($this->get('fos_user.util.token_generator')->generateToken());

            $em->persist($newsletter);
            $em->flush();

            $flash = sprintf('L\'adresse mail "%s" a bien été ajoutée. Vous recevrez dès à présent nos nouveautés.', $mail);
            $this->addFlash('success', $flash);
        } else {
            $this->addFlash('danger', $emailConstraint->message);
        }

        return $this->redirect($this->generateUrl('homepage'));
    }

    /**
     * @Route("/cookie/consent", name="cookie_consent")
     * @Method("GET")
     */
    public function cookieConsentAction()
    {
        $cookie = new Cookie('cookie_consent', 'true', time() + (10 * 365 * 24 * 60 * 60));
        $response = new RedirectResponse($this->generateUrl('homepage'));

        $response->headers->setCookie($cookie);

        return $response;
    }

    /**
     * @Route("/newsletter/remove/{token}", name="newsletter_remove")
     * @Method("GET")
     */
    public function removeNewsletterAction($token)
    {
        $em = $this->getDoctrine()->getManager();
        $newsletter = $em->getRepository('AppBundle:Newsletter')->findOneBy(array('token' => $token));

        if(null === $newsletter){
            $this->addFlash('danger', 'Le token de vérification ne correspond pas...');
        } else {
            $em->remove($newsletter);
            $em->flush();

            $this->addFlash('success', 'Vous ne recevrez plus nos nouveautés. Nous espérons tout de même vous revoir bientôt.');
        }

        return $this->redirect($this->generateUrl('homepage'));
    }

    /**
     * @Route("/report/feedback", name="report_feedback")
     * @Method({"GET", "POST"})
     * @Template("@App/default/report_feedback.html.twig")
     */
    public function reportFeedbackAction(Request $request)
    {
        $datas = array();

        if($this->isGranted('IS_AUTHENTICATED_FULLY') || $this->isGranted('IS_AUTHENTICATED_REMEMBERED')){
            $user = $this->getUser();

            $datas['email'] = $user->getEmail();
        }

        $form = $this->createForm(ReportFeedbackType::class, $datas);

        if($form->handleRequest($request)->isValid()){
            $mailer = $this->get('bcp.email_sender');
            $from = $this->getParameter('newsletter_from');
            $to = 'nathan.page86@gmail.com';
            $subject = 'Feedback';

            $email = $form->get('email')->getData();
            $content = $form->get('content')->getData();
            $body = sprintf('<p>[Email de réponse]</p><p><b>%s</b></p><p>[Contenu]</p>%s', $email, $content);

            $mailer->send($from, array($to), $subject, $body);

            $this->addFlash('success', 'Votre remarque a été correctement envoyée !');

            return $this->redirect($this->generateUrl('homepage'));
        }

        return array('form' => $form->createView());
    }

    /**
     * @Template("@App/default/pages.html.twig")
     */
    public function pagesAction()
    {
        $pages = $this->getDoctrine()->getRepository('AppBundle:Page')->findAllOrderByPriorityAndPublished();

        return array('pages' => $pages);
    }

    /**
     * @Template("@App/default/categories.html.twig")
     */
    public function categoriesAction()
    {
        $categories = $this->getDoctrine()->getRepository('AppBundle:Category')->findAll();

        return array('categories' => $categories);
    }

    /**
     * Provide a dynamic array for array_multisort
     *
     * @param $array
     * @return array
     */
    private function getDynamicArray($array) {
        $retour = array();

        foreach ($array as $row) {
            foreach ($row as $k => $v) {
                $retour[$k][] = $v;
            }
        }

        return $retour;
    }
}
