<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 12/01/18
 * Time: 02:54
 */

namespace AppBundle\Tests\Controller\API;

use AppBundle\Controller\Api\ProgrammerController;
use AppBundle\Test\ApiTestCase;

class ProgrammerControllerTest extends ApiTestCase
{
    public function setup()
    {
        parent::setUp();
        $this->createUser('weaverryan');
    }
    public function testPOST()
    {

        $data = array(
            'nickname' => 'ObjectOrienter',
            'avatarNumber' => 5,
            'tagLine' => 'a test dev!'
        );
        //1 - Create a programmer resource
        $response = $this->client->post('/api/programmers', [
            'body' => json_encode($data)
        ]);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertStringEndsWith('/api/programmers/ObjectOrienter',$response->getHeader('Location'));
        $finishedData = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('nickname', $finishedData);
        $this->assertEquals('ObjectOrienter',$data['nickname']);
    }

    public function testGETProgrammer()
    {
        $this->createProgrammer(array(
            'nickname' =>'UnitTester',
            'avatarNumber' => 3,
        ));

        $response = $this->client->get('/api/programmers/UnitTester');
        $this->assertEquals(200,$response->getStatusCode());
        $this->asserter()->assertResponsePropertiesExist($response,array(
            'nickname',
            'avatarNumber',
            'powerLevel',
            'tagLine'
        ));

        $this->asserter()->assertResponsePropertyEquals($response,'nickname','UnitTester');
       $this->asserter()->assertResponsePropertyEquals($response,'_links.self',$this->adjustUri('/api/programmers/UnitTester'));



    }


