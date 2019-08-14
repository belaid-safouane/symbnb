<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Entity\Image;
use App\Repository\AdRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AdController extends Controller
{
    /**
     * @Route("/ads", name="ads_index")
     */
    public function index(AdRepository $repo)
    {
        $ads = $repo->findAll();
        return $this->render('ad/index.html.twig', [
            'ads'=>$ads
        ]);
    }

    /**
     * @Route("/ads/new", name="ads_create")
     * 
     * @return response
     */
    public function create(Request $request ,ObjectManager $manager){
        $ad = new Ad();

        $form = $this->createForm(AdType::class,$ad);
        $form->handleRequest($request);
        
      

        if($form->isSubmitted() && $form->isValid()){
            foreach($ad->getImages() as $image){
                $image->setAd($ad);
                $manager->persist($image);
            }

            
            $ad->setAuthor($this->getUser());

            $manager->persist($ad);
            $manager->flush();
            
            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a été bie enregistrée !"
            );

            return $this->redirectToRoute('ads_show',[
                'slug' => $ad->getSlug()
            ]);
        }

        return $this->render('ad/new.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
 * Permet d'afficher une seule annonce
 * 
 * @Route ("/ads/{slug}", name="ads_show")
 * 
 * @return Response
 */
public function show(Ad $ad){
    return $this->render('ad/show.html.twig', [
        'ad'=>$ad
    ]);
 }


    /**
 * Permet d'afficher le formulaire d'édition
 * 
 * @Route("/ads/{slug}/edit",name="ads_edit")
 * 
 * @return Response
 */
    public function edit(Ad $ad,Request $request,ObjectManager $manager){
        $form = $this->createForm(AdType::class,$ad);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            foreach($ad->getImages() as $image){
                $image->setAd($ad);
                $manager->persist($image);
            }


            $manager->persist($ad);
            $manager->flush();
            
            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a été bie modifiée !"
            );

            return $this->redirectToRoute('ads_show',[
                'slug' => $ad->getSlug()
            ]);
        }

       return $this->render('ad/edit.html.twig', [
        'form' => $form->createView(),
        'ad'=> $ad
    ]);
    }
    


   
}
