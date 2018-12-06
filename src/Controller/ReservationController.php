<?php

namespace PiedWeb\ReservationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Method;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormError;
use PiedWeb\ReservationBundle\Entity\OrderInterface as Order;
use PiedWeb\ReservationBundle\Entity\ProductInterface as Product;
use PiedWeb\ReservationBundle\Entity\BasketInterface as Basket;
use PiedWeb\ReservationBundle\Entity\BasketItemInterface as BasketItem;
use PiedWeb\CMSBundle\Entity\UserInterface as User;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security as aSecurity;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Doctrine\ORM\EntityManagerInterface;

class ReservationController extends AbstractController
{
    use HelperTrait;

    protected $requestStack;
    protected $em;
    protected $eventDispatcher;

    public function __construct(EntityManagerInterface $em, RequestStack $requestStack, EventDispatcherInterface $eventDispatcher)
    {
        $this->requestStack = $requestStack;
        $this->em = $em;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Etape 1 : Nbr de Participans
     * If I want to use it without container, i should add ParameterBagInterface $params.
     *
     * @param int $id Product's id
     */
    public function step_1(int $id): Response
    {
        $product = $this->getProduct($id);

        $defaultData = [
            'number' => 1,
        ];
        $form = $this->createFormBuilder($defaultData)
            ->add('number', IntegerType::class, [
                'required' => true,
                'constraints' => [
                    new Assert\Type(['type' => 'integer', 'message' => 'Un chiffre supérieur à 0 est requis']),
                    new Assert\GreaterThan(['value' => 0, 'message' => 'Un chiffre supérieur à 0 est requis']),
                ],
            ])
            ->add('forMe', CheckboxType::class, ['required' => false])

            ->getForm();

        $form->handleRequest($this->container->get('request_stack')->getCurrentRequest());
        if ($form->isSubmitted() && $form->isValid()) {
            $basket = $this->getBasketForCurrentUser();

            $number = $form->get('number')->getData();
            $basketItemClass = $this->container->getParameter('app.entity_basket_item');
            for ($i = 1; $i <= $number; ++$i) {
                $basketItem = new $basketItemClass();
                $basketItem
                    ->setProduct($product)
                    ->setForMe(1 == $i && !$form->get('forMe')->getData() ? true : false)
                    ->setBasket($basket)
                    ->setPriceHt($product->getPriceHt())
                    ->setVat($product->getVat())
                ;

                // we don't add if user have ever put in his basket the same product for him
                if (null === $this->getUser() || true !== $this->getBasketForCurrentUser()->issetItemForMe($product->getId())) {
                    $this->getDoctrine()->getManager()->persist($basketItem);
                }
            }

            $this->getDoctrine()->getManager()->flush();

            $response = $this->redirectToRoute('piedweb_reservation_step_2', ['id' => $basket->getId()]);

            if (null !== $this->newCookie) {
                $response->headers->setCookie($this->newCookie);
            }

            return $response;
        }

        $data = ['step' => 1, 'form' => $form->createView(), 'product' => $product];

        return $this->render('@PiedWebReservation/reservation/reservation.html.twig', $data);
    }

    /**
     * Etape 2 : Déjà Partis ?
     *
     * @param int $id basket's id - not really required
     */
    public function step_2(int $id): Response
    {
        $securityContext = $this->container->get('security.authorization_checker');
        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('piedweb_reservation_step_4', ['previous' => 3]);
        }

        if ($id !== intval($this->getBasketIdFromCookie())) {
            throw $this->createNotFoundException();
        }

        $form = $this->createFormBuilder()
            ->add('oui', SubmitType::class)
            ->add('non', SubmitType::class)
            ->getForm();

        $form->handleRequest($this->container->get('request_stack')->getCurrentRequest());
        if ($form->isSubmitted() && $form->isValid()) {
            $basket = $this->getBasket($id);
            if (null === $basket) {
                throw new NotFoundHttpException();
            }
            if ($form->get('oui')->isClicked()) {
                return $this->redirectToRoute('piedweb_reservation_step_4', ['previous' => 3]);
            } elseif ($form->get('non')->isClicked()) {
                return $this->redirectToRoute('piedweb_reservation_step_3_register', ['id' => $basket->getId()]);
            }
        }

        return $this->render('@PiedWebReservation/reservation/reservation.html.twig', ['step' => 2, 'form' => $form->createView()]);
    }

