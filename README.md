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
    
    public function getArtists(Request $request)
    {
        $artists = $this->fraktal->getPaginatedCollection(Artist::class, $request);
        $paginatorAdapter = $this->fraktal->getPaginationAdapter($request);
        $resource = $this->fraktal->transform($artists, $request);
        $resource->setPaginator($paginatorAdapter);

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

## planned config for bundle
```
format_listner: off|force|header|file_extension -> default to header 
	(forces output schema to the default one or let the user choose which format he wents by providing 
	either an accept header or a file extension. You can also disable automatic serialization by choosing
	the "off" value. you will then have to call the serializer yourself like shown in the above doc)
format_default: jsonapi|hal|json|xml|array -> default to json
format_errors: true|false -> default false in dev, true in prod (formats error in the demanded format)

entity_reader: doctrineorm|doctrineodm|... -> default to doctrineorm

```

### Todo
- ~sparse fieldset
- ~advanced filter operators such as like,in,between,
- !headers map according to spec+serialisation schema
- ~eager fetch helper interface + orm
- !global error catcher that display a json error
- ??paramconverter for injecting transformed Collection or Item
- ?serialization groups
- !response listener for serialization
- !config
- !!split in project
- ?suggest fos rest and allow swagger dump if installed
- ?create a command to automatically add annotations / confg on entities