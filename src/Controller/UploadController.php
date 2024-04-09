<?php

namespace App\Controller;

use App\Service\ChevaletPDFMaker;
use App\Service\EmargementPDFMaker;
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
                            $chevalet->setNom(($row['B']));
                            $chevalet->setPrenom(($row['C']));
                            $chevalet->setFonction(($row['E']));
                            // Ajout du chevalet au PDF en utilisant l'objet $chevaletMaker
                            $chevaletPDFMaker->addChevaletToPDF($chevalet);
                        } // Le reader arrête de lire le fichier excel quand il rencontre une ligne vide
                        else {
                            break;
                        }
                    }
                }
                // Générer le PDF et le renvoyer en réponse
                return new StreamedResponse(function () use ($chevaletPDFMaker) {
                    echo $chevaletPDFMaker->getOutput();
                }, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="chevalets.pdf"'
                ]);
            } elseif ($data['document'] == 'emargement') {
                $emargementPDFMaker = new EmargementPDFMaker();
                // Chemin vers le fichier Excel

                $excelFilePath = $data['fichier']->getPathname();

                // Charger le fichier Excel
                $reader = IOFactory::createReader('Xls'); // ou Xlsx selon le format du fichier Excel
                $spreadsheet = $reader->load($excelFilePath);

                // Accéder aux données des feuilles de calcul "Animateurs" et "Participants"
                // Supposons que vous extrayez les données des cellules B, C, et E de chaque ligne
                $sheetNames = ["Animateurs", "Participants", "Informations"];
                $titre = $date = $hfin = $hdebut = '';
                $personnes = [];
                $personnes['animateurs'] = [];
                $personnes['participants'] = [];

                foreach ($sheetNames as $sheetName) {
                    $worksheet = $spreadsheet->getSheetByName($sheetName);
                    $highestRow = $worksheet->getHighestDataRow();


                    // Extraction des données selon la feuille de calcul
                    switch ($sheetName) {

                        case "Animateurs":
                            $nbAnimateurs = 0;
                            for ($row = 2; $row <= $highestRow; $row++) {
                            // Vérifier si les colonnes B, C et E ne sont pas vides avant d'extraire les données
                            if (!empty($worksheet->getCell('B' . $row)->getValue()) && !empty($worksheet->getCell('C' . $row)->getValue()) && !empty($worksheet->getCell('E' . $row)->getValue())) {
                                $personnes['animateurs'][$nbAnimateurs]['nom'] = $worksheet->getCell('B' . $row)->getValue();
                                $personnes['animateurs'][$nbAnimateurs]['prenom'] = $worksheet->getCell('C' . $row)->getValue();
                                $personnes['animateurs'][$nbAnimateurs]['fonction'] = $worksheet->getCell('E' . $row)->getValue();
                                $personnes['animateurs'][$nbAnimateurs]['civilite'] = $worksheet->getCell('A' . $row)->getValue();
                                $personnes['animateurs'][$nbAnimateurs]['service'] = $worksheet->getCell('D' . $row)->getValue();
                                $nbAnimateurs++;
                            }
                        }
                        break;

                        case "Participants":
                            for ($row = 2; $row <= $highestRow; $row++) {
                            // Vérifier si les colonnes B, C et E ne sont pas vides avant d'extraire les données
                            if (!empty($worksheet->getCell('B' . $row)->getValue()) && !empty($worksheet->getCell('C' . $row)->getValue()) && !empty($worksheet->getCell('E' . $row)->getValue())) {
                                $personnes['participants']['noms'][] = $worksheet->getCell('B' . $row)->getValue();
                                $personnes['participants']['prenoms'][] = $worksheet->getCell('C' . $row)->getValue();
                                $personnes['participants']['fonctions'][] = $worksheet->getCell('E' . $row)->getValue();
                                $personnes['participants']['civilites'][] = $worksheet->getCell('A' . $row)->getValue();
                                $personnes['participants']['services'][] = $worksheet->getCell('D' . $row)->getValue();
                            }
                        }
                        break;

                        // Déterminer le type en fonction de $sheetName
                        case "Informations":
                            // Assurez-vous d'ajuster les lettres des colonnes selon la disposition réelle de vos données
                            // Vérifier si la cellule B1 contient des données avant d'extraire les titres
                            if (!empty($worksheet->getCell('B1')->getValue())) {
                                $titre = $worksheet->getCell('B1' )->getValue(); // Première cellule de la plage de données pour les titres
                            }
                            // Vérifier si la cellule B2 contient des données avant d'extraire les dates
                            if (!empty($worksheet->getCell('B2')->getValue())) {
                                $date = $worksheet->getCell('B2')->getValue(); // Première cellule de la plage de données pour les dates
                            }
                            if (!empty($worksheet->getCell('B3')->getValue())){
                                $hdebut = $worksheet->getCell('B3')->getValue(); // Première cellule de la plage de données pour l'heure de début
                            }
                            if (!empty($worksheet->getCell('B4')->getValue())){
                                $hfin = $worksheet->getCell('B4')->getValue(); // Première cellule de la plage de données pour l'heure de fin
                            }

                            break;
                        default:
                            // Ne rien faire pour les autres feuilles de calcul
                            break;
                    }
                }

                // Maintenant, vous pouvez appeler la fonction emargement avec les données extraites

                // Appel de la méthode generatePDF avec les données extraites
                $pdfContent = $emargementPDFMaker->generatePDF($personnes, $titre, $date, $hdebut, $hfin);
                // Générer le PDF et le renvoyer en réponse
                return new StreamedResponse(function () use ($pdfContent) {
                    echo $pdfContent;
                }, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="emargement.pdf"'
                ]);
            }
        }

        // Renvoyer le formulaire à la vue
        return $this->render('upload/upload.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}