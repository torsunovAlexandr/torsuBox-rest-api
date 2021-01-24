<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Cache;

class FilesController extends BaseController
{
    /**
     * Получить список файлов
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $listOfFiles = Storage::files("public/userFiles");
        return $this->sendResponse($listOfFiles, 'Список файлов успешно получен');
    }
    /**
     * Создать файл
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //если в contents есть файл то загружаем его с его именем и расширением иначе создаём пустой файл
        if( $request->hasFile('contents')){
            // Имя и расширение файла
            $filenameWithExt = $request->file('contents')->getClientOriginalName();
            $exists = Storage::disk('public')->exists("userFiles/".$filenameWithExt);
            if ($exists === true) {
                return $this->sendError('Файл с таким именем уже создан. Укажите другое имя');
            }
            // Сохраняем файл
            $request->file('contents')->storeAs('public/userFiles/', $filenameWithExt);
        } else {
            $input = $request->all();
            $filename = $input['name'];
            $extention = $input['format'];
            $validator = Validator::make($input, [
                'name' => 'required',
                'format' => 'required', //вынес формат файла в отдельный параметр по логике с гугл диском, где сначала выбираешь тип файла
            ]);
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $exists = Storage::disk('public')->exists("userFiles/".$filename.".".$extention);
            if ($exists === true) {
                return $this->sendError('Файл с таким именем уже создан. Укажите другое имя');
            }
            Storage::put("public/userFiles/".$filename.".".$extention, '');
        }


        return $this->sendResponse(null, 'Файл создан');
    }

    /**
     * У текстовых файлов возвращает содержимое у остальных типов ссылку на файл
     * @param  string  $name
     * @return \Illuminate\Http\Response
     */
    public function show($name)
    {
        //пока не придумал где в приложении использовать кеширование
        Cache::remember('test',now()->addMinutes(10), function () {
           return 'chikibriki';
        });
        $exists = Storage::disk('public')->exists("/userFiles/".$name);

        if ($exists === false) {
            return $this->sendError('Файл не найден');
        }
        $pos = strpos($name, '.');
        $extentionFile = substr($name, $pos+1);
        $textExtentionArr = ['txt', 'log','text'];
        //смотреть содержимое можно только у текстовых файлов
        if(in_array($extentionFile,$textExtentionArr)) {
            $file = Storage::disk('public')->get("/userFiles/".$name);
            return $this->sendResponse($file, 'Содержимое файла успешно получено');
        } else {
            $file_path = Storage::url($name);
            $url = asset($file_path);
            return $this->sendResponse($url, 'Ссылка на файл успешно получено');
        }
    }

    /**
     * Обновить содержимое файла
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
            'format' => [
                'required',
                Rule::In(['txt', 'text', 'log']), //Обновлять содержимое можно только у текстовых файлов
            ],
            'contents'=>'required'
        ]);
        $name = $input['name'];
        $format = $input['format'];
        $contents = $input['contents'];
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $exists = Storage::disk('public')->exists("/userFiles/".$name.".".$format);
        if ($exists === false) {
            return $this->sendError('Файл с таким именем не найден');
        }
        //$contents = $contents;
        Storage::put("public/userFiles/".$name.".".$format, $contents);
        return $this->sendResponse($contents, 'Файл успешно обновлён');
    }

    /**
     * Удалить файл
     *
     * @param  string  $name
     * @return \Illuminate\Http\Response
     */
    public function destroy($name)
    {
        $exists = Storage::disk('public')->exists("/userFiles/".$name);
        if ($exists === false) {
            return $this->sendError('Файл с таким именем не найден');
        }
        Storage::delete("public/userFiles/".$name);
        return $this->sendResponse([], 'Файл успешно удалён');
    }
}