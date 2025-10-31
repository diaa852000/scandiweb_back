<?php

namespace App\Controller;

use App\GraphQL\Schema\SchemaFactory;
use Doctrine\ORM\EntityManagerInterface;
use GraphQL\GraphQL;
use GraphQL\Error\DebugFlag;
use RuntimeException;
use Throwable;

class GraphQLController
{
    public static function handle(EntityManagerInterface $em): string
    {
        try {
            $schema = SchemaFactory::build($em);

            $rawInput = file_get_contents('php://input');
            if ($rawInput === false) {
                throw new RuntimeException('Failed to read request input');
            }

            $input = json_decode($rawInput, true);
            $query = $input['query'] ?? '';
            $variables = $input['variables'] ?? null;

            $result = GraphQL::executeQuery($schema, $query, null, null, $variables);
            $output = $result->toArray(DebugFlag::INCLUDE_DEBUG_MESSAGE);
        } catch (Throwable $e) {
            $output = [
                'errors' => [
                    ['message' => $e->getMessage()],
                ],
            ];
        }

        header('Content-Type: application/json; charset=UTF-8');
        return json_encode($output);
    }
}
