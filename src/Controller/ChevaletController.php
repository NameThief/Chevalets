<?php

namespace App\Controller;


use App\Model\Personne;
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
        $personne = new Personne();
        $form = $this->createFormBuilder($personne)
            ->add('nom', TextType::class,[
                'attr' => ['class' => 'fr-input', 'autocomplete' => 'off', 'style' =>'width: 400px'],
                'label' => 'Nom',
                'label_attr' => ['class' => 'fr-label']
            ])
            ->add('prenom', TextType::class,[
                'attr' => ['class' => 'fr-input', 'autocomplete' => 'off', 'style' =>'width: 400px'],
                'label' => 'Prénom',
                'label_attr' => ['class' => 'fr-label']
            ])
            ->add('fonction', TextType::class,[
                'attr' => ['class' => 'fr-input', 'autocomplete' => 'off', 'style' =>'width: 400px'],
                'label' => 'Fonction',
                'label_attr' => ['class' => 'fr-label']
            ])
            ->getForm();
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données du formulaire
            $data = $form->getData();
            $chevaletPDFMaker = new ChevaletPDFMaker();
            $chevaletPDFMaker->addChevaletToPDF($personne);

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
