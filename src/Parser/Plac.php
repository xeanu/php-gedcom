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

class Plac extends \Parser\Component
{
    public static function parse(\Gedcom\Parser $parser)
    {
        $record = $parser->getCurrentLineRecord();
        $depth = (int) $record[0];
        if (isset($record[2])) {
            $_plac = trim($record[2]);
        } else {
            $parser->skipToNextLevel($depth);

            return null;
        }

        $plac = new \Record\Plac();
        $plac->setPlac($_plac);

        $parser->forward();

        while (!$parser->eof()) {
            $record = $parser->getCurrentLineRecord();
            $currentDepth = (int) $record[0];
            $recordType = strtoupper(trim($record[1]));

            if ($currentDepth <= $depth) {
                $parser->back();
                break;
            }

            switch ($recordType) {
                case 'FORM':
                    $plac->setForm(trim($record[2]));
                    break;
                case 'FONE':
                    $fone = \Parser\Plac\Fone::parse($parser);
                    $plac->setFone($fone);
                    break;
                case 'ROMN':
                    $romn = \Parser\Plac\Romn::parse($parser);
                    $plac->setRomn($romn);
                    break;
                case 'NOTE':
                    if ($note = \Parser\NoteRef::parse($parser)) {
                        $plac->addNote($note);
                    }
                    break;
                case 'MAP':
                    $map = \Parser\Plac\Map::parse($parser);
                    $plac->setMap($map);
                    break;
                default:
                    $parser->logUnhandledRecord(get_class().' @ '.__LINE__);
            }

            $parser->forward();
        }

        return $plac;
    }
}
