<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookingRepository")
 * 
 * cette entité doit gérer son cycle de vie (A chaque fois qu'on enregistre une reservation certaine chose soient mise en place automatiquement )
 * @ORM\HasLifecycleCallbacks()
 */
class Booking
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $booker;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ad", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ad;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Date(message="Attention, la date d'arrivée doit étre au bon format !")
     * @Assert\GreaterThan("today")
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Date(message="Attention, la date de depart doit étre au bon format !")
     */
    private $endDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;


    /**
     * Callback appellé appelé à chaque fois qu'on crée une réservation 
     * 
     * @ORM\PrePersist
     *
     * @return void
     */
    public function prePersist(){
        if(empty($this->createdAt)){
            //si ma date de creation est vide alors une date sera créer à l'instant
            $this->createdAt = new \DateTime();
        }

        if(empty($this->amount)){
            //prix de l'annonce * nombre de jour(getDuration)
            $this->amount = $this->ad->getPrice() * $this->getDuration(); 
        }
    }

    public function isBookableDates(){
        // il faut connaitre les dates qui sont impossible pour l'annponce
        $notAvailableDays = $this->ad->getNotAvailableDays();
        //il faut comparer les dates choisies avec les date impossibles
        $bookingDays = $this->getDays();

        
        $formatDay = function($day){
            return $day->format('Y-m-d');
        };

        //Tableau des chaine de caractère de mes journnées
        $days         = array_map($formatDay, $bookingDays);

        $notAvailable = array_map($formatDay, $notAvailableDays);

        foreach($days as $day){
            if(array_search($day, $notAvailable) !== false) return false;
        }
        return true;
    }

    /**
     * Permet de reccuperer un tableau qui correspond à ma reservation
     *
     * @return array un tableau d'objets DateTime representant les jours de la reservation
     */
    public function getDays(){
       $resultat = range(
           $this->startDate->getTimestamp(),
           $this->endDate->getTimestamp(),
           24 *60 * 60 
       );
       $days = array_map(function($dayTimestamp){
           return new \DateTime(date('Y-m-d', $dayTimestamp));
       },$resultat);
       return $days;
    }

    public function getDuration(){
        //difference entre endDate et startDate
        $diff = $this->endDate->diff($this->startDate);
        return $diff->days;

    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBooker(): ?User
    {
        return $this->booker;
    }

    public function setBooker(?User $booker): self
    {
        $this->booker = $booker;

        return $this;
    }

    public function getAd(): ?Ad
    {
        return $this->ad;
    }

    public function setAd(?Ad $ad): self
    {
        $this->ad = $ad;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
