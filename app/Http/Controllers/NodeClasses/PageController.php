<?php

namespace App\Http\Controllers\NodeClasses;

use Marble\Admin\App\Helpers\NodeHelper;
use Marble\Admin\App\Models\Node;
use App\Http\Controllers\NodeClassController;

class PageController extends NodeClassController
{
    public function viewNode($id, $languageId, $param = null)
    {        
        $node = Node::find($id);

        return $this->view("nodeclass/page/view", ["node" => $node]);
    }
}
