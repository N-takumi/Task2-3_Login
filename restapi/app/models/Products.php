<?php

namespace Store\Models;

use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness;
use Phalcon\Validation\Validator\PresenceOf;//null空文字チェック
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Numericality;//is数値チェック
use Phalcon\Validation\Validator\Between;//数値の大きさ
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Message;

class Products extends Model
{

    public $id;
    public $name;
    public $description;
    public $price;
    public $imgFileName;


    public function validation()//データの妥当性を高めるための機能
    {

      $validator = new Validation();

      //同じ名前をつけさせない
      $validator->add(
        'name',
        new Uniqueness(
        [
            'message' => 'The Product name must be unique',
        ]
        )
      );


      //値がnullまたは空の文字列でないことを検証
      $validator->add(
        [
        "name",
        "description",
        "price",
        "imgFileName",
        ],
        new PresenceOf(
          [
            "message" => [
                "name"  => "The name is required",
                "description" => "The description is required",
                "price" => "The price is required",
                "imgFileName" => "The imgFileName is required",
            ],
          ]
        )
      );


      //文字列長の制限
      $validator->add(
        [
        "name",
        "description",
        ],
        new StringLength(
          [
            "max" => [
                "name"  => 100,
                "description" => 500,
            ],
            "min" => [
                "name"  => 1,
                "description" => 1,
            ],
            "messageMaximum" => [
                "name"  => "We don't like really long name",
                "description" => "We don't like really long description",
            ],
            "messageMinimum" => [
                "name"  => "We don't like too short last name",
                "description" => "We don't like too short first description",
            ]
          ]
        )
      );

      //priceが数値であるか
      $validator->add(
        "price",
        new Numericality(
          [
            "message" => "price is not numeric",
          ]
        )
      );

      //priceが0以上で99999999以下であるか
      $validator->add(
        "price",
        new Between(
          [
            "minimum" => 0,
            "maximum" => 99999999,
            "message" => "The price must be between 0 and 99999999",
          ]
          )
      );


      return $this->validate($validator);
    }


}
