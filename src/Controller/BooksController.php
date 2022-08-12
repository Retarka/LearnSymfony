<?php

namespace App\Controller;

use App\Entity\Book;
use App\Type\BookType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\BookRepository;

class BooksController extends AbstractController
{
    #[Route('/books', name: 'app_books_list')]
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {
        // get list of books from db
        $books = $doctrine->getRepository(Book::class)->findAll();
        return $this->render('books/index.html.twig', [
            'books' => $books
        ]);
    }

    #[Route('/books/create', name: 'app_books_create')]
    public function create(Request $request, ManagerRegistry $doctrine): Response {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $entityManager = $doctrine->getManager();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($book);
            $entityManager->flush();
            

            return $this->redirectToRoute("book_show", ["id"=>$book->getId()]);
        }

        return $this->render('books/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/books/{id}', name: 'book_show')]
    public function show(ManagerRegistry $doctrine, int $id): Response
    {
        $book = $doctrine->getRepository(Book::class)->find($id);

        if (!$book) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }
//        return new Response('Saved new book with id '.$book->getId());
        return $this->render('books/view.html.twig', ['book' => $book]);

     
    }


}

