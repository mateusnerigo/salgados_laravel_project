# **Salgados Manager**
API project using Laravel 9
- [How to configure it](https://github.com/mateusnerigo/salgados_laravel_project/edit/main/README.md#how-to-configure-it)
- [How to use it](https://github.com/mateusnerigo/salgados_laravel_project/edit/main/README.md#how-to-use-it)
    - [Default return](https://github.com/mateusnerigo/salgados_laravel_project/edit/main/README.md#default-return)
    - [GET default routes](https://github.com/mateusnerigo/salgados_laravel_project/edit/main/README.md#get-default-routes)
    - [Retrieving by ID](https://github.com/mateusnerigo/salgados_laravel_project/edit/main/README.md#retrieving-by-id)
    - [Deactivate/Activate registers](https://github.com/mateusnerigo/salgados_laravel_project/edit/main/README.md#deactivateactivate-registers)
    - [Creating/Updating data](https://github.com/mateusnerigo/salgados_laravel_project/edit/main/README.md#creatingupdating-data)
    - [Updating sales status](https://github.com/mateusnerigo/salgados_laravel_project/edit/main/README.md#updating-sales-status)
- [Thank you!](https://github.com/mateusnerigo/salgados_laravel_project/edit/main/README.md#thank-you-im-really-glad-that-youre-here)

    

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
The default return is JSON formated and all requisitions returns in the following way:
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

## GET default routes
There are 4 main routes used to retrieve data (`get`) as a JSON:

- `/salePoints`: Returns in `data` all sale points created. `idSalePoints` (integer), `salePointName` (string), `description` (string), `isActive`(integer [0 | 1]).
```json
{
    "msg": "",
    "dev": "",
    "type": "",
    "data": [
        {
            "idSalePoints": 1,
            "salePointName": "First Sale Point",
            "description": "I'm the first sale point",
            "isActive": 1,
        },
        {
            "idSalePoints": 2,
            "salePointName": "Another Sale Point (But Deactivated)",
            "description": "I'm another sale point",
            "isActive": 0,
        },
    ]
}
```

- `/clients`: Returns in `data` all clients created. `idClients` (integer), `clientName` (string), `idSalePoints`(integer), `isActive`(integer [0 | 1]).
```json
{
    "msg": "",
    "dev": "",
    "type": "",
    "data": [
        {
            "idClients": 1,
            "clientName": "James 'The Crazy' Borg",
            "idSalePoints": 1,
            "isActive": 0,
        },
        {
            "idClients": 2,
            "clientName": "Maryanne Foster",
            "idSalePoints": 1,
            "isActive": 1,
        },
    ]
}
```
- `/products`: Returns in `data` all products created. `idProducts` (integer), `productName` (string), `standardValue`(decimal), `isActive`(integer [0 | 1]).
```json
{
    "msg": "",
    "dev": "",
    "type": "",
    "data": [
        {
            "idProducts": 1,
            "productName": "Dino Toothbrush",
            "standardValue": 52.35,
            "isActive": 0,
        },
        {
            "idProducts": 3,
            "productName": "Gigantic Ornamental Flower",
            "standardValue": 112.50,
            "isActive": 1,
        },
    ]
}
```
- `/sales`: Returns in `data` all sales created. `idSales` (integer), `idClients` (integer), `idSalePoints` (integer), `deliverDateTime` (datetime), `status` (string [ic: in course | cl: canceled | fs: finished]), `created_at` (laravel datetime), `updated_at` (laravel datetime), `items`(JSON array).
    - JSON array `items` contains `idSaleItems` (integer) (starting from 0 for each sale), `idSales` (integer), `idProducts` (integer), `quantity` (decimal), `soldPrice` (decimal), `discountApplied` (decimal).
```json
{
    "msg": "",
    "dev": "",
    "type": "",
    "data": [
        {
            "idSales": 1,
            "idClients": 1,
            "idSalePoints": 1,
            "deliverDateTime": "2022-10-01 14:35:12",
            "status": "fs",
            "created_at": "2022-11-27T19:42:25.000000Z",
            "updated_at": "2022-11-27T21:45:53.000000Z",
            "items": [
                {
                    "idSaleItems": 0,
                    "idSales": 1,
                    "idProducts": 1,
                    "quantity": "1.00",
                    "soldPrice": "12.50",
                    "discountApplied": "0.00"
                },
                {
                    "idSaleItems": 1,
                    "idSales": 1,
                    "idProducts": 2,
                    "quantity": "4.00",
                    "soldPrice": "2.50",
                    "discountApplied": "10.00"
                }
            ]
        }
    ]
}
```
## Retrieving by ID
To retrieve a register from its ID, we need to send a JSON field named **data** with the key based on each requisition for its specific retrieve route. (The logic is based on **generalRoute/sameButInSingular**)
<br>
A POST to **/sales/sale** with the following **multipart/form-data** body
```json
{
    "idSales":1
}
```
will result in a JSON array like in a **GET** requisition (in the section above) but with a single sale data register (**idSales** (integer) is the ID from a register created).
<br>

The same is valid for the other routes. Like the following:
- to **/salePoints/salePoint** use **idSalePoints**;
```json
{
    "idSalePoints": 1123
}
```
- to **/clients/client** use **idClients**;
```json
{
    "idClients": 3211
}
```
- to **/products/product** use **idProducts**;
```json
{
    "idProducts": 43312
}
```

## Deactivate/Activate registers
It is possible to toggle **sale points**, **clients** and **products** registers by sending a **POST** requisiton to its default routes with **/toggle** adition. <br>
The **multipart/form-data body** will follow the pattern in the section above, with the primary key (idSalePoints, idClients, etc.) in its **data** field.<br>
The routes are:
- `/salePoints/toggle`
- `/clients/toggle`
- `/products/toggle`

## Creating/Updating data
It is possible to create a **sale point**, a **client**, a **product** or a **sale** by sending a **POST** requisition to its default routes. Using that routes, we can also update a **sale point**, a **client** or a **product**, but we cannot update a sale (yet).<br>
To create or update a register, use a field called **data** im **multipart/form-data** body with the following data:
- `/salePoints`: 
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

- `/clients`:
    - **idClients** (integer): If sended, updates an existing register.
    - **clientName** (string): A name for a(n) new/existing client.
    - **idSalePoints** (int): A default sale point for the client.
```json
{
    "idClients":"",
    "clientName": "James Revolver",
    "idSalePoints":"2"
}
```

- `/products`:
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

## Updating sales status 
A sale can be set to three different status:
- `ic`: In course (can be updated to `cl`or `fs`)
- `cl`: Cancelled (cannot be updated)
- `fs`: Finished (cannot be updated)

In order to update the status of a sale, it is needed to send a **POST** requisition to the route **/sales/updateStatus**. <br>
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
 There's some big needs and updates coming like sales updating and authentication that I will learn and develop.<br> <br>
 See you! <br>
 Mateus Neri
