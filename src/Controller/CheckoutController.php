<?php

namespace App\Controller;

use App\Entity\LineOrder;
use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;

class CheckoutController extends AbstractController
{
    #[IsGranted("ROLE_USER")]
    #[Route('/checkout_success/{token}', name: 'checkout_success')]
    public function checkout(EntityManagerInterface $manager, SessionInterface $session, OrderRepository $orderRepo, string $token)
    {
        if ($this->isCsrfTokenValid('stripe_token', $token)) {
            $order = $orderRepo->find($session->get("order_waiting"));
            $order->setStatus("PAYMENT_OK");
            $manager->flush();
            return $this->render('checkout/success.html.twig', [
                "order" => $order
            ]);
        } else {
            return $this->render('checkout/error.html.twig', []);
        }
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/checkout_error', name: 'checkout_error')]
    public function error()
    {
        return $this->render('checkout/error.html.twig', []);

    }

    #[IsGranted("ROLE_USER")]
    #[Route('/checkout', name: 'checkout')]
    public function index(EntityManagerInterface $manager, ProductRepository $productRepo, SessionInterface $session)
    {
        $tokenProvider = $this->container->get('security.csrf.token_manager');
        $token = $tokenProvider->getToken('stripe_token')->getValue();
        $stripe_items = [];
        $cart = $session->get("cart", []);
        if (empty($cart)) {
            return $this->redirectToRoute("home");
        }
        $order = new Order;
        $order->setDatetime(new DateTime);
        $order->setStatus("PAYMENT_WAITING");
        $total = 0;

        foreach ($cart as $key => $quantity) {
            $product = $productRepo->find($key);
            $line = new LineOrder;
            $line->setProduct($product);
            $line->setQuantity($quantity);
            $line->setSubtotal($quantity * $product->getPrice());
            $total += $quantity * $product->getPrice();
            $order->addLineOrder($line);
            //equivalent
            // $line->setOrderAssociated($order);
            $stripe_items[] =
                [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $product->getName(),
                        ],
                        'unit_amount' => $product->getPrice(),
                    ],
                    'quantity' => $quantity,
                ];
        }
        $order->setTotal($total);
        $manager->persist($order);
        $manager->flush();
        $session->set("order_waiting", $order->getId());

        \Stripe\Stripe::setApiKey($_ENV["STRIPE_API_KEY"]);
        $session = \Stripe\Checkout\Session::create([
            'line_items' => $stripe_items,
            'mode' => 'payment',
            'success_url' => 'http://localhost:8000/checkout_success/' . $token,
            'cancel_url' => 'http://localhost:8000/checkout_error'
        ]);

        return $this->redirect($session->url, 303);
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/order', name: 'order', methods: ['POST'])]
    public function order(Request $request, EntityManagerInterface $manager, ProductRepository $productRepo): Response
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['cart'])) {
            return $this->json(['error' => 'Cart is empty'], 400);
        }

        $order = new Order;
        $order->setDatetime(new DateTime);
        $order->setStatus("PAYMENT_WAITING");
        $total = 0;

        foreach ($data['cart'] as $productId => $quantity) {
            $product = $productRepo->find($productId);
            if (!$product) {
                return $this->json(['error' => "Product with id $productId not found"], 404);
            }

            $line = new LineOrder;
            $line->setProduct($product);
            $line->setQuantity($quantity);
            $line->setSubtotal($quantity * $product->getPrice());
            $total += $quantity * $product->getPrice();
            $order->addLineOrder($line);
        }

        $order->setTotal($total);
        $manager->persist($order);
        $manager->flush();

        \Stripe\Stripe::setApiKey($_ENV["STRIPE_API_KEY"]);

        $stripeItems = [];
        foreach ($data['cart'] as $productId => $quantity) {
            $product = $productRepo->find($productId);
            $stripeItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $product->getName(),
                    ],
                    'unit_amount' => $product->getPrice(),
                ],
                'quantity' => $quantity,
            ];
        }

        $stripeSession = \Stripe\Checkout\Session::create([
            'line_items' => $stripeItems,
            'mode' => 'payment',
            'success_url' => 'http://localhost:8000/checkout_success/',
            'cancel_url' => 'http://localhost:8000/checkout_error'
        ]);

        return $this->json(['url' => $stripeSession->url], 303);
    }
}
