<?php

namespace App\Controller;

use App\Entity\Sweatshirt;
use App\Form\SweatshirtType;
use App\Form\SweatshirtAddType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'app_admin')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $sweatshirts = $em->getRepository(Sweatshirt::class)->findAll();

        $newSweatshirt = new Sweatshirt();
        $addForm = $this->createForm(SweatshirtAddType::class, $newSweatshirt);
        $addForm->handleRequest($request);

        if ($addForm->isSubmitted() && $addForm->isValid()) {
            $imageFile = $addForm->get('image')->getData();

            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image');
                    return $this->redirectToRoute('app_admin');
                }

                $newSweatshirt->setImage($newFilename);
            }

            $em->persist($newSweatshirt);
            $em->flush();

            $this->addFlash('success', 'Sweat-shirt ajouté avec succès.');

            return $this->redirectToRoute('app_admin');
        }

        $editForms = [];
        foreach ($sweatshirts as $sweatshirt) {
            $form = $this->createForm(SweatshirtType::class, $sweatshirt, [
                'method' => 'POST',
                'action' => $this->generateUrl('app_admin_edit', ['id' => $sweatshirt->getId()]),
            ]);
            $editForms[$sweatshirt->getId()] = $form->createView();
        }

        return $this->render('admin/admin.html.twig', [
            'sweatshirts' => $sweatshirts,
            'addForm' => $addForm->createView(),
            'editForms' => $editForms,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_admin_edit', methods: ['POST'])]
    public function editSweatshirt(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $sweatshirt = $em->getRepository(Sweatshirt::class)->find($id);

        if (!$sweatshirt) {
            $this->addFlash('error', 'Sweat-shirt non trouvé.');
            return $this->redirectToRoute('app_admin');
        }

        $form = $this->createForm(SweatshirtType::class, $sweatshirt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image');
                    return $this->redirectToRoute('app_admin');
                }

                $sweatshirt->setImage($newFilename);
            }

            $em->flush();

            $this->addFlash('success', 'Sweat-shirt modifié.');
        } else {
            $this->addFlash('error', 'Erreur lors de la modification.');
        }

        return $this->redirectToRoute('app_admin');
    }

    #[Route('/delete/{id}', name: 'app_admin_delete', methods: ['POST'])]
    public function deleteSweatshirt(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $sweatshirt = $em->getRepository(Sweatshirt::class)->find($id);

        if (!$sweatshirt) {
            $this->addFlash('error', 'Sweat-shirt non trouvé.');
            return $this->redirectToRoute('app_admin');
        }

        if ($this->isCsrfTokenValid('delete' . $id, $request->request->get('_token'))) {
            $em->remove($sweatshirt);
            $em->flush();

            $this->addFlash('success', 'Sweat-shirt supprimé.');
        }

        return $this->redirectToRoute('app_admin');
    }
}
