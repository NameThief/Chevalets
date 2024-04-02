<?php

namespace App\Controller;

use App\Service\ChevaletPDFMaker;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Model\Chevalet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\Filesystem\Filesystem;
use FPDF;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Reader\Xls;

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
                // Créer un objet de type FPDF
                $pdf = new FPDF();
                // Créer un objet de type ChevaletPDFMaker
                $chevaletPDFMaker = new ChevaletPDFMaker();
                // Charger dans le fichier excel les feuilles "Animateurs" et "Participants"
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
                $reader->setLoadSheetsOnly(["Animateurs", "Participants"]);
                $spreadsheet = $reader->load($data['fichier']->getPathname());

                // Pour chaque feuille du fichier excel Mettre les données dans un tableau $data
                foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
                    $data = $worksheet->toArray(null, true, true, true);
                    // Exclure la première ligne
                    $data = array_slice($data, 1);
                    foreach ($data as $row) {
                        // Accédez aux clés uniquement si elles existent
                        if (!empty($row['B']) && !empty($row['C']) && !empty($row['E'])) {
                            $chevalet = new Chevalet();
                            $chevalet->setNom(mb_convert_encoding($row['B'], "UTF-8", mb_detect_encoding($row['B'])));
                            $chevalet->setPrenom($row['C']);
                            $chevalet->setFonction($row['E']);
                            // Ajout du chevalet au PDF en utilisant l'objet $chevaletMaker
                            $chevaletPDFMaker->addChevaletToPDF($pdf, $chevalet);
                        }
                        // Le reader arrête de lire le fichier excel quand il rencontre une ligne vide
                        else {
                            break;
                        }
                    }
                }
                // Générer le PDF et le renvoyer en réponse
                return new StreamedResponse(function () use ($pdf) {
                    echo $pdf->Output();
                }, 200, [

                    'Content-Type' => 'application/pdf; charset=utf-8',
                    'Content-Disposition' => 'attachment; filename="chevalets.pdf"'
                ]);
            }
        }
        // Renvoyer le PDF en réponse

        return $this->render('upload/upload.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}






