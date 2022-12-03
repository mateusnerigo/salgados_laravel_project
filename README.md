# **Salgados Manager**
API project using Laravel 9

<br>

# How to configure it
- First of all, we need `PHP` and `composer` installed. Also a `PHP server` and `MySQL database` running.
- Copy `.env.example` to `.env` and change the values in order to use it with your MySQL database.
- Open a console in the project root and run composer in it like `composer install`.
- Verify if a PHP server and MySQL is running.
- In the console, continue with the command `php artisan migrate` to create the database structure.
- Run the command `php artisan serve` to start the project.
- Done, use it like an API to create, retrieve, update and delete data.

<br>

# How to use it
## Default return
The default return is JSON formated and all requisitions returns in the following way:
```json
{
    // (string) Message for frontend use
    "msg": "",

    // (string) Message for dev use, more information about errors and exceptions
    "dev": "",

    // (string) The type/class of the message.
    // Minded to be used for frontend libraries with default message types
    "type": "",

    // (json array) Returned data from requisition
    "data": []
}
```
<br>
<hr>
<br>

## GET default routes

There are 4 main routes used to retrieve data (`get`) as a JSON:

- `/salePoints`
<br>
Returns in `data` all sale points created. `idSalePoints` (integer), `salePointName` (string), `description` (string), `isActive`(integer [0 | 1]).
<br>
Example:

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

<br>

- `/clients`
<br>
Returns in `data` all clients created. `idClients` (integer), `clientName` (string), `idSalePoints`(integer), `isActive`(integer [0 | 1]).
<br>
Example:

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

<br>

- `/products`
<br>
Returns in `data` all products created. `idProducts` (integer), `productName` (string), `standardValue`(decimal), `isActive`(integer [0 | 1]).
<br>
Example:

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

<br>

- `/sales`
<br>
Returns in `data` all sales created. `idSales` (integer), `idClients` (integer), `idSalePoints` (integer), `deliverDateTime` (datetime), `status` (string [ic: in course | cl: canceled | fs: finished]), `created_at` (laravel datetime), `updated_at` (laravel datetime), `items`(JSON array). <br>
`items` JSON array  contains `idSaleItems` (integer) (starting from 0 for each sale), `idSales` (integer), `idProducts` (integer), `quantity` (decimal), `soldPrice` (decimal), `discountApplied` (decimal).
<br>
Example:

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

<br>

## Retrieving by ID
To retrieve a register from its ID, we need to send a JSON array with the key based on each requisition for its specific retrieve route. (The logic is based on `generalRoute/sameButInSingular`)
<br><br>
 A POST to `/sales/sale` with the following multipart form-data body:

```json
// (integer) ID from a register created
{
    "idSales":1
}
```

will result in a JSON array like in a `GET` requisition (in the section above) but with a single sale data register.
<br>

The same is valid for the other routes. Like the following:
```json
// to /salePoints/salePoint use:
{
    "idSalePoints": 1123
}

// to /clients/client use:
{
    "idClients": 3211
}

// to /products/product use:
{
    "idProducts": 43312
}
```

<br>

## Deactivating registers (coming soon)

<br>

## Creating/Updating data (coming soon)



