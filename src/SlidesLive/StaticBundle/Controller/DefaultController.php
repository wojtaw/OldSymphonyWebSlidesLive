<?php

namespace SlidesLive\StaticBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    
    public function indexAction() {
		$selectedPresentations = $this->getDoctrine()->getRepository('SlidesLiveBundle:HomepageBox')->findPublicPresentationBoxes();
		$categoryPositions = $this->generateRandomPositions(count($selectedPresentations));
        $this->data['presentationBoxes'] = $selectedPresentations;	
        $this->data['totalTiles'] = count($categoryPositions)+count($selectedPresentations);
        $this->data['categoryPositions'] = $categoryPositions;	
        return $this->render('StaticBundle:Homepage:index.html.twig', $this->data);
    }
    
    public function tourAction() {
        return $this->render('StaticBundle:Homepage:tour.html.twig');
    }
    
    public function aboutAction() {
        return $this->render('StaticBundle:Homepage:about.html.twig');
    }
    
    public function termsAction() {
        return $this->render('StaticBundle:Homepage:terms.html.twig');
    }
    
    public function policyAction() {
        return $this->render('StaticBundle:Homepage:policy.html.twig');
    }
	
    public function thankyouAction() {		
        return $this->render('StaticBundle:Homepage:thankYouDownload.html.twig');
    }	
	
	public function generateRandomPositions($numberOfTiles){
		$numberOfCategories = 5;
		$totalTiles = $numberOfTiles+$numberOfCategories;
		$categoryPositions = array();
		//Count create random positions
		for ($i=1; $i<=$numberOfCategories; $i++){
			$randomValue = rand(1, $totalTiles);
			if (in_array($randomValue, $categoryPositions)){
				$i--;
			} else {
				$categoryPositions[$i] = $randomValue;				
			}
		}	
		return $categoryPositions;
	}
	
    
// --------EXAMPLE ACCOUNT -----------------------------------------------------

    public function exampleAction() {		
      return $this->render('StaticBundle:Example:example.html.twig');
    }       

}
