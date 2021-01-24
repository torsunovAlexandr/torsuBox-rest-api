# Laravel 8.7.1 REST API With Simple API Authentication
A PHP Laravel Authentication API with E-mail verification, developed with Laravel 8.7.1 framework.

## Installation steps

Follow the bellow steps to install and set up the application.

**Step 1: Clone the Application**<br>
You can download the ZIP file or git clone from my repo into your project  directory.

**Step 2: Configure the Application**<br>
After you clone the repo in to your project folder the project need to be set up by following commands-

- In terminal go to your project directory and Run

        composer install 

- Then copy the .env.example file to .env file in the project root folder

- Edit the .env file and fill all required data for the bellow variables

        APP_URL=http://localhost //your application domain URL go here
    
        DB_HOST=127.0.0.1 // Your DB host IP. Here we are assumed it to be local host
        DB_PORT=3306 //Port if you are using except the default
        DB_DATABASE=name_of_your_database
        DB_USERNAME=db_user_name
        DB_PASSWORD=db_password

- To set the Application key run the bellow command in your terminal.

        php artisan key:generate

- Make your storage and bootstrapp folder writable by your application user.

- Create all the necessary tables need for the application by runing the bellow command.

        php artisan migrate

- Fill default data if your need by running bellow command.

        php artisan db:seed

Thats all! The application is configured now.


## API Endpoints and Routes

Laravel follows the Model View Controller (MVC) pattern I have creatd models associated with each resource. You can check in the **routes/api.php** file for all the routes that map to controllers in order to send out JSON data that make requests to our API.

Bellow are the all resources API endpoints -

        GET    | api/files  | api,auth:api Headers {"Accept":"application/json","Authorization":"Bearer {token}"}

        POST   | api/files | api,auth:api   Body {"name":"test","format":"txt","contents":"Hello World"}

        GET    | api/files/{fileName} | api,auth:api Headers {"Accept":"application/json","Authorization":"Bearer {token}"}

        PUT    | api/files?name={name}&format={format}&contents={content} | api,auth:api Headers {"Accept":"application/json","Authorization":"Bearer {token}"}

        DELETE | api/files/{fileName} | api,auth:api Headers {"Accept":"application/json","Authorization":"Bearer {token}"}

        POST   | api/login | api,guest Body {"email":"test@mail.ru","password":"123456"}'

        POST   | api/register | api,guest Body {"name":"test","email":"test@mail.ru","password":"123456","c_password":"123456"}


## API Authentication

All the api endpoints are protected by a simple API Authentication process. To access the resource data, the request header need token field. The **token** value need to be taken from the **api/login** API by passing valid email and password.

**Example Of Login API request**

        http://localhost:8001/api/login Body {"email":"test@mail.ru","password":"123456"}'

**Response Of Valid Login API**

        {
    "success": true,
    "data": {
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIzIiwianRpIjoiYWEwMTJkMTc4ZGQyY2UyYWNjNzNlODAxMzVmZmI0MGFhMzk2MzdmNWUwYjM2NWRhZWU3NWM1NmY2OTE0MmM0Y2JhMTJiMTYzYzcyZGU2N2UiLCJpYXQiOjE2MTE0OTI4ODEsIm5iZiI6MTYxMTQ5Mjg4MSwiZXhwIjoxNjQzMDI4ODgxLCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.JGyQK1h406oFhT9txCohgHpaPebn1EwoEvokVOWXfR-7XQgGMXpCs4KQ07iTb1N3uisXUUl3sP_YndcXsA_ZTXvik49F1-PyyFdvkbxgfMV_we5W90DrSdaKZ4PR_CAAbZjydKuNurv8ju9HMO5DNF5lxPaMI6fZr2IRH8kDVSJZkkk_hmHIxd5bhMILvxq9rdOF785OKbiSYPFzN3_RFKIGmZQwiv6kdIqdshoqmQCDVS-i1kBPgkV5eF6vfITEbx73CqoY4TMGayAEs_yP5_iSpTzXgR7LG9Y3CLz08GhOZElo8fxOXLJhr10JEC63E1A5KvtLbFeLEo2y-MCtsB1Nt0pUAT1iVCeWkx8zb790suSSb4DCXR8wcyHFQhdzTQxIgG0mVGNsMZG8FYNWGj_EeWMgYjdc1eEneyM3Y8kceUiKERdsyCcyOTOpKLDvfF4gSbWW5QHPMf8tF3sCJrpofUK89SBsL4HXgekmd0hSZjjHOE0cQljZgfMnzUDTYtI2dE8PnEXHfNiGM8HaHQMvJtNLI6Q1gOqJo0lYPnJpcg7xU2xfz911oXoHIR0Mhzz8nnZWF4Xy1lurfdaREhaKC-rIlwh_n8G0K6WaPc48VH8MINofFOOls0-zW8gh-FcTk5W1A_vZnjXOV9lq8LsSGhLNjDnnSB7ylapoWdY",
        "name": "torsunov"
    },
    "message": "Авторизация прошла успешно"
}


## Test Case

I have created several test case to test all the API endpoints by using Laravel TestCase set up.

To execute all the test case, move to the project root folder in terminal and then run -

        php artisan test

## Further work plan

1) Introduce a mechanism for the differentiation of rights. To do this, you need to create a table in the database, which will record the path to the file and the user who created this file. And let only the user who created it work with the file.
2) Store files in folders with user ID. Now different users will not be able to create files with the same name. The program is designed for one user
3) I need to figure out how to set up Docker. On windows I couldn't do this.

