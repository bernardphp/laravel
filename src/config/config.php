<?php

return array(

    /*
     ***********
     * Services
     ***********
     */

    'services' => array(

        // reference a service class name
        'Foo' => '\\My\\Service\\Foo'

        // reference a service object
        //'Bar' => new \My\Service\Bar,

        // reference an IoC item
        //'Baz' => 'some.ioc.key',
    ),


    /*
     ***********
     * Connection
     ***********
     */

    // Contains name of connection in IoC (eq instance of SQSClient por Predis)
    //  defaults to name of the driver (eg "iron_mq"), if not set.
    //'connection' => 'sqs',


    /*
     ***********
     * Driver
     ***********
     */

    // IronMQ
    //  requires iron-io/iron_mq package
    'driver' => 'iron_mq',

    // SQS
    //  requires aws/aws-sdk-php package
    //'driver' => 'sqs',

    // Predis
    //  requires predis/predis package
    //'driver' => 'predis',

    // Custom
    //  Your custom driver class, which implements the \Bernard\Driver interface
    //'driver' => '\\My\\Superb\\Driver',


    /*
     ***********
     * Misc
     ***********
     */

    // Optional: Prefetch messages. Available for for SQS and IronMQ
    //'prefetch' => 1,

    // Optional: For SQS, holding an array of name -> url mappings of SQS queues
    //'queue_urls' => array('some-queue' => 'https://sqs.eu-west-1.amazonaws.com/123123/some-queue')

    // Optional: List of alternative normalizers classes
    //'normalizers' => array('\\Bernard\\Symfony\\EnvelopeNormalizer', '\\Bernard\\Symfony\\DefaultMessageNormalizer'),

    // Optional: Alternative serializer class
    //'serializer' => '\\Bernard\\Serializer\\SymfonySerializer'

    // Optional: List of alternative encoder classes
    //'encoders' => array('\\Symfony\\Component\\Serializer\\Encoder\\JsonEncoder')

);