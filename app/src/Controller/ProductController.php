<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\AddToCartType;
use App\Form\ProductType;
use App\Manager\CartManager;
use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ProductController extends AbstractController
{
    /**
     * @Route("/api/products", name="home")
     */
    public function index(ProductsRepository $productRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    /**
     * @Route("/api/products/new", name="app_product_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ProductsRepository $productsRepository): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productsRepository->add($product);
            return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/api/products/{id}", name="product.detail")
     */
    public function detail(Product $product, Request $request, CartManager $cartManager)
    {
        $form = $this->createForm(AddToCartType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $item = $form->getData();
            $item->setProduct($product);

            $cart = $cartManager->getCurrentCart();
            $cart
                ->addItem($item)
                ->setUpdatedAt(new \DateTime());

            $cartManager->save($cart);

            return $this->redirectToRoute('product.detail', ['id' => $product->getId()]);
        }

        return $this->render('product/detail.html.twig', [
            'product' => $product,
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/api/products/{id}/edit", name="app_product_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Product $product, ProductsRepository $productsRepository): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productsRepository->add($product);
            return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/api/products/{id}/delete", name="app_product_delete", methods={"POST"})
     */
    public function delete(Request $request, Product $product, ProductsRepository $productsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $productsRepository->remove($product);
        }

        return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
    }
}
