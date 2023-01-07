<?php

header('Content-Type: application/json');
// header('Access-Control-Allow-*');
header('Access-Control-Allow-Origin: *');

$json = '
{
    "swagger": "2.0",
    "info": {
        "description": "Уютная Ниндзя АПИ.",
        "version": "0.1.0",
        "title": "Swagger Ninja", '
    // .' "termsOfService": "http://swagger.io/terms/",
    // "contact": {
    //     "email": "ninja@php-cat.com"
    // },
    // "license": {
    //     "name": "Apache 2.0",
    //     "url": "http://www.apache.org/licenses/LICENSE-2.0.html"
    // } '
    . ' }, '

    // .' "hosts": [ 
    //     "petstore.swagger.io",
    //     "petstore.swagger.io2" 
    // ], '

    // .' "basePath": "/v2", '
    // .' "tags": [
    //     {
    //         "name": "getPromocode",
    //         "description": "getPromocode"
    //     },
    //     {
    //         "name": "getFilesList",
    //         "description": "getFilesList"
    //     },
    //     {
    //         "name": "getSections",
    //         "description": "getSections"
    //     },
    //     {
    //         "name": "getProductsBySection",
    //         "description": "getProductsBySection"
    //     }
    // ], '

    . ' "servers": [ 
        {
        "url" : "https://111",
        "description" : "121212" 
        },
        {
        "url" : "https://111222",
        "description" : "121212" 
        },
    ], '

    . ' "schemes": [
        "https",
        "http"
    ], 
    '
    . ' "paths": {
        "/local/ajax/frontPromocode.php": {
            "post": {
                "tags": [
                    "getPromocode"
                ],
                "summary": "getPromocode",
                "description": "",
                "operationId": "getPromocode",
                "consumes": [
                    "application/json"
                ],
                "produces": [
                    "application/json"
                ],
                "parameters": [
                    {
                        "action": "getPromocode"
                    }
                ],
                "responses": {
                    "200": {
                        "description": "successful operation",
                        "schema": {
                            "$ref": "#/definitions/getPromocode"
                        }
                    }
                }
            },
            "post": {
                "tags": [
                    "getFilesList"
                ],
                "summary": "getFilesList",
                "description": "",
                "operationId": "getFilesList",
                "consumes": [
                    "application/json"
                ],
                "produces": [
                    "application/json"
                ],
                "parameters": [
                    {
                        "action": "getFilesList"
                    }
                ],
                "responses": {
                    "200": {
                        "description": "successful operation",
                        "schema": {
                            "$ref": "#/definitions/getFilesList"
                        }
                    }
                }
            },
            "post": {
                "tags": [
                    "getSections"
                ],
                "summary": "getSections",
                "description": "",
                "operationId": "getSections",
                "consumes": [
                    "application/json"
                ],
                "produces": [
                    "application/json"
                ],
                "parameters": [
                    {
                        "action": "getSections"
                    }
                ],
                "responses": {
                    "200": {
                        "description": "successful operation",
                        "schema": {
                            "$ref": "#/definitions/getSections"
                        }
                    }
                }
            },
            "post": {
                "tags": [
                    "getProductsBySection"
                ],
                "summary": "getProductsBySection",
                "description": "",
                "operationId": "getProductsBySection",
                "consumes": [
                    "application/json"
                ],
                "produces": [
                    "application/json"
                ],
                "parameters": [
                    {
                        "action": "getProductsBySection",
                        "data": {
                            "iblock": {
                                "type": "integer",
                                "format": "int32"
                            },
                            "idsection": {
                                "type": "integer",
                                "format": "int32"
                            }
                        }
                    }
                ],
                "responses": {
                    "200": {
                        "description": "successful operation",
                        "schema": {
                            "$ref": "#/definitions/getProductsBySection"
                        }
                    }
                }
            },
            "post": {
                "tags": [
                    "newPromocode"
                ],
                "summary": "newPromocode",
                "description": "",
                "operationId": "newPromocode",
                "consumes": [
                    "application/json"
                ],
                "produces": [
                    "application/json"
                ],
                "parameters": [
                    {
                        "action": "newPromocode",
                        "data": {
                            "$ref": "#/definitions/newPromocode"
                        }
                    }
                ],
                "responses": {
                    "200": {
                        "description": "successful operation",
                        "schema": {
                            "status": true
                        }
                    }
                }
            }
        }
    }, '

    // .' "securityDefinitions": {
    //     "petstore_auth": {
    //         "type": "oauth2",
    //         "authorizationUrl": "http://petstore.swagger.io/oauth/dialog",
    //         "flow": "implicit",
    //         "scopes": {
    //             "write:pets": "modify pets in your account",
    //             "read:pets": "read your pets"
    //         }
    //     },
    //     "api_key": {
    //         "type": "apiKey",
    //         "name": "api_key",
    //         "in": "header"
    //     }
    // }, '

    // .' "definitions": {
    //     "getPromocode": {
    //         "type": "object",
    //         "properties": {
    //             "promocodeList": {
    //                 "DATE": {
    //                     "type": "string"
    //                 },
    //                 "MIN_SUM": {
    //                     "type": "integer",
    //                     "format": "int32"
    //                 },
    //                 "PRODUCT": {
    //                     "type": "string"
    //                 },
    //                 "TYPE": {
    //                     "type": "string"
    //                 },
    //                 "VARIANT": {
    //                     "type": "string"
    //                 }
    //             },
    //             "status": true
    //         }
    //     },
    //     "getFilesList": {
    //         "type": "object",
    //         "properties": {
    //             "fileList": {
    //                 "id": {
    //                     "type": "integer",
    //                     "format": "int32"
    //                 },
    //                 "name": {
    //                     "type": "string"
    //                 },
    //                 "usersCount": {
    //                     "type": "integer",
    //                     "format": "int32"
    //                 }
    //             },
    //             "status": true
    //         }
    //     },
    //     "getSections": {
    //         "type": "object",
    //         "properties": {
    //             "sectionList": {
    //                 "NAME": {
    //                     "type": "string"
    //                 },
    //                 "IBLOCK": {
    //                     "type": "integer",
    //                     "format": "int32"
    //                 },
    //                 "IDSECTION": {
    //                     "type": "integer",
    //                     "format": "int32"
    //                 }
    //             },
    //             "status": true
    //         }
    //     },
    //     "getProductsBySection": {
    //         "type": "object",
    //         "properties": {
    //             "getProductsBySection": {
    //                 "NAME": {
    //                     "type": "string"
    //                 },
    //                 "COST_PRICE": {
    //                     "type": "string"
    //                 },
    //                 "PRICE": {
    //                     "type": "integer",
    //                     "format": "int32"
    //                 }
    //             },
    //             "status": true
    //         }
    //     },
    //     "newPromocode": {
    //         "type": "object",
    //         "properties": {
    //             "getProductsBySection": {
    //                 "name": {
    //                     "type": "string"
    //                 },
    //                 "dateFrom": {
    //                     "type": "string"
    //                 },
    //                 "dateTo": {
    //                     "type": "string"
    //                 },
    //                 "minSum": {
    //                     "type": "integer",
    //                     "format": "int32"
    //                 },
    //                 "tiragiType": {
    //                     "type": "integer",
    //                     "format": "int32"
    //                 },
    //                 "tiragiVariant": {
    //                     "type": "integer",
    //                     "format": "int32"
    //                 },
    //                 "product": {
    //                     "type": "array"
    //                 },
    //                 "userList": {
    //                     "type": "integer",
    //                     "format": "int32"
    //                 },
    //                 "discount": {
    //                     "type": "integer",
    //                     "format": "int32"
    //                 },
    //                 "isBonus": {
    //                     "type": "integer",
    //                     "format": "int32"
    //                 },
    //                 "bonusVal": {
    //                     "type": "integer",
    //                     "format": "int32"
    //                 },
    //                 "bonusPeriod": {
    //                     "type": "integer",
    //                     "format": "int32"
    //                 }
    //             },
    //             "status": true
    //         }
    //     }
    // }, '
    // .' "externalDocs": {
    //     "description": "Find out more about Swagger",
    //     "url": "http://swagger.io"
    // } '
    . ' }';

