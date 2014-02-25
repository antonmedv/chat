<?php
/* (c) Anton Medvedev <anton@elfet.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elfet\Chat;

use Symfony\Component\HttpFoundation\Response;

class Application extends \Silex\Application
{
    public function __construct(array $values = array())
    {
        parent::__construct($values);
    }

    public function render($viewPath, $params = [])
    {
        $viewPath = $this['view_dir'] . $viewPath;

        $basePath = $this['request']->getBasePath();

        extract($params);
        ob_start();
        include $viewPath;
        $content = ob_get_clean();

        return new Response($content);
    }
} 