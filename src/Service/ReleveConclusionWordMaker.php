<?php

namespace App\Service;

use App\Model\Reunion;
use PhpOffice\PhpWord\PhpWord;

class ReleveConclusionWordMaker
{
    const TMPFILENAME = 'releve-conclusion.docx';
    private PHPWord $phpWord;

    private Reunion $reunion;

    public function __construct(Reunion $reunion)
    {
        $this->phpWord = new PhpWord();
        $this->setReunion($reunion);
    }

    public function makeWord(): string
    {
        $section = $this->getPhpWord()->addSection();

        // Add text elements
        $section->addImage('../public/assets/img/logo-ac-bx-fd-blc-2014.jpg', array('width' => 100, 'height' => 125, 'align' => 'left'));
        $section->addText('Bordeaux le ' . $this->getReunion()->getDate(), array(), array('align'=>'right'));

        $section->addText($this->getReunion()->getObjet(), array('bold'=>true, 'size'=>16),array('align'=>'center'));
        $section->addText('Relevé de conclusions de la réunion du ' . $this->getReunion()->getDate() . ' (de ' . $this->getReunion()->getHeureDebut() . ' à ' . $this->getReunion()->getHeureFin() . ')', array('bold'=>true, 'size'=>12),array('align'=>'center'));

        $section->addTextBreak(1);
        $section->addText('  Objectifs :', array('bold' => true, 'italic' => true));
        if(count($this->getReunion()->getObjectifs()) > 0) {
            foreach ($this->getReunion()->getObjectifs() as $numeroObjectif => $objectif) {
                $section->addText('   ' . $numeroObjectif + 1 . ' ' . $objectif);
            }
        }

        if(count($this->getReunion()->getOrdresDuJour()) > 0){
            $premierNumeroOrdreDuJour = count($this->getReunion()->getObjectifs()) + 1;
            $section->addTextBreak(1);
            $section->addText('   ' . $premierNumeroOrdreDuJour . ' Ordre du jour', array('bold' => true, 'italic' => true));
            foreach ($this->getReunion()->getOrdresDuJour() as $secondNumeroOrdreDuJour => $ordreDuJour) {
                $section->addText('  - ' . $premierNumeroOrdreDuJour . '.' . $secondNumeroOrdreDuJour+1 . ' ' . $ordreDuJour);
            }
        }
        $section->addTextBreak(1);

        $this->getPhpWord()->save(self::TMPFILENAME, 'Word2007');

        $content = file_get_contents(self::TMPFILENAME);
        unlink(self::TMPFILENAME);
        return $content;
    }

    public function getPhpWord(): PhpWord
    {
        return $this->phpWord;
    }

    public function setPhpWord(PhpWord $phpWord): void
    {
        $this->phpWord = $phpWord;
    }

    public function getReunion(): Reunion
    {
        return $this->reunion;
    }

    public function setReunion(Reunion $reunion): void
    {
        $this->reunion = $reunion;
    }
}