$ar = json_decode($json);

$ar["swagger"] = "2.0";
$ar['info'] = [
    "description" => "Уютная Ниндзя АПИ.",
    "version" => "0.1.0",
    "title" => "Swagger Ninja",
];
//     '
//     // .' "termsOfService": "http://swagger.io/terms/",
//     // "contact": {
//     //     "email": "ninja@php-cat.com"
//     // },
//     // "license": {
//     //     "name": "Apache 2.0",
//     //     "url": "http://www.apache.org/licenses/LICENSE-2.0.html"
//     // } '
// .' }, '



$ar['paths'] = [];

// добавляем эндпоинт + метод обращения POST
$ar['paths']['/local/ajax/frontPromocode.php']['post'] =
    [
        "tags" => ["getPromocode"],
        "summary" => "getPromocode",
        "description" => "Получаем промокоды",
        "operationId" => "getPromocode",
        "consumes" => [
            "application/json"
        ],
        "produces" => [
            "application/json"
        ],
        "parameters" => [
            [
                'name' => "action",
                'in' => 'query',
                'type' => 'string',
                'example' => "getPromocode",
                // 'default' => "getPromocode"
                'required' => true

            ]
        ],
        "responses" => [
            "200" => [
                "description" => "successful operation",
                // "schema": {
                //     "$ref": "#/definitions/getPromocode"
                // }
            ],
            "401" => [
                "description" => "no aut",
            ],
            "404" => [
                "description" => "not found",
            ]
        ]
    ];


die(json_encode($ar));
