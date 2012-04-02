<?php

/**
 * Copyright (c) 2012 Alchemy-fr
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 */

/**
 *
 * @author      Nicolas Le Goff
 * @author      Phraseanet team
 * @license     http://opensource.org/licenses/MIT MIT
 */
class ParserTest extends PHPUnit_Framework_TestCase
{

  public function testParse()
  {
    $b64 = $this->binaryToBase64(__DIR__ . '/smile.png');

    $tests = array(
        "data:image/png;base64," . $b64,
        "data:image/png;paramName=paramValue;base64," . $b64,
        "data:text/plain;charset=utf-8,%23%24%25",
        "data:application/vnd-xxx-query,select_vcount,fcol_from_fieldtable/local"
    );

    //#1
    $dataURI = DataURI\Parser::parse($tests[0]);
    $this->assertEquals('image/png', $dataURI->getMimeType());
    $this->assertTrue($dataURI->isBase64Encoded());
    $this->assertTrue(is_string($dataURI->getData()));
    $this->assertEquals(0, count($dataURI->getParameters()));

    //#2
    $dataURI = DataURI\Parser::parse($tests[1]);
    $this->assertEquals('image/png', $dataURI->getMimeType());
    $this->assertTrue($dataURI->isBase64Encoded());
    $this->assertTrue(is_string($dataURI->getData()));
    $this->assertEquals(1, count($dataURI->getParameters()));

    //#3
    $dataURI = DataURI\Parser::parse($tests[2]);
    $this->assertEquals('text/plain', $dataURI->getMimeType());
    $this->assertFalse($dataURI->isBase64Encoded());
    $this->assertEquals('%23%24%25', utf8_decode($dataURI->getData()));
    $this->assertEquals(1, count($dataURI->getParameters()));

    //#4
    $dataURI = DataURI\Parser::parse($tests[3]);
    $this->assertEquals('application/vnd-xxx-query', $dataURI->getMimeType());
    $this->assertFalse($dataURI->isBase64Encoded());
    $this->assertEquals('select_vcount,fcol_from_fieldtable/local', $dataURI->getData());
    $this->assertEquals(0, count($dataURI->getParameters()));
  }

  public function testInvalidDataException()
  {
    try
    {
      $invalidData = 'data:image/gif;base64,';
      DataURI\Parser::parse($invalidData);
      $this->fail('Should raised an \DataURI\Exception\InvalidData Exception');
    }
    catch (\DataURI\Exception\InvalidData $e)
    {
      
    }
  }
  
  public function testInvalidArgumentException()
  {
    try
    {
      $invalidData = 'lorem:image:test,datas';
      DataURI\Parser::parse($invalidData);
      $this->fail('Should raised an InvalidArgumentException Exception');
    }
    catch (\InvalidArgumentException $e)
    {
      
    }
  }
  
  private function binaryToBase64($file)
  {
    return base64_encode(file_get_contents($file));
  }

}