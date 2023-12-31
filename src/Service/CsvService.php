<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class CsvService
{
    public function export($data, $filename) {
        $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
        $response = new Response($serializer->encode($data, CsvEncoder::FORMAT));
        $response->headers->set('Content type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', "attachement; filename=\"$filename\"");
        return $response;
    }

    public function import($filename, $option = []) {
        $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
        return $serializer->decode(file_get_contents($filename), CsvEncoder::FORMAT, $option);
    }
}