<?php
namespace component\Locale;

use Phalcon\Di\Injectable;
use Phalcon\Translate\Adapter\NativeArray;
use Phalcon\Translate\InterpolatorFactory;
use Phalcon\Translate\TranslateFactory;
use App\Middleware\Middleware;
class Locale extends Injectable
{
    /**
     * @return NativeArray
     */
    public function getTranslator(): NativeArray
    {
        session_start();
        Middleware::boot();
        $language = $_SESSION['language'];
        $messages = [];
        if (isset($_SESSION['language'])) {
            $translationFile = APP_PATH . '/messages/' . $language . '.php';
        }
        if (true !== file_exists($translationFile)) {
            $translationFile = APP_PATH . '/messages/en-GB.php';
        }
        require $translationFile;

        $interpolator = new InterpolatorFactory();
        $factory = new TranslateFactory($interpolator);
        $di = $this->getDI();

        if ($di->get('cache')->has('words') && $di->get('cache')->get('language') == $language) {
            $array = json_decode(json_encode(array($di->get('cache')->get('words'))[0]), true);
            return $factory->newInstance('array', ['content' => $array]);
        } else {
            $di->get('cache')->set('words', $messages);
            $di->get('cache')->set('language', $language);
            return $factory->newInstance(
                'array',
                [
                    'content' => $messages,
                ]
            );
        }
    }
}