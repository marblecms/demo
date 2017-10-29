<?php

namespace App\Http\Controllers;

use Marble\Admin\App\Models\Node;
use Marble\Admin\App\Models\Language;
use Marble\Admin\App\Helpers\NodeHelper;
use Config;
use Cache;

class FrontController extends Controller
{
    public function viewNode($id, $languageId, $parameters = array())
    {
        $nodeClassName = Cache::rememberForever("node_class_name_$id", function () use ($id, $languageId) {

            $node = Node::find($id);

            $classNameParts = explode('_', $node->class->namedIdentifier);
            $className = '\App\Http\Controllers\NodeClasses\\';

            foreach ($classNameParts as $classNamePart) {
                $className .= ucfirst($classNamePart);
            }

            $className .= 'Controller';

            if (class_exists($className)) {
                return $className;
            }

        });

        $controller = new $nodeClassName();
        $controller->setCurrentLocale($languageId);

        return $controller->viewNode($id, $languageId, ...$parameters);
    }

    public function redirectLocale()
    {
        if (Config::get('app.uri_locale_prefix')) {
            $language = Language::find(Config::get('app.locale'));

            return redirect('/'.$language->id.'/');
        } else {
            return $this->viewIndexForLocale(Language::find(Config::get('app.locale')));
        }
    }

    public function viewIndexForLocale($language)
    {
        $node = NodeHelper::getSystemNode('settings');
        $frontpageNodeId = $node->attributes->frontpage->value[$language->id];

        if (!$frontpageNodeId) {
            die("No Frontpage selected for locale '".$language->id."'");
        }

        return $this->viewNode($frontpageNodeId, $language->id);
    }
}
