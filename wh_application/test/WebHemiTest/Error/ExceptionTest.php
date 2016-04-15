<?php
/**
 *
 * WebHemi
 *
 * PHP version 5.6
 *
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://webhemi.gixx-web.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@gixx-web.com so we can send you a copy immediately.
 *
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2012 - 2016 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 *
 */

namespace WebHemiTest\Error;

use WebHemi\Error\Exception;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class ExceptionTest
 * @package WebHemiTest\Error
 */
class ExceptionTest extends TestCase
{
    /**
     * Test exception without any parameters
     */
    public function testExceptionWithNoParams()
    {
        $exception = new Exception();

        $this->assertEquals(500, $exception->getCode());
        $this->assertEquals('Internal Server Error', $exception->getMessage());
    }

    /**
     * Test exception with specific parameters
     */
    public function testExceptionWithParams()
    {
        $code = 123;
        $message = 'Hello World!';

        $exception = new Exception($message, $code);

        $this->assertEquals($code, $exception->getCode());
        $this->assertEquals($message, $exception->getMessage());
    }
}
