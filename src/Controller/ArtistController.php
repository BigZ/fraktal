<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Service\Fraktal;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArtistController
{
    private $fraktal;

    public function __construct(Fraktal $fraktal)
    {
        $this->fraktal = $fraktal;
    }

    /**
     * @Route("/artists")
     */
    public function getArtists()
    {
        return new Response(
            'coucou'
        );
    }

    /**
     * @Route("/artists/{id}")
     */
    public function getArtist(Artist $artist, Request $request)
    {
        $resource = $this->fraktal->transform($artist, $request);

        return new Response($this->fraktal->serialize($resource));
    }
}