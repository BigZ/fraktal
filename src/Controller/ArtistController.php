<?php

namespace App\Controller;

use App\Entity\Artist;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Routing\Annotation\Route;
use WizardsRest\WizardsRest;

class ArtistController
{
    /**
     * @var WizardsRest
     */
    private $rest;

    /**
     * ArtistController constructor.
     * @param WizardsRest $rest
     */
    public function __construct(WizardsRest $rest)
    {
        $this->rest = $rest;
    }

    /**
     * @Route("/artists")
     */
    public function getArtists(ServerRequestInterface $request): \Traversable
    {
        return $this->rest->getPaginatedCollection(Artist::class, $request);
    }

    /**
     * @Route("/artists/{id}")
     */
    public function getArtist(Artist $artist): Artist
    {
        return $artist;
    }
}
