<?php

use PHPUnit\Framework\TestCase;

class PartsTest extends TestCase
{
    public function testDisplayParts()
    {
        global $conn;
        $conn = $this->getMockBuilder(mysqli::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resultMock = $this->getMockBuilder(stdClass::class)
            ->addMethods(['num_rows', 'fetch_assoc'])
            ->getMock();

        $resultMock->expects($this->any())
            ->method('num_rows')
            ->willReturn(1);

        $resultMock->expects($this->any())
            ->method('fetch_assoc')
            ->willReturn(['name' => 'Part1', 'price' => 100, 'description' => 'Description1']);

        $conn->expects($this->any())
            ->method('query')
            ->willReturn($resultMock);

        ob_start();

        include 'parts.php';

        $output = ob_get_clean();

        $this->assertStringContainsString('<h3>Материнські плати</h3>', $output);
        $this->assertStringContainsString('<td>Part1</td><td>100 грн</td><td>Description1</td>', $output);
    }
}

class CollectionTest extends TestCase
{
    public function testSuccessfulCollection()
    {
        $_POST['processor'] = 'Processor1';
        $_POST['motherboard'] = 'Motherboard1';
        $_POST['ram'] = 'RAM1';
        $_POST['videoCard'] = 'VideoCard1';

        $_SESSION['username'] = 'testuser';
        $_SESSION['email'] = 'testuser@example.com';

        include 'collection.php';

        $this->assertStringContainsString('Дані успішно збережено', $GLOBALS['successMessage']);
    }
}

