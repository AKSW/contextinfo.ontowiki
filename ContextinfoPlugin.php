<?php
/**
 * This file is part of the {@link http://ontowiki.net OntoWiki} project.
 *
 * @copyright Copyright (c) 2013, {@link http://aksw.org AKSW}
 * @author    Norman Radtke <norman.radtke@gmail.com>, Andreas Nareike <nareike@informatik.uni-leipzig.de>, Natanael Arndt <arndtn@gmail.com>
 * @license   http://opensource.org/licenses/gpl-license.php GNU General Public License (GPL)
 */

/**
 * @category   OntoWiki
 * @package    OntoWiki_Extensions_Contextinfo
 */
class ContextinfoPlugin extends OntoWiki_Plugin
{
    private $_properties = null;

    public function init()
    {
        $configValues      = $this->_privateConfig->properties->toArray();
        $this->_properties = array_combine($configValues, $configValues);

        return $this->_properties;
    }

    public function onPropertiesActionData($event)
    {
        $eventPredicates = $event->predicates;
        foreach ($eventPredicates as $graphUri => $predicates) {
            foreach ($predicates as $pkey => $predicate) {
                $predicate['title'] = 'blub';
                $predicates[$pkey] = $predicate;
            }
            $eventPredicates[$graphUri] = $predicates;
        }
        $event->predicates = $eventPredicates;
        return true;
    }
}
