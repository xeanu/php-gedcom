<?php
/**
 * php-gedcom.
 *
 * php-gedcom is a library for parsing, manipulating, importing and exporting
 * GEDCOM 5.5 files in PHP 5.3+.
 *
 * @author          Kristopher Wilson <kristopherwilson@gmail.com>
 * @copyright       Copyright (c) 2010-2013, Kristopher Wilson
 * @license         MIT
 *
 * @link            http://github.com/mrkrstphr/php-gedcom
 */

namespace Parser;

class SourRef extends \Parser\Component
{
    public static function parse(\Gedcom\Parser $parser)
    {
        $record = $parser->getCurrentLineRecord();
        $depth = (int) $record[0];
        if (isset($record[2])) {
            $sour = new \Record\SourRef();
            $sour->setSour($parser->normalizeIdentifier($record[2]));
        } else {
            $parser->skipToNextLevel($depth);

            return null;
        }

        $parser->forward();

        while (!$parser->eof()) {
            $record = $parser->getCurrentLineRecord();
            $recordType = strtoupper(trim($record[1]));
            $currentDepth = (int) $record[0];

            if ($currentDepth <= $depth) {
                $parser->back();
                break;
            }

            switch ($recordType) {
                case 'PAGE':
                    $sour->setPage(trim($record[2]));
                    break;
                case 'EVEN':
                    $even = \Parser\SourRef\Even::parse($parser);
                    $sour->setEven($even);
                    break;
                case 'DATA':
                    $sour->setData(\Parser\SourRef\Data::parse($parser));
                    break;
                case 'TEXT':
                    $sour->setText($parser->parseMultiLineRecord());
                    break;
                case 'OBJE':
                    $obje = \Parser\ObjeRef::parse($parser);
                    if ($obje) {
                        $sour->addNote($obje);
                    }
                    break;
                case 'NOTE':
                    $note = \Parser\NoteRef::parse($parser);
                    if ($note) {
                        $sour->addNote($note);
                    }
                    break;
                case 'QUAY':
                    $sour->setQuay(trim($record[2]));
                    break;
                default:
                    $parser->logUnhandledRecord(get_class().' @ '.__LINE__);
            }

            $parser->forward();
        }

        return $sour;
    }
}
