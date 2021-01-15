<?php

namespace SimpleSAML\XMLSecurity;

use DOMXPath;
use Exception;

/**
 * xmlseclibs.php
 *
 * Copyright (c) 2007-2017, Robert Richards <rrichards@cdatazone.org>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Robert Richards nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @copyright 2007-2017 Robert Richards <rrichards@cdatazone.org>
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 */

class XMLSecurityDSig extends \RobRichards\XMLSecLibs\XMLSecurityDSig
{
    /** @var DomXPath|null */
    private $xPathCtx = null;


    /**
     * @return bool
     * @throws Exception
     */
    public function validateReference()
    {
        $sigNode = $this->sigNode;
        $docElem = $sigNode->ownerDocument->documentElement;

        // enveloped signature, remove it
        if (!$docElem->isSameNode($sigNode)) {
            if ($sigNode->parentNode !== null) {
                $sigNode->parentNode->removeChild($sigNode);
            }
        }
        $xpath = $this->getXPathObj();
        $query = "./secdsig:SignedInfo[1]/secdsig:Reference";
        $nodeset = $xpath->query($query, $sigNode);
        if ($nodeset->length < 1) {
            throw new Exception("Reference nodes not found");
        }

        /* Initialize/reset the list of validated nodes. */
        $this->validatedNodes = [];

        foreach ($nodeset as $refNode) {
            if (!$this->processRefNode($refNode)) {
                /* Clear the list of validated nodes. */
                $this->validatedNodes = null;
                throw new Exception("Reference validation failed");
            }
        }
        return true;
    }


    /**
     * Returns the XPathObj or null if xPathCtx is set and sigNode is empty.
     *
     * @return DOMXPath|null
     */
    private function getXPathObj()
    {
        if (empty($this->xPathCtx) && ! empty($this->sigNode)) {
            $xpath = new DOMXPath($this->sigNode->ownerDocument);
            $xpath->registerNamespace('secdsig', self::XMLDSIGNS);
            $this->xPathCtx = $xpath;
        }
        return $this->xPathCtx;
    }


    /**
     * Reset the XPathObj to null
     */
    private function resetXPathObj()
    {
        $this->xPathCtx = null;
    }
}