    /**
     * Etape 3 : S'inscire.
     */
    public function step_3_register(): Response
    {
        // It may possible an User bypass detailled registration and we need to force hom
        // to do it, else, we will not be able to do step 4.
        $securityContext = $this->container->get('security.authorization_checker');
        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            //return $this->redirectToRoute('piedweb_reservation_step_2', ['id' => $basket->getId()]);
            $user = $this->getUser();
        } else {
            $userClass = $this->container->getParameter('app.entity_user');
            $user = new $userClass();
            $user->setEnabled(true);
        }

        $form = $this->createFormBuilder($user)
            ->add('lastname', TextType::class)
            ->add('firstname', TextType::class)
            ->add('dateOfBirth', DateType::class, ['widget' => 'single_text', 'required' => false])
            ->add('phone', TelType::class)
            ->add('city', TextType::class)
        ;

        if (!$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $form
                ->add('email', EmailType::class)
                ->add('plainPassword', PasswordType::class, ['always_empty' => false])
            ;
        }

        $form = $form->getForm();

        $form->handleRequest($this->container->get('request_stack')->getCurrentRequest());
        if ($form->isSubmitted() && $form->isValid()) {
            // Register
            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();

            // Logged in
            $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
            $this->get('security.token_storage')->setToken($token);

            $response = $this->redirectToRoute('piedweb_reservation_step_4', ['previous' => 3]);

            // Transform basket from visitor to this user
            $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $this->requestStack->getCurrentRequest(), $response));

            return $response;
        }

        return $this->render('@PiedWebReservation/reservation/reservation.html.twig', ['step' => 3, 'form' => $form->createView()]);
    }

    /**
     * @aSecurity("has_role('ROLE_USER')")
     * Etape 4 : Renseigner les participants
     *
     * @param int $previous 3 means we are logged or just logged before coming here
     */
    public function step_4(int $previous = 0): Response
    {
        $basket = $this->getUser()->getBasket();

        if (null === $basket) {
            throw $this->createNotFoundException();
        }

        $basketItems = $basket->getBasketItems();
        $user = $this->getUser();
        $form = [];
        $formItemLabel = [];
        $checkName = [];
        $formDataComplement = [];

        if ($basketItems->isEmpty()) {
            return $this->render('@PiedWebReservation/reservation/no_reservation.html.twig');
        }

        foreach ($basketItems as $i => $basketItem) {
            if (true === $basketItem->getForMe() && (3 === $previous || null === $basketItem->getFirstname())) {
                if (null === $user->getFirstname() || null === $user->getLastname()) { // may we check every input ???
                    return $this->redirectToRoute('piedweb_reservation_step_3_register', ['id' => $basket->getId()]);
                }
                //$checkName[] = $user->getLastname().$user->getFirstname();
                $basketItem->setFirstname($user->getFirstname());
                $basketItem->setLastname($user->getLastname());
                $basketItem->setDateOfBirth($user->getDateOfBirth());
                $basketItem->setPhone($user->getPhone());
                $basketItem->setEmail($user->getEmail());
                $basketItem->setCity($user->getCity());
                $this->getDoctrine()->getManager()->flush();

                $subtitle = '<small>Vos détails ont été automatiquement ajouter<br><a href="'.$this->get('router')->generate('piedweb_reservation_step_4').'">Vérifier</a></small>';
            } else {
                $formItemLabel[$i] = $basketItem->getProduct()->getName();
                $formDataComplement[$i] = [
                    'forMe' => $basketItem->getForMe(),
                    'id' => $basketItem->getId(),
                ];
                $form[$i] = $this->get('form.factory')->createNamedBuilder('form_'.$i, 'Symfony\\Component\\Form\\Extension\\Core\\Type\\FormType', $basketItem)
                    ->add('lastname')
                    ->add('firstname')
                    ->add('dateOfBirth', DateType::class, ['widget' => 'single_text', 'required' => false])
                    ->add('phone')
                    ->add('email')
                    ->add('city')
                    ->getForm();
            }
        }

        if (!empty($form)) {
            $valid = [];
            foreach ($form as $i => $f) {
                $f->handleRequest($this->container->get('request_stack')->getCurrentRequest());
                if ($f->isSubmitted()) {
                    $valid[] = $f->isValid() ? true : false;
                    $data = $f->getData();
                    if (in_array($data->getLastname().$data->getFirstName(), $checkName)) {
                        $f->get('firstname')->addError(new FormError('Vous avez déjà inscrit ce participant ('.$data->getLastname().' '.$data->getFirstName().')'));
                        $valid[] = false;
                    }
                    $checkName[] = $data->getLastname().$data->getFirstName();
                }
            }

            if (!empty($valid) && !in_array(false, $valid)) {
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('piedweb_reservation_step_5');
            }

            //$this->getDoctrine()->getManager()->flush();

            $forms = [];
            foreach ($form as $i => $f) {
                $forms[$i] = $f->createView();
            }

            return $this->render('@PiedWebReservation/reservation/reservation.html.twig', [
                'step' => 4,
                'forms' => $forms,
                'formItemLabel' => $formItemLabel,
                'forDifferentItem' => count(array_unique($formItemLabel)) < 2 ? false : true,
                'formDataComplement' => $formDataComplement,
                'subtitle' => isset($subtitle) ? $subtitle : null,
            ]);
        }

        return $this->redirectToRoute('piedweb_reservation_step_5');
    }

    /**
     * @aSecurity("has_role('ROLE_USER')")
     * Etape 5 : Validation et Paiement
     */
    public function step_5(): Response
    {
        $user = $this->getUser();
        $basket = $user->getBasket();

        if ($basket->getBasketItems()->isEmpty()) {
            return $this->render('@PiedWebReservation/reservation/no_reservation.html.twig');
        }

        foreach ($basket->getBasketItems() as $basketItem) {
            if (!$this->doesThisBasketItemCompleted($basketItem)) {
                return $this->redirectToRoute('piedweb_reservation_step_4');
            }
        }

        $form = $this->createFormBuilder()
            ->add('creditcard', SubmitType::class)
            ->add('espece', SubmitType::class)
            ->getForm();

        $form->handleRequest($this->container->get('request_stack')->getCurrentRequest());
        if ($form->isSubmitted() && $form->isValid()) {
            $order = $this->createOrder($form);
            if (false !== $order) {
                return $this->redirectToRoute('piedweb_reservation_step_6', ['id' => $order->getId()]);
            }
        }

        $data = ['step' => 5, 'form' => $form->createView(), 'basket' => $basket];

        return $this->render('@PiedWebReservation/reservation/reservation.html.twig', $data);
    }

    protected function createOrder($form)
    {
        $espece = $form->get('espece')->isClicked();
        $creditcard = $form->get('creditcard')->isClicked();

        foreach ($this->getUser()->getBasket()->getBasketItems() as $basketItem) {
            $participantNumber = $basketItem->getProduct()->getParticipantNumber();
            $participantsCount = $basketItem->getProduct()->getParticipants()->count();
            if (null !== $participantNumber && $participantNumber > 0 && $participantNumber <= $participantsCount) {
                // todo: fire event No place anymore
                $this->addFlash(
                    'warning',
                    '<b>'.$basketItem->getProduct()->getName().'</b> :<br>'.
                    $this->get('translator')->trans('reservation.full')
                );

                return false;
            } elseif (null !== $participantNumber && $participantNumber > 0 && $participantNumber <= $participantsCount + 3) {
                //todo: log + mail : il ne reste plus que 2 places pour la sortie taratata
                // add a paramater in app.alert_almost_full: [0-10]
            }
        }

        if ($espece || $creditcard) {
            $order = $this->basketToOrder($this->getUser()->getBasket());

            if ($creditcard) {
                $order->setPaiementMethod(2);
                $order->setPaid(false);
            // todo: redirection vers PAYAPL (ou autre)
            } elseif ($espece) {
                $order->setPaiementMethod(1);
                $order->setPaid(false);
                $this->sendConfirmationMail($order);
            }

            $this->getDoctrine()->getManager()->persist($order);
            $this->getDoctrine()->getManager()->flush();
        } else {
            $this->addFlash(
                    'danger',
                    $this->get('translator')->trans('paiementMethod.invalid')
                );

            return false;
        }

        return $order;
    }

    protected function basketToOrder($basket)
    {
        $orderClass = $this->container->getParameter('app.entity_order');
        $order = new $orderClass();
        $order->setUser($this->getUser());

        foreach ($this->getUser()->getBasket()->getBasketItems() as $basketItem) {
            $orderItem = $this->basketItemToOrderItem($basketItem);
            $this->getDoctrine()->getManager()->remove($basketItem);
            $order->addOrderItem($orderItem);
        }

        return $order;
    }

    protected function basketItemToOrderItem($basketItem)
    {
        $orderItemClass = $this->container->getParameter('app.entity_order_item');
        $orderItem = new $orderItemClass();

        //$cols = $this->getDoctrine()->getManager()->getClassMetadata($orderItemClass)->getColumnNames();
        $cols = get_class_methods($this->container->getParameter('app.entity_order_item'));
        foreach ($cols as $col) {
            if ('set' == substr($col, 0, 3)) {
                $col = substr($col, 3);
                $getter = 'get'.$col;
                $setter = 'set'.$col;
                if (method_exists($basketItem, $getter) && method_exists($orderItem, $setter) && 'id' != $col) {
                    $orderItem->$setter($basketItem->$getter());
                }
            }
        }

        // a product
        $orderItem->setProduct($basketItem->getProduct());

        return $orderItem;
    }

    protected function doesThisBasketItemCompleted($basketItem): bool
    {
        if (empty($basketItem->getLastname()) || empty($basketItem->getFirstname())) {
            return false;
        }

        return true;
    }

    protected function sendConfirmationMail($order)
    {
        //todo
    }

    /**
     * Etape 6 : Réserver avec succès.
     */
    public function step_6(int $id): Response
    {
        $order = $this->getOrder($id);

        if (1 == 2) { // Reviens de paypal et c'est OK
            $order->setPaid(true);
            $order->setPaiementMethod(2);
            $order->paidAt(new \DateTime());
            $this->sendConfirmationMail($order);
        } elseif (1 == 2) { // Reviens de paypal et c'est un pb
            $this->addFlash('danger', 'Le Paiement ne semble pas avoir fonctionné... A COMPLETER A VOIR');
        }

        $data = ['step' => 6];

        $data['page'] = $this->em
            ->getRepository($this->container->getParameter('app.entity_page'))
            ->findOneBySlug('step-6', $this->requestStack->getCurrentRequest()->getLocale())
        ;


        return $this->render('@PiedWebReservation/reservation/reservation.html.twig', $data);
    }

    public function deleteBasketItem(int $id, int $from)
    {
        // todo: Il faudrait que je vérifie qu'il y a un user (car si delete d'un basket non loguer !)
        $user = $this->getUser();
        $basket = $user->getBasket();
        $basketItem = $basket->getBasketItem($id);

        if ($basket->getBasketItems()->isEmpty() || null === $basketItem) {
            throw $this->createNotFoundException();
        }

        $form = $this->createFormBuilder()
            ->add('oui', SubmitType::class)
            ->add('non', SubmitType::class)
            ->getForm();

        $form->handleRequest($this->container->get('request_stack')->getCurrentRequest());
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('oui')->isClicked()) {
                $this->getDoctrine()->getManager()->remove($basketItem); // to track ??? Yes : create an event table
                $this->getDoctrine()->getManager()->flush();
            }

            if ($from > 0 && in_array($from, [4, 5])) {
                return $this->redirectToRoute('piedweb_reservation_step_'.$from);
            } else {
                throw new \Exception('todo: page pour gérer son panier');
                //return $this->redirectToRoute('piedweb_reservation_basket');
            }
        }

        //echo '<pre>'; var_dump($basketItem->getFirstName()); die();
        $data = ['step' => 'delete_basket_item', 'form' => $form->createView(), 'firstname' => $basketItem->getFirstName()];

        return $this->render('@PiedWebReservation/reservation/reservation.html.twig', $data);
    }

    /**
     * @aSecurity("has_role('ROLE_USER')")
     * Etape 5 bis : Validation et Paiement depuis une commande
     *
     * @param int $id Order's id
     */
    public function step_5_bis(int $id): Response
    {
        $order = $this->getOrder($id);

        if (null === $order
            || $order->getUser() != $this->getUser()
            || !$this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')
           ) {
            $this->createNotFoundException();
        } elseif ($order->getPaidAt()) {
            return $this->render('@PiedWebReservation/reservation/ever_paid.html.twig');
        }

        $form = $this->getPaiementForm();

        if ($form->isSubmitted() && $form->isValid()) {
            // todo: redirect to paiement method
            return $this->redirectToRoute('piedweb_reservation_step_6', ['id' => $order->getId()]);
        }

        $data = ['step' => 5, 'form' => $form->createView(), 'basket' => $order];

        return $this->render('@PiedWebReservation/reservation/reservation.html.twig', $data);
    }

    protected function getPaiementForm()
    {
        $form = $this->createFormBuilder()
            ->add('creditcard', SubmitType::class)
            ->add('espece', SubmitType::class)
            ->getForm();

        $form->handleRequest($this->container->get('request_stack')->getCurrentRequest());

        return $form;
    }
}
