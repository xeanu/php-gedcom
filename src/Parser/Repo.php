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

class Repo extends \Parser\Component
{
    public static function parse(\Gedcom\Parser $parser)
    {
        $record = $parser->getCurrentLineRecord();
        $depth = (int) $record[0];
        if (isset($record[1])) {
            $identifier = $parser->normalizeIdentifier($record[1]);
        } else {
            $parser->skipToNextLevel($depth);

            return null;
        }

        $repo = new \Record\Repo();
        $repo->setRepo($identifier);

        $parser->getGedcom()->addRepo($repo);

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
                case 'NAME':
                    $repo->setName(trim($record[2]));
                    break;
                case 'ADDR':
                    $addr = \Parser\Addr::parse($parser);
                    $repo->setAddr($addr);
                    break;
                case 'PHON':
                    $repo->addPhon(trim($record[2]));
                    break;
                case 'EMAIL':
                    $repo->addEmail(trim($record[2]));
                    break;
                case 'FAX':
                    $repo->addFax(trim($record[2]));
                    break;
                case 'WWW':
                    $repo->addWww(trim($record[2]));
                    break;
                case 'NOTE':
                    if ($note = \Parser\NoteRef::parse($parser)) {
                        $repo->addNote($note);
                    }
                    break;
                case 'REFN':
                    $refn = \Parser\Refn::parse($parser);
                    $repo->addRefn($refn);
                    break;
                case 'RIN':
                    $repo->setRin(trim($record[2]));
                    break;
                case 'CHAN':
                    $chan = \Parser\Chan::parse($parser);
                    $repo->setChan($chan);
                    break;
                default:
                    $parser->logUnhandledRecord(get_class().' @ '.__LINE__);
            }

            $parser->forward();
        }

        return $repo;
    }
}
