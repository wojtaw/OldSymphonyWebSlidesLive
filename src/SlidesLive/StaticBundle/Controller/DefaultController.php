<?php

namespace SlidesLive\StaticBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;

use SlidesLive\SlidesLiveBundle\Entity\Subscribe;
use SlidesLive\SlidesLiveBundle\Form\SubscribeType;

class DefaultController extends Controller
{

    public function indexAction() {
		$request = $this->getRequest();
		$cookies = $request->cookies;
        $em = $this->getDoctrine()->getEntityManager();

        $subscribe = new Subscribe();

        if ($cookies->has('downloadEmail'))
			$subscribe->setEmail($cookies->get('downloadEmail'));

        $downloadForm = $this->createForm(new SubscribeType(), $subscribe);
        if ($request->getMethod() == 'POST')
		{
		  $response = $this->redirect($this->generateUrl('thankyou'));

		  $downloadForm->bindRequest($request);
		  if ($downloadForm->isValid())
		  {
			$em->merge($subscribe);
			$em->flush();

			$response->headers->setCookie(new Cookie('downloadEmail', $subscribe->getEmail()));
		  }

		  return $response;
		}

		$selectedPresentations = $em->getRepository('SlidesLiveBundle:HomepageBox')->findPublicPresentationBoxes();
		
			

        $data['presentationBoxes'] = $selectedPresentations;
        $data['categories'] = $em->getRepository('SlidesLiveBundle:Category')->listAllCategories();		
        $data['categoryPositions'] = $this->generateRandomPositions(count($selectedPresentations),count($data['categories']));
        $data['siteVersion'] = $this->runABTest();		
		//$data['downloadForm'] = $downloadForm->createView();

        return $this->render('StaticBundle:Homepage:index.html.twig', $data);
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

	private function generateRandomPositions($numberOfTiles,$numberOfCategories){
		if($numberOfTiles < $numberOfCategories) return 0;

		$categoryPositions = array();
		//Create random positions, not two same
		for ($i=1; $i<=$numberOfCategories; $i++){
			$randomValue = rand(1, $numberOfTiles);
			if (in_array($randomValue, $categoryPositions)){
				$i--;
			} else {
				$categoryPositions[$i] = $randomValue;
			}
		}
		return $categoryPositions;
	}
	
	protected function runABTest(){
		//Calculating sum of IP address and deciding type of user
			$ipAddress = $_SERVER['REMOTE_ADDR'];
			$this->testPrint("IP Address: ".$ipAddress."\n");			
			$ipAddressArray = explode(".", $ipAddress);
			//Diversion
			if((array_sum($ipAddressArray) % 2) == 0) {
				$this->displayVersion("A");
				return "A";
			} else {
				displayVersion("B");
				return "B";				
			}
	
	}
	
	protected function displayVersion($version){
			if($version == "A"){
				$this->testPrint("Running version A");
			}else if($version == "B"){
				$this->testPrint("Version B");								
			}else{
				$this->testPrint("Undefined version");				
			}
	}
	
	protected function testPrint($message){
		echo $message;
	}


// --------EXAMPLE ACCOUNT -----------------------------------------------------

    public function exampleAction() {
      return $this->render('StaticBundle:Example:example.html.twig');
    }

}
