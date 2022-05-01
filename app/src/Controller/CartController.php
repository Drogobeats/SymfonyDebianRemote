<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Form\CartType;
use App\Manager\CartManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CartController
 * @package App\Controller
 */
class CartController extends AbstractController
{
    /**
     * @Route("/api/carts", name="cart")
     */
    public function index(CartManager $cartManager): Response
    {
        $cart = $cartManager->getCurrentCart();
        $form = $this->createForm(CartType::class, $cart);

        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/api/carts/validate", name="cart_validate", methods={"GET", "POST"})
     */
    public function cart_validate(Request $request,CartManager $cartManager): Response
    {
        $cart = $cartManager->getCurrentCart();
        $form = $this->createForm(CartType::class, $cart);
        $form->handleRequest($request);

        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/api/carts/clear", name="cart_clear", methods={"GET", "POST"})
     */
    /**
     * Removes all items from the cart when the clear button is clicked.
     *
     * @param FormEvent $event
     * @return Response
     */
    public function cart_clear(FormEvent $event): Response
    {
        $form = $event->getForm();
        $cart = $form->getData();

        if (!$cart instanceof Orders) {
            return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
        }

        // Is the clear button clicked?
        if (!$form->get('clear')->isClicked()) {
            return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
        }

        // Clears the cart
        $cart->removeItems();

        return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
    }

}
