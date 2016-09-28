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

return [

    /**
     * Auth method for access to RestApi protocol
     * @see \oat\taoRestAPI\model\AuthenticationInterface
     */
    'authenticator' => '\oat\taoRestAPI\proxy\BasicAuthentication',
    
    /**
     * default definer for the format of the data
     * @see \oat\taoRestAPI\model\HttpDataFormatInterface
     */
    'encoder' => '\oat\taoRestAPI\model\v1\http\Request\DataFormat',
    
    /**
     * Adapter for requested data from different frameworks (Slim, ClearFw)
     * @see \oat\taoRestAPI\model\v1\http\Request\RouterAdapter
     */
    'routerAdapter' => '\oat\taoRestAPI\model\v1\http\Request\RouterAdapter\TaoRouterAdapter',
    
    /**
     * Adapter for data access (Array, Qti, Rdf ... types of the data storage)
     * @see \oat\taoRestAPI\model\DataStorageInterface
     */
    'storageAdapter' => '\oat\taoItemRestApi\model\v1\ItemRdfStorageAdapter',
    
    /**
     * Configurable identifier getter
     * 
     * #examples:
     * url $_GET param: ?uri={id} => ['type' => 'get', 'key' => 'uri'] // used by tao for Rdf models with clearfw
     * in url params: /url/{id} => ['type' => 'param', 'key' => 'id'] // used by phpunit test with Slim framework
     * in post: ['type' => '[post', 'key' => 'id'] // need realization, for example
     * 
     * @param string
     */
    'idRule' => [
        'type' => 'get',
        'key' => 'uri'
    ],
];
