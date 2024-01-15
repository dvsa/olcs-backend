<?php

declare(strict_types=1);

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Messaging\Message;

use Dvsa\Olcs\Api\Domain\CommandHandler\Messaging\Message\Create as CreateMessageHandler;
use Dvsa\Olcs\Api\Domain\Exception\BadRequestException;
use Dvsa\Olcs\Api\Domain\Repository\Conversation as ConversationRepo;
use Dvsa\Olcs\Api\Domain\Repository\Message as MessageRepo;
use Dvsa\Olcs\Api\Domain\Repository\MessageContent as MessageContentRepo;
use Dvsa\Olcs\Api\Entity\Messaging\MessagingContent;
use Dvsa\Olcs\Api\Entity\Messaging\MessagingConversation;
use Dvsa\Olcs\Api\Entity\Messaging\MessagingMessage;
use Dvsa\Olcs\Transfer\Command\Messaging\Message\Create as CreateMessageCommand;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

class Create extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new CreateMessageHandler();
        $this->mockRepo(ConversationRepo::class, ConversationRepo::class);
        $this->mockRepo(MessageRepo::class, MessageRepo::class);
        $this->mockRepo(MessageContentRepo::class, MessageContentRepo::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $data = [
            'conversation' => $conversationId = 1,
            'messageContent' => 'This is a test!',
        ];

        $command = CreateMessageCommand::create($data);

        $mockConversation = m::mock(MessagingConversation::class);
        $mockConversation->expects('getId')->andReturn(1);
        $mockConversation->expects('getIsClosed')->andReturn(0);
        $mockConversation->expects('getIsArchived')->andReturn(0);
        $this->repoMap[ConversationRepo::class]->expects('fetchById')->with($conversationId)->andReturn($mockConversation);
        $this->repoMap[MessageContentRepo::class]->expects('save')->with(m::type(MessagingContent::class));
        $this->repoMap[MessageRepo::class]->expects('save')->with(m::type(MessagingMessage::class));

        $result = $this->sut->handleCommand($command);

        $this->assertArrayHasKey('id', $result->toArray());
        $this->assertArrayHasKey('message', $result->toArray()['id']);
        $this->assertArrayHasKey('messageContent', $result->toArray()['id']);
        $this->assertArrayHasKey('messageConversation', $result->toArray()['id']);
        $this->assertArrayHasKey('messages', $result->toArray());
    }

    public function testCannotAddMessageToClosedConversation()
    {
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage(CreateMessageHandler::EXCEPTION_MESSAGE_UNABLE_TO_ADD_MESSAGE_TO_CLOSED_ARCHIVED_CONVERSATION);

        $data = [
            'conversation' => $conversationId = 1,
            'messageContent' => 'This is a test!',
        ];

        $command = CreateMessageCommand::create($data);

        $mockConversation = m::mock(MessagingConversation::class);
        $mockConversation->expects('getIsClosed')->andReturn(1);
        $this->repoMap[ConversationRepo::class]->expects('fetchById')->with($conversationId)->andReturn($mockConversation);

        $this->sut->handleCommand($command);
    }

    public function testCannotAddMessageToArchivedConversation()
    {
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage(CreateMessageHandler::EXCEPTION_MESSAGE_UNABLE_TO_ADD_MESSAGE_TO_CLOSED_ARCHIVED_CONVERSATION);

        $data = [
            'conversation' => $conversationId = 1,
            'messageContent' => 'This is a test!',
        ];

        $command = CreateMessageCommand::create($data);

        $mockConversation = m::mock(MessagingConversation::class);
        $mockConversation->expects('getIsClosed')->andReturn(0);
        $mockConversation->expects('getIsArchived')->andReturn(1);
        $this->repoMap[ConversationRepo::class]->expects('fetchById')->with($conversationId)->andReturn($mockConversation);

        $this->sut->handleCommand($command);
    }
}
