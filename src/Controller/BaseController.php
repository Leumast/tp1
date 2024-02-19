<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Produits;
use App\Entity\Categories;

class BaseController extends AbstractController
{
    #[Route('/', name: 'accueil')]
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {
        $searchQuery = $request->query->get('recherche');

        // Query products based on search query if it exists
        if ($searchQuery !== null) {
            $produits = $doctrine->getManager()
                ->getRepository(Produits::class)
                ->createQueryBuilder('p')
                ->where('p.nom LIKE :searchQuery OR p.description LIKE :searchQuery')
                ->setParameter('searchQuery', '%' . $searchQuery . '%')
                ->getQuery()
                ->getResult();
        } else {
            // If no search query, fetch all products
            $produits = $doctrine->getManager()
                ->getRepository(Produits::class)
                ->findAll();
        }

        // Fetch categories
        $categories = $doctrine->getManager()
            ->getRepository(Categories::class)
            ->findAll();

        return $this->render('accueil.html.twig', [
            'tabProduits' => $produits,
            'tabCategories' => $categories,
            'searchQuery' => $searchQuery, // Pass the search query to the template
        ]);
    }
}
