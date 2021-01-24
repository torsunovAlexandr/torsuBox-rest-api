<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;

class FilesTest extends TestCase
{
    private $headers;
    public function setUp(): void
    {
        parent::setUp();
        //формируем токен авторизации
        if(Auth::attempt(['email' => 'torsunov@mail.ru', 'password' => "123456"])) {
            $user = Auth::user();
            $token = $user->createToken('MyApp')->accessToken;
            $this->headers = ['Authorization' => "Bearer $token"];
        }
    }
    /**
     * Создание файла
     *
     * @return void
     */
    public function testCreateFile()
    {
        if(empty($this->headers)) {
            $this->assertTrue($this->headers); //если нет headers то выкидываем ошибку
        }
        $params = [
            'name'=>"test",
            'format'=>"txt",
            "contents"=>"Hello world"
        ];
        $response = $this->json('POST', '/api/files', $params, $this->headers);
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'message'
            ]);
        Storage::disk('public')->assertExists("userFiles/".$params["name"].".".$params["format"]);
    }
    /**
     * Обновить содержимое файла
     *
     * @return void
     */
    public function testUpdateFile()
    {
        Storage::disk('public')->assertExists("userFiles/test.txt");
        if(!empty($this->headers)) {
            $response = $this->json('PUT', '/api/files?name=test&format=txt&contents=12312', [], $this->headers);
            $response
                ->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data',
                    'message'
                ]);

        }
    }
    /**
     * Получить содержимое файла
     *
     * @return void
     */
    public function testGetContents()
    {
        if(!empty($this->headers)) {
            $response = $this->json('get', '/api/files/test.txt', [], $this->headers);
            $response
                ->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data',
                    'message'
                ]);
        }
    }
    /**
     * Получить список файлов
     *
     * @return void
     */
    public function testGetListFiles()
    {
        if(!empty($this->headers)) {
            $response = $this->json('GET', '/api/files', [], $this->headers);
            $response
                ->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data',
                    'message'
                ]);
        }
    }
    /**
     * Удаление файла
     *
     * @return void
     */
    public function testDeleteFile()
    {
        if(!empty($this->headers)) {
            $response = $this->json('DELETE', '/api/files/test.txt', [], $this->headers);
            $response
                ->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data',
                    'message'
                ]);
            Storage::disk('public')->assertMissing("userFiles/test.txt");
        }
    }
}
