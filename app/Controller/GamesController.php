<?php

App::uses('HttpSocket', 'Network/Http');
App::uses('AppController', 'Controller');


/**
 * Games controller
 * @package app.Controller
 */
class GamesController extends AppController {

/**
 * This controller does not use a model
 *
 * @var array
 */
    public $uses = array();
/**
 * Components used by controller
 *
 * @var array
 */
    public $components = array('RequestHandler');

/**
 * display sorted cards 
 * @return void
 */
    public function getSortedCards(){
        $cardServiceResponse =  $this->_requestCardsService();
        $sortedCard = $this->_sortCards($cardServiceResponse['data']);
        $result = $this->_validateSortedCards($sortedCard, $cardServiceResponse['exerciceId']);
        $this->set(compact('sortedCard', 'result'));
    }

/**
 * request cards validation service
 * @param Array $sortedCard array of unordered cards
 * @param String $exerciceId exercice identifier
 * @return Bool true on success
 * @throws SocketException
 */
    private function _validateSortedCards(Array $sortedCard, $exerciceId){
        $body = json_encode(array('cards' => $sortedCard));
        
        $url =  Configure::read('Service.postCardsUrl');
        $request = array(
            'header' => array('Content-Type' => 'application/json'),
        );

        $datum = array();
        // post data to cards validation services
        $httpSocket = new HttpSocket();
        $response = $httpSocket->post($url.$exerciceId, $body, $request);

        if ($response->code == 200) {
            return true;
        }

        if ($response->code == 406) {
            return false;
        }

        throw new SocketException('Bad request or service unavailable');
    }

/**
 * request the cards service
 * @return array of cards on success
 * @throws SocketException
 * @throws BadRequestException
 */
    private function _requestCardsService(){
         
        
        $url =  Configure::read('Service.getCardsUrl');
        $datum = array();
        // remotely get the information from cards services
        $httpSocket = new HttpSocket();
        $response = $httpSocket->get($url);
        
        if ($response->code != 200) {
            throw new SocketException('Services cartes inaccessible');
        }

        $datum = json_decode($response->body, true);

        if (empty($datum['data']['cards']) || 
            empty($datum['data']['categoryOrder']) || 
            empty($datum['data']['valueOrder']) || 
            empty($datum['exerciceId'])) {
            throw new BadRequestException("Format icorrect");
        }

        return $datum;
    }
    
/**
 * order Cards 
 * @param Array $datum unordered cards
 * @return Array of sorted cards
 */
    private function _sortCards(Array $datum){
        $unorderedCards = $datum['cards'];
        $categoryOrder = $datum['categoryOrder'];
        $valueOrder = $datum['valueOrder'];
        
        $genericOrder = array();
        //créer un tableau d'order générique
        foreach ($categoryOrder as $category) {
            foreach ($valueOrder as $value) {
                $genericOrder[] = array("category" => $category, "value" => $value);
            }
        }

        $ordered = array();
        // créer un nouveau tableau dont les clés 
        //sont les clés de tabeau d'order générique
        foreach ($unorderedCards as $unorderedCard) {
            //Chercher la position de la carte non ordonnée dans 
            //le tabeau d'order générique
            $index = array_search($unorderedCard, $genericOrder);
            // remplir le nouveau tableau  
            $ordered[$index] = $unorderedCard;
        };
        //ordonner le nouveau tableau par clés
        ksort($ordered);
        //regulariser les clés du nouveau tableau
        $ordered = array_values($ordered);

        return $ordered;
    }

}
