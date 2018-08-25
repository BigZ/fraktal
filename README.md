### Doc flow
- explain the purpose and portability
- most common use case, symfony + json api in 10 minutes
- links to advanced docs: symfony guide, laravel guide, Plain Old Php


### Use without config

```
<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Service\Fraktal;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArtistController
{
	private $doctrineReader;
	
    /**
     * @Route("/artists/{id}")
     */
    public function getArtist(Artist $artist, Request $request)
    {
    	$reader = new DoctrineAnnotationReader($this->doctrineReader);
    	$entityTransformer = new EntityTransformer($reader); // or any other ObjectReaderInterface 
    	$fraktal = new Fraktal($entityTransformer); // or any TransformerAbstract
        $resource = $this->fraktal->transform($artist, $request);

        return new Response(
            $this->fraktal->serialize(
                $resource,
                Fraktal::SPEC_JSONAPI,
                Fraktal::FORMAT_JSON
            ),
            200,
            ['Content-Type' => 'application/vnd.api+json']
        );
    }
}
```

### Todo
- includes
- filters
- headers map according to spec+serialisation schema
- eager fetch helper interface + orm
- global error catcher that display a json error
- paramconverter for injecting transformed Collection or Item
- response listener for serialization
- config
- split in project
- suggest fos rest and allow swagger dump if installed
- create a command to automatically add annotations / confg on entities