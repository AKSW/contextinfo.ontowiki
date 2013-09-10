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
                $comment = $this->getPredicateComment($predicate['uri']);
                if (isset($comment)) {
                    $predicate['title'] = '<span title="' . $comment . '">' . $predicate['title'] . '</span>';
                }
                $predicates[$pkey] = $predicate;
            }
            $eventPredicates[$graphUri] = $predicates;
        }
        $event->predicates = $eventPredicates;
        return true;
    }

    /**
     * 
     */
    public function getPredicateComment($predicateUri)
    {
        $owApp = OntoWiki::getInstance();
        $selectedModel = $owApp->selectedModel;

        /*
         * this will return only the first comment type found in the
         * the doap.n3
         * TODO: move the query out of the foreach loop and query
         *       all properties at once
         */
        foreach ($this->_privateConfig->properties->toArray() as $property) {
            $query = 'PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>' . PHP_EOL;
            $query.= 'SELECT ?comment WHERE {' . PHP_EOL;
            $query.= '       <' . $predicateUri . '> <' . $property . '> ?comment' . PHP_EOL;
            $query.= '}';
            $result = $selectedModel->sparqlQuery($query);

            if (count($result) > 0) {
                $resultstrings = array();
                foreach ($result as $res) {
                    $resultstrings[] = $res['comment'];
                }
                return join(PHP_EOL, $resultstrings);
                return $result[0]['comment'];
            }
        }
    }

}
