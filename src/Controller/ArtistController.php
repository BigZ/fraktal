<?php

namespace App\Controller;

use App\Entity\Artist;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Routing\Annotation\Route;
use WizardsRest\CollectionManager;

class ArtistController
{
    /**
     * @var CollectionManager
     */
    private $rest;

    /**
     * ArtistController constructor.
     * @param CollectionManager $rest
     */
    public function __construct(CollectionManager $rest)
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
