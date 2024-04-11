<?php

namespace App\Controller;

use App\Model\Emargement;
use App\Model\Personne;
use App\Service\ChevaletPDFMaker;
use App\Service\ChevaletsExcelParser;
use App\Service\EmargementExcelParser;
use App\Service\EmargementPDFMaker;
use PhpOffice\PhpWord\PhpWord;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Model\Chevalet;
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

            if ($data['document'] == 'chevalets') {
                // Créer un objet de type ChevaletPDFMaker
                $chevaletPDFMaker = new ChevaletPDFMaker();
                $chevaletsExcelParser = new ChevaletsExcelParser();
                $chevaletsExcelParser->setExcelFilePath($data['fichier']->getPathname());
                $chevaletsExcelParser->parseFile($chevaletPDFMaker);
                $content = $chevaletPDFMaker->getOutput();
                return new StreamedResponse(function () use ($content) {
                    echo $content;
                }, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="chevalets.pdf"'
                ]);
            } elseif ($data['document'] == 'emargement') {
                $emargement = New Emargement();
                $emargementPDFMaker = new EmargementPDFMaker();
                $emargementExcelParser = new EmargementExcelParser();
                $emargementExcelParser->setExcelFilePath($data['fichier']->getPathname());
                $emargementExcelParser->parse($emargement);

                // Appel de la méthode generatePDF avec les données extraites
                $content = $emargementPDFMaker->generatePDF($emargement);

                // Générer le PDF et le renvoyer en réponse
                return new StreamedResponse(function () use ($content) {
                    echo $content;
                }, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="emargement.pdf"'
                ]);
            } elseif ($data['document'] == 'compte_rendu') {
                // COPIER COLLER DU CR.PHP DE L'ANCIENNE APPLICATION
                // createSection est une fonction dépreciée par exemple
                // + remplacer les variables $rd $rhf $rt etc
                // $rt = objet de la réunion, $rd = date de réunion
                $PHPWord = new PHPWord();

                // New portrait section
                $section = $PHPWord->addSection();
                $excelFilePath = $data['fichier']->getPathname();

                // Charger le fichier Excel
                $reader = IOFactory::createReader('Xls'); // ou Xlsx selon le format du fichier Excel
                $spreadsheet = $reader->load($excelFilePath);
                // On définit les variables
                $sheetNames = ["Animateurs", "Participants", "Informations", "Ordres du jour", "Objectifs"];
                $titre = $date = $hfin = $hdebut = $objectif = $ordredujour = '';
                $personnes = [];
                $personnes['Animateurs'] = [];
                $personnes['Participants'] = [];

                // Add text elements
                $section->addImage('../public/assets/img/logo-ac-bx-fd-blc-2014.jpg', array('width'=>100, 'height'=>125, 'align'=>'left'));
                $section->addText('Bordeaux le '.$date,array(),array('align'=>'right'));

                $section->addText($titre, array('bold'=>true, 'size'=>16),array('align'=>'center'));
                $section->addText('Compte rendu de la réunion du '.$date.' (de '.$hdebut.' à '.$hfin.')', array('bold'=>true, 'size'=>12),array('align'=>'center'));

                $section->addTextBreak(1);
                $section->addText('Étaient présents :',array('bold'=>true,'underline'=> 'single'));
                $section->addText('  Animateurs :',array('bold'=>true,'italic'=>true));

                foreach ($animateurs as $personne) {
                    $section->addText('    - '.$personne.'.');
                }
                $section->addText('  Participants :',array('bold'=>true,'italic'=>true));
                foreach ($participants as $personne) {
                    $section->addText('    - '.$personne.'.');
                }
                $section->addTextBreak(1);
                $section->addText('Étaient absents :',array('bold'=>true,'underline'=> 'single'));
                $section->addTextBreak(1);
                $section->addText('Étaient excusés :',array('bold'=>true,'underline'=> 'single'));
                $section->addTextBreak(1);

                $section->addText('  Objectifs :',array('bold'=>true,'italic'=>true));
                $j=1;
                foreach ($objectifs as $objectif) {
                    $section->addText('   '.$j.' '.$objectif);
                    $j++;
                }
                $section->addTextBreak(1);
                $section->addText('   '.$j.' Ordre du jour',array('bold'=>true,'italic'=>true));
                $k=1;
                foreach ($ordredujour as $odj) {
                    $section->addText('  - '.$j.'.'.$k.' '.$odj);
                    $k++;
                }
                $section->addTextBreak(1);

                $fileName = 'CR.docx';
                $PHPWord->save($fileName, 'Word2007');
                readfile($fileName);
                unlink($fileName);
                // $objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007'); => Ancienne ligne corrigée
                //$objWriter->save('tmp/CR.docx');
                //header("location:tmp/CR.docx");
//                $temp_file = tempnam(sys_get_temp_dir(), 'CompteRendu');
//                $objWriter->save($temp_file);
                header('Content-Type: application/vnd.ms-word');
                header('Content-Transfer-Encoding: binary');
                header("Content-Disposition: attachment; filename=CompteRendu.docx");

//                unlink($temp_file);
            }
        }

        // Renvoyer le formulaire à la vue
        return $this->render('upload/upload.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}