    public function testGETProgrammerDeep()
    {
        $this->createProgrammer(array(
            'nickname' => 'UnitTester',
            'avatarNumber' => 3,
        ));

        $response = $this->client->get('/api/programmers/UnitTester?deep=1');
        $this->assertEquals(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyExists($response,'user.username');
    }

    public function testGETProgrammersCollections()
    {
        $this->createProgrammer(array(
            'nickname' =>'UnitTester',
            'avatarNumber' => 3,
        ));

        $this->createProgrammer(array(
            'nickname' =>'CowerCoder',
            'avatarNumber' => 5,
        ));

        $response = $this->client->get('/api/programmers');
        $this->debugResponse($response);
        $this->assertEquals(200,$response->getStatusCode());
        $this->asserter()->assertResponsePropertyIsArray($response,'items');
        $this->asserter()->assertResponsePropertyCount($response,'items',2);
        $this->asserter()->assertResponsePropertyEquals($response,'items[1].nickname','CowerCoder');
    }


    public function testGETProgrammersCollectionPaginated()
    {

       $this->createProgrammer(array(
            'nickname' => 'willnotmatch',
            'avatarNumber' => 3 ,
        ));
        for ($i=0;$i<25;$i++)
        {
            $this->createProgrammer(array(
                'nickname' =>'Programmer'.$i,
                'avatarNumber' => 3,
            ));
        }


        $response = $this->client->get('/api/programmers?filter=programmer');

        $this->assertEquals(200,$response->getStatusCode());

        $this->asserter()->assertResponsePropertyEquals(
            $response,
            'items[5].nickname',
            'Programmer5'
        );

        $this->asserter()->assertResponsePropertyEquals($response,
            'count',
            10
        );

        $this->asserter()->assertResponsePropertyEquals($response,
            'total',
            25
        );

        $this->asserter()->assertResponsePropertyExists($response,
            '_links.next'
        );

        $nextUrl = $this->asserter()->readResponseProperty($response,'_links.next');
        $response = $this->client->get($nextUrl);

        $this->assertEquals(200,$response->getStatusCode());

        $this->asserter()->assertResponsePropertyEquals($response,
            'items[5].nickname',
            'Programmer15'
        );

        $this->asserter()->assertResponsePropertyEquals($response,
            'count',
            10
        );

         $lastUrl = $this->asserter()->readResponseProperty($response,'_links.last');
        $response = $this->client->get($lastUrl);

        $this->assertEquals(200,$response->getStatusCode());

        $this->asserter()->assertResponsePropertyEquals($response,
            'items[4].nickname',
            'Programmer24'
        );
        $this->asserter()->assertResponsePropertyDoesNotExist($response,'items[5].nickname');
        $this->asserter()->assertResponsePropertyEquals($response,
            'count',
            5
        );


    }


    public function testPUTProgrammers()
    {
        $this->createProgrammer(array(
            'nickname' =>'CowerCoder',
            'avatarNumber' => 5,
            'tagLine' => 'a test dev!'
        ));
        $data = array(
            'nickname' => 'CowergirlCoder',
            'avatarNumber' => 2,
            'tagLine' => 'foo'
        );

        $response = $this->client->put('/api/programmers/CowerCoder',array(
            'body'=> json_encode($data)
        ));
        $this->assertEquals(200,$response->getStatusCode());


        $this->asserter()->assertResponsePropertyEquals($response,'avatarNumber',2);
        $this->asserter()->assertResponsePropertyEquals($response,'nickname','CowerCoder');

    }

    public function testDELETEProgrammes()
    {
        $this->createProgrammer(array(
            'nickname' =>'UnitTester',
            'avatarNumber' => 3,
        ));

        $response = $this->client->delete('/api/programmers/UnitTester');
        $this->assertEquals(204,$response->getStatusCode());

    }

    public function testPATCHProgrammers()
    {
        $this->createProgrammer(array(
            'nickname' =>'CowerCoder',
            'avatarNumber' => 5,
            'tagLine' => 'a test dev!'
        ));
        $data = array(
            'tagLine' => 'bar'
        );

        $response = $this->client->patch('/api/programmers/CowerCoder',array(
            'body'=> json_encode($data)
        ));
        $this->assertEquals(200,$response->getStatusCode());


        $this->asserter()->assertResponsePropertyEquals($response,'tagLine','bar');
        $this->asserter()->assertResponsePropertyEquals($response,'avatarNumber',5);
    }

    public function testValidationErrors()
    {

        $data = array(
            'avatarNumber' => 5,
            'tagLine' => 'a test dev!'
        );
        //1 - Create a programmer resource
        $response = $this->client->post('/api/programmers', [
            'body' => json_encode($data)
        ]);
        $this->assertEquals(400, $response->getStatusCode());
        $this->asserter()->assertResponsePropertiesExist($response,array(
            'type',
            'title',
            'errors',
        ));

        $this->asserter()->assertResponsePropertyExists($response,'errors.nickname');
        $this->asserter()->assertResponsePropertyEquals($response,
            'errors.nickname[0]',
            'Please enter a clever nickname');
        $this->asserter()->assertResponsePropertyDoesNotExist($response,'errors.avatarNumber');
        $this->assertEquals('application/problem+json',$response->getHeader('Content-Type'));

    }


    public function testInvalidJson()
    {

       $invalidJson = <<<EOF
{
    "nickname": "alfred",
    "avatarNumber" : "2
    "tagLine" "i'm from a test!"
}
EOF;

        //1 - Create a programmer resource
        $response = $this->client->post('/api/programmers', [
            'body' =>$invalidJson
        ]);

        $this->assertEquals(400, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyContains(
            $response,
            'type',
            'invalid_body_format'
        );

    }

    public function test404Exception()
    {
        $response  = $this->client->get('/api/programmers/fake');
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('application/problem+json',$response->getHeader('Content-Type'));
        $this->asserter()->assertResponsePropertyEquals(
            $response,
            'type',
            'about:blank'
        );
        $this->asserter()->assertResponsePropertyEquals(
            $response,
            'title',
            'Not Found'
        );
        $this->asserter()->assertResponsePropertyEquals(
            $response,
            'detail',
            'No programmer found for username fake'
        );




    }

    public function testRequiresAuthentification()
    {
        $response = $this->client->post('/api/programmers',[
            'body' => '[]'
        ]);
        $this->assertEquals(401,$response->getStatusCode());

    }

}


