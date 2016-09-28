<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2016  (original work) Open Assessment Technologies SA;
 * 
 * @author Alexander Zagovorichev <zagovorichev@1pt.com>
 */

namespace oat\taoItemRestApi\model\v1;


use oat\taoQtiItem\model\ItemModel;
use oat\taoRestAPI\model\v1\StorageAdapter\RdfStorageAdapter;


class ItemRdfStorageAdapter extends RdfStorageAdapter
{
    /** @var  \taoItems_models_classes_ItemsService */
    protected $service;

    /**
     * Turn on creating resources without params
     * @var bool
     */
    protected $allowedDefaultResources = true;
    
    /**
     * Default properties for rdf of the Items
     * @var array
     */
    private $defaultProperties = [
        TAO_ITEM_MODEL_PROPERTY => ItemModel::MODEL_URI,
        RDF_TYPE => TAO_ITEM_CLASS,
    ];
    
    public function __construct()
    {
        parent::__construct();
        
        // default for item rdf prop
        $this->appendPropertiesValues($this->defaultProperties);
    }
    
    protected function getService()
    {
        return \taoItems_models_classes_ItemsService::singleton();
    }

    public function create()
    {
        $propertiesValues = $this->getPropertiesValues();
        
        $itemContent = null;
        if (isset($propertiesValues[TAO_ITEM_CONTENT_PROPERTY])) {
            $itemContent = $propertiesValues[TAO_ITEM_CONTENT_PROPERTY];
            $this->unsetPropertiesValue(TAO_ITEM_CONTENT_PROPERTY);
        }
        
        $itemUri = parent::create();
        
        $item = new \core_kernel_classes_Resource($itemUri);
        if (isset($itemContent)) {
            $this->service->setItemContent($item, $itemContent);
        } else {
            $this->service->deleteItemContent($item);
        }

        return $itemUri;
    }
}
