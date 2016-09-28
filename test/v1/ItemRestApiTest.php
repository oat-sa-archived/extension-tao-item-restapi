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

namespace oat\taoItemRestApi\test\v1;


use core_kernel_classes_Resource;
use oat\taoRestAPI\model\v1\http\Request\RouterAdapter\TaoRouterAdapter;
use oat\taoRestAPI\test\TaoCurlRequest\RestTestCase;
use tao_helpers_Uri;
use tao_models_classes_ClassService;
use taoItems_models_classes_ItemsService;

class ItemRestApiTest extends RestTestCase
{
    /**
     * @var string
     */
    protected $uri = 'taoItemRestApi/v1/';

    /**
     * @var tao_models_classes_ClassService
     */
    protected $service = null;

    /**
     * Test items
     * 
     * @var array of the itemUris
     */
    private $items = [];
    
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        \common_ext_ExtensionsManager::singleton()->getExtensionById('taoItemRestApi');
        $this->service = taoItems_models_classes_ItemsService::singleton();
    }

    public function tearDown()
    {
        parent::tearDown();
        foreach ($this->items as $itemUri) {
            $this->service->deleteResource(new core_kernel_classes_Resource($itemUri));
        }
    }

    /** get list and crud tests */
    public function serviceProvider()
    {
        return [[$this->uri, $this->service->getRootClass()]];
    }

    /**
     * Test Http POST, GET, DELETE requests
     */
    public function testPostAndGetAndDelete()
    {
        // create
        $data = $this->checkPost($this->uri);

        $this->items[] = $uriResource = $data[0]['uri'];
        $resource = new core_kernel_classes_Resource(tao_helpers_Uri::decode($uriResource));
        $this->assertTrue($resource->exists(), 'Object should be exists');

        // get
        $this->checkGet($this->uri . '?uri=' . urlencode($uriResource), $this->service->getRootClass());

        // delete
        $this->checkDelete($this->uri . '?uri=' . urlencode($uriResource));
        $this->assertFalse($resource->exists(), 'Object should be deleted');

        // check deletion from storage
        $resource = new core_kernel_classes_Resource(tao_helpers_Uri::decode($uriResource));
        $this->assertFalse($resource->exists(), 'Object should be deleted');
    }

    /**
     * Replace the source with new data
     * old resource data will be deleted
     */
    public function testPut()
    {
        $label1 = 'label for test';
        $comment = 'comment1';
        
        // create
        $data = $this->checkPost($this->uri, [], [RDFS_COMMENT => $comment, RDFS_LABEL => $label1]);

        // check
        $this->items[] = $uriResource = $data[0]['uri'];
        $resource = new core_kernel_classes_Resource(tao_helpers_Uri::decode($uriResource));
        $this->assertSame($label1, $resource->getLabel());
        $this->assertSame($comment, $resource->getComment());
        
        // put
        $test_label = 'changed Label for test';
        $putData = [RDFS_LABEL => $test_label];
        $putJson = json_encode($putData);
        
        $data = $this->checkPut($this->uri . '?uri=' . urlencode($uriResource), [
            'Content-Type: application/json', 
            'Content-Length: ' . mb_strlen($putJson),
            'Content-MD5: ' . md5($putJson),
        ], $putJson);

        $uriResourcePut = $data[0]['uri'];
        $this->assertEquals($uriResource, $uriResourcePut);

        // reload
        $resource = new core_kernel_classes_Resource(tao_helpers_Uri::decode($uriResourcePut));
        // replaced
        $this->assertSame($test_label, $resource->getLabel());
        // not set, therefor deleted
        $this->assertSame('', $resource->getComment());
        
        // delete
        $this->service->deleteResource($resource);
    }

    /**
     * update the source
     * only determined data will be updated
     */
    public function testPatch()
    {
        // create
        $label1 = 'label for test';
        $comment = 'comment1';
        $data = $this->checkPost($this->uri, [], [RDFS_COMMENT => $comment, RDFS_LABEL => $label1]);
        
        // check
        $this->items[] = $uriResource = $data[0]['uri'];
        $resource = new core_kernel_classes_Resource(tao_helpers_Uri::decode($uriResource));
        $this->assertSame($label1, $resource->getLabel());
        $this->assertSame($comment, $resource->getComment());

        // patch
        $test_label = 'changed Label for test';
        $patchData = [RDFS_LABEL => $test_label];
        $patchJson = json_encode($patchData);

        /*
         * ClearFw doesn't implement PATCH method
         * todo on the slim switch to patch
         */
          /*$data = $this->checkPatch($this->uri . '?uri=' . urlencode($uriResource), [
            'Content-Type: application/json',
            'Content-Length: ' . mb_strlen($putJson),
            'Content-MD5: ' . md5($putJson),
            ], $putJson);*/

        // for PATCH we can send put with the parameter _method = 'patch'
        $data = $this->checkPut($this->uri . '?' . TaoRouterAdapter::HTTP_METHOD  . '=' . TaoRouterAdapter::HTTP_PATCH . '&uri=' . urlencode($uriResource), [
            'Content-Type: application/json',
            'Content-Length: ' . mb_strlen($patchJson),
            'Content-MD5: ' . md5($patchJson),
        ], $patchJson);

        $uriResourcePatched = $data[0]['uri'];
        $this->assertEquals($uriResource, $uriResourcePatched);

        // reload
        $resource = new core_kernel_classes_Resource(tao_helpers_Uri::decode($uriResourcePatched));
        $this->assertSame($test_label, $resource->getLabel());
        $this->assertSame($comment, $resource->getComment());

        // delete
        $this->service->deleteResource($resource);
    }
}
