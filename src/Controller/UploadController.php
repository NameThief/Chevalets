<?php

namespace App\Controller;

use App\Model\Reunion;
use App\Service\ChevaletPDFMaker;
use App\Service\CompteRenduWordMaker;
use App\Service\EmargementPDFMaker;
use App\Service\ExcelParser;
use PhpOffice\PhpWord\PhpWord;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UploadController extends AbstractController
{
    #[Route('/upload', name: 'upload_page')]
    public function uploadPage(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('fichier', FileType::class)
            ->add('document', ChoiceType::class, [
                'choices' => [
                    'Chevalets pour réunions' => 'chevalets',
                    'Liste d\'émargements' => 'emargement',
                    'Compte rendu' => 'compte_rendu',
                    'Relevé de conclusion' => 'releve_conclusion'
                ],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $reunion = new Reunion();
            $excelParser = new ExcelParser();
            $excelParser->setExcelFilePath($data['fichier']->getPathname());
            $excelParser->parse($reunion);

            if ($data['document'] == 'chevalets') {
                $chevaletPDFMaker = new ChevaletPDFMaker();
                $chevaletPDFMaker->makePdfForReunion($reunion);
                $content = $chevaletPDFMaker->getOutput();
                return new StreamedResponse(function () use ($content) {
                    echo $content;
                }, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="chevalets.pdf"'
                ]);
            } elseif ($data['document'] == 'emargement') {
                $emargementPDFMaker = new EmargementPDFMaker($reunion);

                // Appel de la méthode generatePDF avec les données extraites
                $content = $emargementPDFMaker->makePDF();

                // Générer le PDF et le renvoyer en réponse
                return new StreamedResponse(function () use ($content) {
                    echo $content;
                }, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="emargement.pdf"'
                ]);
            } elseif ($data['document'] == 'compte_rendu') {
                $compteRenduWordMaker = new CompteRenduWordMaker($reunion);
                $content = $compteRenduWordMaker->makeWord();

                return new StreamedResponse(function () use ($content) {
                    echo $content;
                }, 200, [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'Content-Disposition' => 'attachment; filename="CompteRendu.docx"'
                ]);
            }
        }
        // Renvoyer le formulaire à la vue
        return $this->render('upload/upload.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}