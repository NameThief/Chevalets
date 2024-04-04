<?php

namespace App\Controller;

use App\Model\Chevalet;
use App\Service\ChevaletPDFMaker;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ChevaletController extends AbstractController
{

    #[Route('/generate-pdf', name: 'generate_pdf')]
    public function generatePdf(Request $request): Response
    {
        $chevalet = new Chevalet();
        $form = $this->createFormBuilder($chevalet)
            ->add('nom', TextType::class)
            ->add('prenom', TextType::class)
            ->add('fonction', TextType::class)
            ->getForm();
        // Créez le formulaire
//        $form = $this->createForm(ChevaletType::class);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données du formulaire
            $data = $form->getData();
            $chevaletPDFMaker = new ChevaletPDFMaker();
            $chevaletPDFMaker->addChevaletToPDF($data);

            // Envoyer le PDF en réponse
            return new Response($chevaletPDFMaker->getOutput(), 200, [
                'Content-Type' => 'application/pdf',
            ]);
        }
        // Afficher le formulaire Twig
        return $this->render('chevalet/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
