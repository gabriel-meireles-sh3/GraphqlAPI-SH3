<?php

declare(strict_types = 1);

use Illuminate\Support\Facades\App;

return [
    'route' => [
        'prefix' => 'graphql',
        'controller' => \Rebing\GraphQL\GraphQLController::class . '@query',
        'middleware' => [],
        'group_attributes' => [],
    ],
    'default_schema' => 'default',
    'batching' => [
        'enable' => true,
    ],

    
    'schemas' => [
        'default' => [
            'query' => [
                // User
                'users' => \App\GraphQL\Queries\User\UsersQuery::class,
                'user' => \App\GraphQL\Queries\User\UserQuery::class,
                'allSuport' => \App\GraphQL\Queries\User\AllSuportQuery::class,

                //Ticket
                'tickets' => \App\GraphQL\Queries\Ticket\TicketsQuery::class,
                'ticket' => \App\GraphQL\Queries\Ticket\TicketQuery::class,

                // Service
                'services' => \App\GraphQL\Queries\Service\ServicesQuery::class,
                'service' => \App\GraphQL\Queries\Service\ServiceQuery::class,
                'servicesBySupportId' => \App\GraphQL\Queries\Service\ServicesBySupportIdQuery::class,
                'servicesByTicketId' => \App\GraphQL\Queries\Service\ServicesByTicketIdQuery::class,
                'servicesAreas' => \App\GraphQL\Queries\Service\ServiceByServiceAreaQuery::class,
                'servicesTypes' => \App\GraphQL\Queries\Service\ServiceByServiceTypeQuery::class,
                'servicesUnassociated' => \App\GraphQL\Queries\Service\ServiceUnassociatedQuery::class,
                'servicesIncomplete' => \App\GraphQL\Queries\Service\ServiceIncompleteQuery::class,
            ],
            'mutation' => [
                // User
                'register' => \App\GraphQL\Mutations\User\RegisterMutation::class,
                'login' => \App\GraphQL\Mutations\User\LoginMutation::class,
                'logout' => \App\GraphQL\Mutations\User\LogoutMutation::class,

                // Ticket
                'createTicket' => \App\GraphQL\Mutations\Ticket\createTicketMutation::class,
                'removeTicket' => \App\GraphQL\Mutations\Ticket\removeTicketMutation::class,
                'restoreTicket' => \App\GraphQL\Mutations\Ticket\restoreTicketMutation::class,
                'updateTicket' => \App\GraphQL\Mutations\Ticket\updateTicketMutation::class,

                // Service
                'createService' => \App\GraphQL\Mutations\Service\createServiceMutation::class,
                'removeService' => \App\GraphQL\Mutations\Service\removeServiceMutation::class,
                'restoreService' => \App\GraphQL\Mutations\Service\restoreServiceMutation::class,
                'updateService' => \App\GraphQL\Mutations\Service\updateServiceMutation::class,
                'associateService' => \App\GraphQL\Mutations\Service\associateServiceMutation::class,
                'completeService' => \App\GraphQL\Mutations\Service\completeServiceMutation::class,
            ],
            // The types only available in this schema
            'types' => [
                'UserData' => \App\GraphQL\Inputs\UserDataInput::class,

                'User' => \App\GraphQL\Types\UserType::class,
                'Support' => \App\GraphQL\Types\SupportType::class,
        
                'Ticket' => \App\GraphQL\Types\TicketType::class,
                'Service' => \App\GraphQL\Types\ServiceType::class,
            ],

            // Laravel HTTP middleware
            'middleware' => null,

            // Which HTTP methods to support; must be given in UPPERCASE!
            'method' => ['GET', 'POST'],

            // An array of middlewares, overrides the global ones
            'execution_middleware' => null,
        ],
    ],

    'types' => [],

    'error_formatter' => [\Rebing\GraphQL\GraphQL::class, 'formatError'],

    'errors_handler' => [\Rebing\GraphQL\GraphQL::class, 'handleErrors'],

    'security' => [
        'query_max_complexity' => null,
        'query_max_depth' => null,
        'disable_introspection' => false,
    ],

    'pagination_type' => \Rebing\GraphQL\Support\PaginationType::class,

    'simple_pagination_type' => \Rebing\GraphQL\Support\SimplePaginationType::class,

    'defaultFieldResolver' => null,

    'headers' => [],

    'json_encoding_options' => 0,

    'apq' => [
        // Enable/Disable APQ - See https://www.apollographql.com/docs/apollo-server/performance/apq/#disabling-apq
        'enable' => env('GRAPHQL_APQ_ENABLE', false),

        // The cache driver used for APQ
        'cache_driver' => env('GRAPHQL_APQ_CACHE_DRIVER', config('cache.default')),

        // The cache prefix
        'cache_prefix' => config('cache.prefix') . ':graphql.apq',

        // The cache ttl in seconds - See https://www.apollographql.com/docs/apollo-server/performance/apq/#adjusting-cache-time-to-live-ttl
        'cache_ttl' => 300,
    ],

    /*
     * Execution middlewares
     */
    'execution_middleware' => [
        \Rebing\GraphQL\Support\ExecutionMiddleware\ValidateOperationParamsMiddleware::class,
        // AutomaticPersistedQueriesMiddleware listed even if APQ is disabled, see the docs for the `'apq'` configuration
        \Rebing\GraphQL\Support\ExecutionMiddleware\AutomaticPersistedQueriesMiddleware::class,
        \Rebing\GraphQL\Support\ExecutionMiddleware\AddAuthUserContextValueMiddleware::class,
        // \Rebing\GraphQL\Support\ExecutionMiddleware\UnusedVariablesMiddleware::class,
    ],
];
