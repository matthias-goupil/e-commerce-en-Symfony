<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Product;
use App\Entity\User;
use App\Form\CartType;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/cart")
 */
class CartController extends AbstractController
{
    /**
     * @Route("/", name="app_cart_index", methods={"GET"})
     */
    public function index(CartRepository $cartRepository): Response
    {
        return $this->render('cart/index.html.twig', [
            'carts' => $cartRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_cart_new", methods={"GET", "POST"})
     */
    public function new(Request $request, CartRepository $cartRepository): Response
    {
        $cart = new Cart();
        $form = $this->createForm(CartType::class, $cart);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cartRepository->add($cart);
            return $this->redirectToRoute('app_cart_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('cart/new.html.twig', [
            'cart' => $cart,
            'form' => $form,
        ]);
    }

    /**
     * @Route ("/{idUser<\d+>}/add-product/{idProduct<\d+>}", name="app_cart_add_product")
     */
    public function add($idUser, $idProduct,UserRepository $userRepository, ProductRepository $productRepository,CartRepository $cartRepository){
        $user = $userRepository->findOneBy([
            'id' => $idUser
        ]);

        $product = $productRepository->findOneBy([
            'id' => $idProduct
        ]);

        $cart = new Cart();
        $cart->setUser($user);
        $cart->setProduct($product);
        $cart->setQuantity(1);
        $cartRepository->add($cart);

        return $this->redirectToRoute("app_cart_show",[
            "id" => $idUser
        ]);
    }
    /**
     * @Route("/{id}", name="app_cart_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        $carts = $user->getCarts();
        return $this->render('cart/show.html.twig', [
            'user' => $user,
            'carts' => $carts
        ]);
    }

    /**
     * @Route("/{idCart}/edit", name="app_cart_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, $idCart,CartRepository $cartRepository): Response
    {
        $cart = $cartRepository->findOneBy([
            "id" => $idCart
        ]);
        $form = $this->createForm(CartType::class, $cart,[
            "max" => $cart->getProduct()->getStock(),
            "quantity" => $cart->getQuantity()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cartRepository->add($cart);
            return $this->redirectToRoute('app_cart_show', [
                "id" => $cart->getUser()->getId()
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('cart/edit.html.twig', [
            'cart' => $cart,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_cart_delete", methods={"POST"})
     */
    public function delete(Request $request, Cart $cart, CartRepository $cartRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cart->getId(), $request->request->get('_token'))) {
            $cartRepository->remove($cart);
        }

        return $this->redirectToRoute('app_cart_index', [], Response::HTTP_SEE_OTHER);
    }

}
