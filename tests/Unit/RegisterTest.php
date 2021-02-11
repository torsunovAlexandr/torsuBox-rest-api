<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Validator;
use Faker\Factory as Faker;

class RegisterTest extends TestCase
{
    /**
     * Регистрация пользователя
     *
     * @return void
     */
    public function testRegister()
    {
        $faker = Faker::create();
        $params = [
            'name'=>$faker->name,
            'email'=>$faker->email,
            "password"=>"123456",
            "c_password"=>"123456"
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
    }
    /**
     * Авторизация пользователя
     *
     * @return void
     */
    public function testLogin()
    {
        $params = [
            'email'=>'test@mail.ru',
            "password"=>"123456",
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
    }
}
