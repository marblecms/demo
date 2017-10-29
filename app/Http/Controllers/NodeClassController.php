<?php

namespace App\Http\Controllers;

use Marble\Admin\App\Helpers\NodeHelper;
use Marble\Admin\App\Models\Node;
use Marble\Admin\App\Models\Language;
use App\Http\Controllers\Controller;

class NodeClassController extends Controller
{
    private $currentLocale = null;
    
    public function view($view, $data = array())
    {
        $menuNode = NodeHelper::getSystemNode('menu');
        $menuNodes = Node::where(array('parentId' => $menuNode->id))->get();
        
        $data["menuItems"] = $menuNodes;
        $data["locale"] = $this->currentLocale;
        $data["languages"] = Language::all();

        return view($view, $data);
    }
    
    public function setCurrentLocale($locale)
    {
        $this->currentLocale = $locale;
    }
}
