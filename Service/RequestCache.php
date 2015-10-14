<?php

namespace Publisher\Service;

/**
 * ExpressionEngine Publisher RequestCache Class
 *
 * @package     ExpressionEngine
 * @subpackage  Libraries
 * @category    Publisher
 * @author      Brian Litzinger
 * @copyright   Copyright (c) 2012, 2013 - Brian Litzinger
 * @link        http://boldminded.com/add-ons/publisher
 * @license
 *
 * Copyright (c) 2015. BoldMinded, LLC
 * All rights reserved.
 *
 * This source is commercial software. Use of this software requires a
 * site license for each domain it is used on. Use of this software or any
 * of its source code without express written permission in the form of
 * a purchased commercial or other license is prohibited.
 *
 * THIS CODE AND INFORMATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF ANY
 * KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND/OR FITNESS FOR A
 * PARTICULAR PURPOSE.
 *
 * As part of the license agreement for this software, all modifications
 * to this source must be submitted to the original author for review and
 * possible inclusion in future releases. No compensation will be provided
 * for patches, although where possible we will attribute each contribution
 * in file revision notes. Submitting such modifications constitutes
 * assignment of copyright to the original author (Brian Litzinger and
 * BoldMinded, LLC) for such modifications. If you do not wish to assign
 * copyright to the original author, your license to  use and modify this
 * source is null and void. Use of this software constitutes your agreement
 * to this clause.
 */

class RequestCache implements RequestCacheInterface
{
    const NAME = 'publisher:RequestCache';
    const DEFAULT_NAMESPACE = '__default__';
    
    /**
     * @var array
     */
    private $cache = array();

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $namespace = null)
    {
        if ($namespace) {
            $this->cache[$namespace][$key] = $value;
        } else {
            $this->cache[self::DEFAULT_NAMESPACE][$key] = $value;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function add($key, $value, $namespace = null)
    {
        if ($namespace) {
            if (!is_array($this->cache[$namespace][$key])) {
                $this->cache[$namespace][$key] = array();
            }
            $this->cache[$namespace][$key][] = $value;
        } else {
            if (!is_array($this->cache[self::DEFAULT_NAMESPACE][$key])) {
                $this->cache[self::DEFAULT_NAMESPACE][$key] = array();
            }
            $this->cache[self::DEFAULT_NAMESPACE][$key][] = $value;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $namespace = null)
    {
        if ($namespace) {
            if (isset($this->cache[$namespace][$key])) {
                // @todo add Logger trait
                return $this->cache[$namespace][$key];
            }
        } else {
            if (isset($this->cache[self::DEFAULT_NAMESPACE][$key])) {
                return $this->cache[self::DEFAULT_NAMESPACE][$key];
            }
        }

        return null;
    }
}
