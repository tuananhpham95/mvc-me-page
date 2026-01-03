<?php
// src/Controller/LearningItemController.php

namespace App\Controller;

use App\Entity\LearningItem;
use App\Form\LearningItemType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/proj/edu')]
class LearningItemController extends AbstractController
{
    // ---------- List all items ----------
    #[Route('/', name: 'edu_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $items = $em->getRepository(LearningItem::class)
            ->findBy([], ['createdAt' => 'DESC']);

        return $this->render('proj/edu/index.html.twig', [
            'items' => $items,
        ]);
    }

    // ---------- Create new item ----------
    #[Route('/create', name: 'edu_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $item = new LearningItem();
        $item->setStatus('learning'); // default status

        $form = $this->createForm(LearningItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $item->setCreatedAt(new \DateTime());
            $em->persist($item);
            $em->flush();

            $this->addFlash('success', 'Learning item created!');
            return $this->redirectToRoute('edu_index');
        }

        return $this->render('proj/edu/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // ---------- Show single item ----------
    #[Route('/{id}', name: 'edu_show', methods: ['GET'])]
    public function show(LearningItem $item): Response
    {
        return $this->render('proj/edu/show.html.twig', [
            'item' => $item,
        ]);
    }

    // ---------- Edit an item ----------
    #[Route('/{id}/edit', name: 'edu_edit')]
    public function edit(
        Request $request,
        LearningItem $item,
        EntityManagerInterface $em
    ): Response {
        $form = $this->createForm(LearningItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Learning item updated!');
            return $this->redirectToRoute('edu_index');
        }

        return $this->render('proj/edu/edit.html.twig', [
            'form' => $form->createView(),
            'item' => $item,
        ]);
    }

    // ---------- Delete an item ----------
    #[Route('/{id}/delete', name: 'edu_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        LearningItem $item,
        EntityManagerInterface $em
    ): Response {
        // Cast to string to satisfy PHPStan / type hint
        $token = (string) $request->request->get('_token');

        if ($this->isCsrfTokenValid('delete' . $item->getId(), $token)) {
            $em->remove($item);
            $em->flush();
            $this->addFlash('success', 'Learning item deleted!');
        }

        return $this->redirectToRoute('edu_index');
    }
}
