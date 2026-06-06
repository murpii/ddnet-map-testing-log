<?php

require(dirname(__DIR__) . '/vendor/autoload.php');

use Slim\App;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use DDNet\MapTestingLog\Support\Config;
use DDNet\MapTestingLog\Fetcher as MapTestingLogFetcher;
use DDNet\MapTestingLog\Support\Asset\Fetcher as AssetFetcher;
use DDNet\MapTestingLog\Support\View\Renderer as ViewRenderer;
use DDNet\MapTestingLog\Support\View\Helpers as ViewHelpers;
use DDNet\MapTestingLog\Message\Component\Renderer as MessageComponentRenderer;
use DDNet\MapTestingLog\Support\Container\Access as ContainerAccess;

// =============================
// Slim framework initialization
// =============================

$config = [
    'settings' => [
        'displayErrorDetails' => true
    ],
];

$app = new App($config);

$container = $app->getContainer();

$container['config'] = (new Config\Fetcher(
    dirname(__DIR__) . '/config/'
))->fetchAll();

$container['mapTestingLogFetcher'] = function ($container) {
    return new MapTestingLogFetcher(
        $container['config']['resources']['mapTestingLogs']['path']
    );
};

$container['assetFetcher'] = function ($container) {
    return new AssetFetcher($container['config']['app']['url']);
};

$container['viewRenderer'] = function ($container) {
    $viewRenderer = new ViewRenderer(
        $container['config']['resources']['views']['path']
    );

    $unmappedViewHelpers = [
        'viewRenderer' => [
            'name' => 'viewRenderer',
            'helper' => $viewRenderer
        ],
        'assetFetcher' => [
            'name' => 'assetFetcher',
            'helper' => $container['assetFetcher']
        ],
        'markdownParser' => [
            'name' => 'markdownParser',
            'helper' => new ViewHelpers\MarkdownParser()
        ],
        'router' => [
            'name' => 'router',
            'helper' => $container['router']
        ],
        'messageComponentRenderer' => [
            'name' => 'messageComponentRenderer',
            'helper' => new MessageComponentRenderer(
                $viewRenderer,
                $container['config']['resources']['views']
                    ['componentRenderer']['subPath'],
                $container['config']['resources']['views']
                    ['componentRenderer']['stepSubPath']
            )
        ],
        'config' => [
            'name' => 'config',
            'helper' => $container['config']
        ]
    ];

    $viewHelpers = [
        [
            'view' => 'layouts/show.phtml',
            'helper' => $unmappedViewHelpers['viewRenderer'],
        ],
        [
            'view' => 'layouts/show.phtml',
            'helper' => $unmappedViewHelpers['assetFetcher'],
        ],
        [
            'view' => 'layouts/index.phtml',
            'helper' => $unmappedViewHelpers['viewRenderer'],
        ],
        [
            'view' => 'layouts/index.phtml',
            'helper' => $unmappedViewHelpers['assetFetcher'],
        ],
        [
            'view' => 'partials/list.phtml',
            'helper' => $unmappedViewHelpers['router'],
        ],
        [
            'view' => 'partials/show.phtml',
            'helper' => $unmappedViewHelpers['assetFetcher'],
        ],
        [
            'view' => 'partials/show.phtml',
            'helper' => $unmappedViewHelpers['messageComponentRenderer'],
        ],
        [
            'view' => 'partials/show.phtml',
            'helper' => $unmappedViewHelpers['config'],
        ],
        [
            'view' => 'partials/message/component/variants/text/component/' .
                'variants/text.phtml',
            'helper' => $unmappedViewHelpers['markdownParser']
        ],
        [
            'view' => 'partials/message/component/variants/text/component/' .
                'variants/channel-mention.phtml',
            'helper' => $unmappedViewHelpers['router']
        ],
        [
            'view' => 'partials/message/component/variants/attachment.phtml',
            'helper' => $unmappedViewHelpers['assetFetcher']
        ],
        [
            'view' => 'partials/message/component/variants/attachment.phtml',
            'helper' => $unmappedViewHelpers['config']
        ],
        [
            'view' => 'partials/message/component/variants/image.phtml',
            'helper' => $unmappedViewHelpers['assetFetcher']
        ],
        [
            'view' => 'partials/message/component/variants/image.phtml',
            'helper' => $unmappedViewHelpers['config']
        ],
        [
            'view' => 'partials/message/component/variants/text/component/variants/custom-emoji.phtml',
            'helper' => $unmappedViewHelpers['assetFetcher']
        ],
        [
            'view' => 'partials/message/component/variants/text/component/variants/custom-emoji.phtml',
            'helper' => $unmappedViewHelpers['config']
        ],
        [
            'view' => 'partials/message/component/variants/container.phtml',
            'helper' => $unmappedViewHelpers['assetFetcher']
        ],
        [
            'view' => 'partials/message/component/variants/container.phtml',
            'helper' => $unmappedViewHelpers['config']
        ],
        [
            'view' => 'partials/message/component/variants/container.phtml',
            'helper' => $unmappedViewHelpers['markdownParser']
        ],
        [
            'view' => 'partials/message/component/variants/embed.phtml',
            'helper' => $unmappedViewHelpers['assetFetcher']
        ],
        [
            'view' => 'partials/message/component/variants/embed.phtml',
            'helper' => $unmappedViewHelpers['config']
        ],
        [
            'view' => 'partials/message/component/variants/embed.phtml',
            'helper' => $unmappedViewHelpers['markdownParser']
        ],
        [
            'view' => 'partials/message/component/variants/reactions.phtml',
            'helper' => $unmappedViewHelpers['assetFetcher']
        ],
        [
            'view' => 'partials/message/component/variants/reactions.phtml',
            'helper' => $unmappedViewHelpers['config']
        ]
    ];

    $viewRenderer->helpers = $viewHelpers;
    return $viewRenderer;
};

ContainerAccess::$container = $container;

// =======
// Routing
// =======

$app->get('/', function (
    Request $request,
    Response $response
) {
    $logList = $this['mapTestingLogFetcher']->all();
    $this['viewRenderer']->render($response, 'layouts/index.phtml', [
        'logList' => $logList
    ]);
    return $response;
});

$app->get('/show/{name}', function (
    Request $request,
    Response $response,
    $args
) {
    $name = $args['name'];
    $log = $this['mapTestingLogFetcher']->byName($name);
    $logList = $this['mapTestingLogFetcher']->all();
    $this['viewRenderer']->render($response, 'layouts/show.phtml', [
        'log' => $log,
        'logList' => $logList
    ]);
    return $response;
})->setName('show.name');

$app->run();
