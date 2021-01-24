<?php

namespace Tests\Feature;

use Faker\Factory as Faker;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GeneralTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGeneralTest()
    {
        //registration
        $faker = Faker::create();
        $userName = $faker->name;
        $email = $faker->email;
        $password = '123456';
        $params = [
            'name'=>$userName,
            'email'=>$email,
            "password"=>$password,
            "c_password"=>$password
        ];
        $response = $this->json('POST', '/api/register', $params);
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data'=> [
                    'token',
                    'name'
                ],
                'message'
            ]);
        //authorization
        $params = [
            'email'=>$email,
            "password"=>$password,
        ];
        $response = $this->json('POST', '/api/login', $params);
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data'=> [
                    'token',
                    'name'
                ],
                'message'
            ]);

        $headers = ['Authorization' => "Bearer ".$response['data']['token']];

        //create file
        $params = [
            'name'=>$faker->numerify('testFile########'),
            'format'=>"txt",
            "contents"=>$faker->text
        ];
        $fileName = $params["name"];
        $response = $this->json('POST', '/api/files', $params, $headers);
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'message'
            ]);
        Storage::disk('public')->assertExists("userFiles/".$fileName.".".$params["format"]);

        //update file
        $response = $this->json('PUT', '/api/files?name='.$fileName.'&format=txt&contents='.$faker->text, [], $headers);
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'message'
            ]);
        //get file content
        $response = $this->json('get', '/api/files/'.$fileName.'.txt', [], $headers);
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'message'
            ]);
        //get list of files
        $response = $this->json('GET', '/api/files', [], $headers);
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'message'
            ]);
        //delete a file
        $response = $this->json('DELETE', '/api/files/'.$fileName.'.txt', [], $headers);
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'message'
            ]);
        Storage::disk('public')->assertMissing("userFiles/'.$fileName.'.txt");
    }
}
