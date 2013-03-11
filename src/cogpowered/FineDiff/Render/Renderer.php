<?php

/**
* FINE granularity DIFF
*
* Computes a set of instructions to convert the content of
* one string into another.
*
* Originally created by Raymond Hill (github.com/gorhill/PHP-FineDiff), brought up
* to date by Cog Powered (github.com/cogpowered/FineDiff).
*
* Licensed under The MIT License
*
* Permission is hereby granted, free of charge, to any person obtaining a copy
* of this software and associated documentation files (the "Software"), to deal
* in the Software without restriction, including without limitation the rights
* to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the Software is
* furnished to do so, subject to the following conditions:
*
* The above copyright notice and this permission notice shall be included in
* all copies or substantial portions of the Software.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
* THE SOFTWARE.
*
* @copyright Copyright 2011 (c) Raymond Hill (http://raymondhill.net/blog/?p=441)
* @copyright Copyright 2013 (c) Robert Crowe (http://cogpowered.com)
* @link https://github.com/cogpowered/FineDiff
* @version 0.0.1
* @license MIT License (http://www.opensource.org/licenses/mit-license.php)
*/

namespace cogpowered\FineDiff\Render;

use cogpowered\FineDiff\Parser\OpcodesInterface;

abstract class Renderer implements RendererInterface
{
    public function process($from_text, OpcodesInterface $opcodes)
    {
        // Holds the generated string that is returned
        $output = '';

        $opcodes        = $opcodes->generate();
        $opcodes_len    = strlen($opcodes);
        $from_offset    = 0;
        $opcodes_offset = 0;

        while ($opcodes_offset < $opcodes_len) {

            $opcode = substr($opcodes, $opcodes_offset, 1);
            $opcodes_offset++;
            $n = intval(substr($opcodes, $opcodes_offset));

            if ($n) {
                $opcodes_offset += strlen(strval($n));
            } else {
                $n = 1;
            }

            if ($opcode === 'c') {
                // copy n characters from source
                $data = $this->callback('c', $from_text, $from_offset, $n);
                $from_offset += $n;
            } else if ($opcode === 'd') {
                // delete n characters from source
                $data = $this->callback('d', $from_text, $from_offset, $n);
                $from_offset += $n;
            } else /* if ( $opcode === 'i' ) */ {
                // insert n characters from opcodes
                $data = $this->callback('i', $opcodes, $opcodes_offset + 1, $n);
                $opcodes_offset += 1 + $n;
            }

            $output .= $data;
        }

        return $output;
    }
}