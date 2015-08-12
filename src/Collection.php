<?php
/**
 * @author @ct-jensschulze <jens.schulze@commercetools.de>
 */

namespace Commercetools\Commons;


use Commercetools\Commons\Json\NodeCollection;

class Collection extends JsonObject
{
    protected function __construct(NodeCollection $node = null)
    {
        if (is_null($node)) {
            $node = NodeCollection::of();
        }
        parent::__construct($node);
    }
}