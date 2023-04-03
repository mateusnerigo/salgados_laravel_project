# **Salgados Manager**
API project using Laravel 9 and JWT authenticaton (jwt-auth).
- [How to configure it](https://github.com/mateusnerigo/salgados_laravel_project#how-to-configure-it)
- [How to use it](https://github.com/mateusnerigo/salgados_laravel_project#how-to-use-it)
    - [Default return](https://github.com/mateusnerigo/salgados_laravel_project#default-return)
    - [Registration, Login, Login validation and Logout](https://github.com/mateusnerigo/salgados_laravel_project#registration-login-logout-and-access-verification)
        - [Registration](https://github.com/mateusnerigo/salgados_laravel_project#registration)
        - [Login](https://github.com/mateusnerigo/salgados_laravel_project#login)
        - [Logout](https://github.com/mateusnerigo/salgados_laravel_project#registration)
        - [Access verification](https://github.com/mateusnerigo/salgados_laravel_project#access-verification)
    - [GET default routes](https://github.com/mateusnerigo/salgados_laravel_project#get-default-routes)
    - [Retrieving by ID](https://github.com/mateusnerigo/salgados_laravel_project#retrieving-by-id)
    - [Deactivate/Activate registers](https://github.com/mateusnerigo/salgados_laravel_project#deactivateactivate-registers)
    - [Creating/Updating data](https://github.com/mateusnerigo/salgados_laravel_project#creatingupdating-data)
    - [Updating sales status](https://github.com/mateusnerigo/salgados_laravel_project#updating-sales-status)
- [Thank you!](https://github.com/mateusnerigo/salgados_laravel_project#thank-you-im-really-glad-that-youre-here)



# How to configure it
- First of all, we need `PHP` and `composer` installed. Also a `PHP server` and `MySQL database` running.
- Copy `.env.example` to `.env` and change the values in order to use it with your MySQL database.
- Open a console in the project root and run composer in it like `composer install`.
- Verify if a PHP server and MySQL is running.
- In the console, continue with the command `php artisan migrate` to create the database structure.
- Run the command `php artisan serve` to start the project.
- Done, use it like an API to create, retrieve, update and delete data.

# How to use it
## Default return
The default return is JSON formated, so all requisitions returns in the following way:
- `msg`:  (string) Message for frontend use
- `dev`:  (string) Message for dev use, more information about errors and exceptions
- `type`: (string) The type/class of the message. Minded to be used for frontend libraries with default message types
- `data`: (json array) Returned data from requisition
```json
{
    "msg": "",
    "dev": "",
    "type": "",
    "data": []
}
```

## Registration, Login, Logout and Access verification
In order to access all API resources, it is obrigatory to have an Authorization option in all requisitions headers. To achive it you will need to, first of all, register an user on it.

### Registration
To register a new user on API it is needed to send a POST requisition containing a **multipart/form-data** body with a JSON named **data** to the route **/api/register** with the following:
- `fisrtName` (string)
- `lastName` (string)
- `userName` (string) (unique)
- `email` (string) (unique)
- `password` (string)
```json
{
    "firstName": "Mateus",
    "lastName": "Neri",
    "userName": "nerigo",
    "email":"mateus@mail.com",
    "password":"mateus123"
}
```
This requisition returns in default way and the `msg` field in it will show you the status of your requisition.

### Login
To login a user on API, it is needed to send a POST requisition containing a **multipart/form-data** body with a JSON named **data** to the route **/api/login** with the following:
- `userName` (string)
- `password` (string)
```json
{
    "userName": "nerigo",
    "password":"mateus123"
}
```
This requisition returns in default way and the `msg` field in it will show you the status of your requisition. In addition, it will have the information needed to use the API routes. This JSON return will be like the following:
```json
{
  "msg": "Usuário logado com sucesso!",
  "dev": "",
  "type": "success",
  "data": {
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjcyOTA4MzM4LCJleHAiOjE2NzI5MTE5MzgsIm5iZiI6MTY3MjkwODMzOCwianRpIjoieWhMSEUxa0RuNjdOcjVKSCIsInN1YiI6IjIiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.ojbcdjzWTI_F5NXdjcDPFQCY0G0nmUfw7ny4riZoC-Y",
    "token_type": "bearer",
    "expires_in": 3600
  }
}
```

**IMPORTANT**: In order to access API resources, it is needed to send an Authorization header option with `data.token_type` and `data.access_token` like

```
bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjcyOTA4MzM4LCJleHAiOjE2NzI5MTE5MzgsIm5iZiI6MTY3MjkwODMzOCwianRpIjoieWhMSEUxa0RuNjdOcjVKSCIsInN1YiI6IjIiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.ojbcdjzWTI_F5NXdjcDPFQCY0G0nmUfw7ny4riZoC-Y
```

### Logout
To logout, it is needed to send a GET requisition with the header option as mentioned in [login](https://github.com/mateusnerigo/salgados_laravel_project/edit/main/README.md#login). The user will be logged out and the Authorization data wil not be valid anymore.

### Access verification
An utility route to verifing access, by validate if the given authorization is still valid, is implemented and it works simply by sending a GET requisition to `/api/verifyAccess` with the header option as mentioned in [login](https://github.com/mateusnerigo/salgados_laravel_project/edit/main/README.md#login) and returning
```json
{
  "msg": "",
  "dev": "",
  "type": "",
  "data": {
    "hasAccess": 1
  }
}
```

## GET default routes
There are 4 main routes used to retrieve data (`GET`) as a JSON. They are paginated with Laravel standards and the params to navigate the result pages and filter data are at the next topic.

By default, the return for this routes contains the following data (products route ie.):
```json
{
    "current_page": 1,
    "data": [...],
    "first_page_url": "http://10.0.0.194:8000/api/products?page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "http://10.0.0.194:8000/api/products?page=1",
    "links": [
        {
            "url": null,
            "label": "&laquo; Previous",
            "active": false
        },
        {
            "url": "http://10.0.0.194:8000/api/products?page=1",
            "label": "1",
            "active": true
        },
        {
            "url": null,
            "label": "Next &raquo;",
            "active": false
        }
    ],
    "next_page_url": null,
    "path": "http://10.0.0.194:8000/api/products",
    "per_page": 10,
    "prev_page_url": null,
    "to": 9,
}
```
For example porpouses, we will show only what is returned inside "data". The routes are:

- `/api/salePoints`: Considering default pagination above, returns in `data` the sale points created. The returned fields are `idSalePoints` (integer), `salePointName` (string), `description` (string), `isActive`(integer [0 | 1]).
```json
{
    // ...
    "data": [
        {
            "isActive": 1,
            "idSalePoints": 1,
            "salePointName": "First Sale Point",
            "description": "I'm the first sale point",
            "idUsersCreation": 2,
            "userCreationName": "Mateus Neri",
            "idUsersLastUpdate": 2,
            "userUpdateName": "Mateus Neri",
            "createdAt": "2023-02-03 01:18:06",
            "updatedAt": "2023-03-30 20:19:40"
        },
        {
            "isActive": 0,
            "idSalePoints": 2,
            "salePointName": "Another Sale Point (But Deactivated)",
            "description": "I'm another sale point",
            "idUsersCreation": 2,
            "userCreationName": "Mateus Neri",
            "idUsersLastUpdate": 2,
            "userUpdateName": "Mateus Neri",
            "createdAt": "2023-02-03 01:18:06",
            "updatedAt": "2023-03-30 20:19:40"
        },
    ],
    // ...
}
```


- `/api/clients`: Considering default pagination above, returns in `data` the clients created. The returned fields are `idClients` (integer), `clientName` (string), `idSalePoints`(integer), `isActive`(integer [0 | 1]).
```json
{
    // ...
    "data": [
        {
            "isActive": 0,
            "idClients": 1,
            "clientName": "James 'The Crazy' Borg",
            "idSalePoints": 1,
            "salePointName": "First Sale Point",
            "idUsersCreation": 3,
            "userCreationName": "Laissi Vedovato",
            "idUsersLastUpdate": 3,
            "userUpdateName": "Laissi Vedovato",
            "createdAt": "2023-03-30 21:02:56",
            "updatedAt": "2023-03-30 21:33:21"
        },
        {
            "isActive": 1,
            "idClients": 2,
            "clientName": "Maryanne Foster",
            "idSalePoints": 1,
            "salePointName": "First Sale Point",
            "idUsersCreation": 3,
            "userCreationName": "Laissi Vedovato",
            "idUsersLastUpdate": 3,
            "userUpdateName": "Laissi Vedovato",
            "createdAt": "2023-03-30 21:02:56",
            "updatedAt": "2023-03-30 21:33:21"
        },
    ],
    // ...
}
```
- `/api/products`: Considering default pagination above, returns in `data` the products created. The returned fields are `idProducts` (integer), `productName` (string), `standardValue`(decimal), `isActive`(integer [0 | 1]).
```json
{
    // ...
    "data": [
        {
            "isActive": 0,
            "idProducts": 1,
            "productName": "Dino Toothbrush",
            "standardValue": 52.35,
            "idUsersCreation": 3,
            "userCreationName": "Laissi Vedovato",
            "idUsersLastUpdate": 3,
            "userUpdateName": "Laissi Vedovato",
            "createdAt": "2023-03-30 21:47:19",
            "updatedAt": "2023-03-30 21:51:16"
        },
        {
            "isActive": 1,
            "idProducts": 3,
            "productName": "Gigantic Ornamental Flower",
            "standardValue": 112.50,
            "idUsersCreation": 3,
            "userCreationName": "Laissi Vedovato",
            "idUsersLastUpdate": 3,
            "userUpdateName": "Laissi Vedovato",
            "createdAt": "2023-03-30 21:47:19",
            "updatedAt": "2023-03-30 21:51:16"
        },
    ],
    // ...
}
```
- `/api/sales`: Considering default pagination above, returns in `data` the sales created. The returned fields are `idSales` (integer), `idClients` (integer), `idSalePoints` (integer), `deliverDateTime` (datetime), `status` (string [ic: in course | cl: canceled | fs: finished]), `created_at` (laravel datetime), `updated_at` (laravel datetime), `items`(JSON array).
    - JSON array `items` contains `idSaleItems` (integer) (starting from 0 for each sale), `idSales` (integer), `idProducts` (integer), `quantity` (decimal), `soldPrice` (decimal), `discountApplied` (decimal).
```json
{
    // ...
    "data": [
       {
            "idSales": 3,
            "idClients": 2,
            "clientName": "Maryanne Foster",
            "idSalePoints": 1,
            "salePointName": "First Sale Point",
            "status": "ic",
            "idUsersCreation": 2,
            "userCreationName": "Mateus Neri",
            "idUsersLastUpdate": 2,
            "userUpdateName": "Mateus Neri",
            "createdAt": "2023-04-02 14:40:16",
            "updatedAt": "2023-04-02 14:40:16",
            "items": [
                {
                    "idSaleItems": 0,
                    "idSales": 3,
                    "idProducts": 1,
                    "quantity": "2.00",
                    "soldPrice": "13.00",
                    "discountApplied": "0.00",
                    "created_at": "2023-04-02T14:40:16.000000Z",
                    "updated_at": "2023-04-02T14:40:16.000000Z"
                }
            ]
        }
    ],
    // ...
}
```
## Pagination and register filtering
All You can optionally use GET parameters to filter data. The parameters available are:
- **page** (integer) (default 1);
- **perPage** (integer) (default 10);
- **search** (string);
- **orderBy** (string);
- **orberByData** (string);

## Retrieving by ID
If you use GET with an ID, that corresponds to the primary key of an entity, like idSalePoints, (ie. `/api/salePoints/1`), it will retrieve only the item that matches to it.

A GET to `/api/sales/1` will result in a JSON array like in the section above but with a single sale data register.

The same is valid for the other routes. Like the following:
- to `/api/salePoints/312`;
- to `/api/clients/213`;
- to `/api/products/231`;

## Deactivate/Activate registers
It is possible to toggle **sale points**, **clients** and **products** registers by sending a **GET** requisiton to its default routes with `/toggle` following by the id of the register that you want to toggle activation. <br>
The routes are like:
- `/api/salePoints/toggle/132`
- `/api/clients/toggle/123`
- `/api/products/toggle/321`

## Creating/Updating data
It is possible to create a **sale point**, a **client**, a **product** or a **sale** by sending a **POST** requisition to its default routes. Using that routes, we can also update a **sale point**, a **client**, a **product** and a **sale**.<br>
To create or update a register, use a field called **data** im **multipart/form-data** body with the following data:
- `/api/salePoints`:
    - **idSalePoints** (integer): If sended, updates an existing register.
    - **salePointName** (string): A name for a(n) new/existing sale point.
    - **description** (string): A brief description for you new sale point. Can be sended empty.
```json
{
    "idSalePoints":"",
    "salePointName": "My new sale point",
    "description":"It is an awesome sale point with awesome people."
}
```

- `/api/clients`:
    - **idClients** (integer): If sended, updates an existing register.
    - **clientName** (string): A name for a(n) new/existing client.
    - **idSalePoints** (integer): A default sale point for the client.
```json
{
    "idClients":"",
    "clientName": "James Revolver",
    "idSalePoints":"2"
}
```

- `/api/products`:
    - **idProducts** (integer): If sended, updates an existing register.
    - **productName** (string): A name for a(n) new/existing client.
    - **standardValue** (decimal): The default value for the item.
```json
{
    "idProducts":"1",
    "productName": "A awesome product with a different name",
    "standardValue":19.60
}
```

- `/api/sales`:
    - **idSales** (integer): If sended, updates an existing register.
    - **idSalePoints** (integer): A sale point for the sale.
    - **idClients** (integer): A client for the sale.
    - **deliverDateTime** (datetime 'Y-m-d H:i:s'): The deliver date and time for a sale.
    - **items** (JSON array): An item array with the products of a sale. It contains:
        - **idProducts** (integer): The item id
        - **quantity** (decimal): The quantity sold for that item
        - **soldPrice** (decimal): The total value for that item and quantity
        - **discountAppied** (decimal): The discount applied for the value of that item
```json
{
    "idSales":12,
    "idSalePoints":4,
    "idClients":7,
    "deliverDateTime":"2022-10-10 13:15:00",
    "items":[
        {
            "idProducts":3,
            "quantity":1.5,
            "soldPrice":9.00,
            "discountAppied":0.0,
        }
    ],
}
```

## Updating sales status
A sale can be set to three different status:
- `ic`: In course (can be updated to `cl`or `fs`)
- `cl`: Cancelled (cannot be updated)
- `fs`: Finished (cannot be updated)

In order to update the status of a sale, it is needed to send a **POST** requisition to the route **/api/sales/updateStatus**. <br>
The **multipart/form-data body** will need the primary key (idSales) **AND** the new status in its **data** field like the following:
```json
{
    "idSales":1,
    "status":"cl"
}
```
 <hr>

 # Thank you! I'm really glad that you're here!
 This project is under development and it is planned to be used as a backend API for another project, a VueJS SPA. <br>
 Please, if you like it or not, let me know what are you thinking about it.<br> <br>
 See you! <br>
 Mateus Neri
