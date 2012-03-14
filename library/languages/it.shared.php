<?php
/*
 * feindura - Flat File Content Management System
 * Copyright (C) Fabian Vogelsteller [frozeman.de]
 * Traslated in Italian By Raffaele Panariello [Social Service] unuomoinblues@gmail.com
 * This program is free software;
 * you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not,see <http://www.gnu.org/licenses/>.
 */
/**
 * ITALIAN (IT) language-file for the feindura CMS (FRONTEND and BACKEND)
 * 
 * need a RETURN $frontendLangFile; at the END
 */


// -> GENERAL <-

$sharedLangFile['HEADER_BUTTON_LOGOUT']                     = 'Disconnetti::Clicca qui per uscire da feindura e rifare il login.';
$sharedLangFile['sortablePageList_functions_startPage']     = 'Imposta Come Pagina Iniziale';
$sharedLangFile['sortablePageList_functions_startPage_set'] = 'Pagina Iniziale';

/* date texts */

$sharedLangFile['DATE_TEXT_YESTERDAY']                      = 'Ieri';
$sharedLangFile['DATE_TEXT_TODAY']                          = 'Oggi';
$sharedLangFile['DATE_TEXT_TOMORROW']                       = 'Domani';


// -> SEARCH

$sharedLangFile['SEARCH_TITLE']                             = 'Ricerca pagine';
$sharedLangFile['SEARCH_TITLE_RESULTS']                     = 'Risultati della ricerca per';
$sharedLangFile['SEARCH_TEXT_MATCH_CATEGORY']               = 'categoria';
$sharedLangFile['SEARCH_TEXT_MATCH_SEARCHWORDS']            = 'ricerca parole';
$sharedLangFile['SEARCH_TEXT_MATCH_TAGS']                   = 'Tags';
$sharedLangFile['SEARCH_TEXT_RESULTS']                      = 'risultati';
$sharedLangFile['SEARCH_TEXT_TIME_1']                       = 'in'; // 12 matches in 0.32 seconds
$sharedLangFile['SEARCH_TEXT_TIME_2']                       = 'secondi';


// -> ERROR TEXTs

$sharedLangFile['errorWindow_h1']                           = 'Si &#232; verificato un errore!';
$sharedLangFile['sortablePageList_setStartPage_error_save'] = '<b>Impossibile impostare la pagina iniziale.</b>';
$sharedLangFile['EDITOR_savepage_error_save']               = '<b>La pagina non pu� essere salvata.</b>';
$sharedLangFile['ADMINSETUP_ERROR_PHPVERSION']              = 'ERRORE<br /><br /><span class="logoname">fein<span>dura</span></span> richiede almeno la versione PHP 5.2.3'; // PHP 5.2.3

// -----------------------------------------------------------------------------------------------
// RETURN ****************************************************************************************
// -----------------------------------------------------------------------------------------------
return $sharedLangFile;

?>